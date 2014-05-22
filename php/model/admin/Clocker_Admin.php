<?php
	include_once '../../model/Clocker.php';

	class Clock_Admin extends Clock
	{
		public function __construct()
		{
			parent::__construct();
		}

		############################################################################################
		## Methods for updating the database													  ##
		##----------------------------------------------------------------------------------------##
        ## Input data is scrubbed and validated before methods are invoked                        ##
		############################################################################################

		public function update_entry($id, $name, $time, $action)
		{
			$this->query("UPDATE $this->table SET name='$name', time='$time', action='$action', editor='$this->username', editor_ip='$this->address', edit_time=NOW() WHERE aid='$id'");
		}

		public function add_entry($name, $time, $action)
		{
			$this->query("INSERT INTO $this->table (name, time, action, editor, editor_ip, edit_time) VALUES ('$name', '$time', '$action', '$this->username', '$this->address', NOW())");
		}

		public function remove_entry($netid, $aid)
		{
			$this->query("DELETE FROM $this->table WHERE aid = $aid and name = '$netid'");
		}

		############################################################################################



		############################################################################################
		## Methods for accessing the database and drawing the web interface						  ##
		############################################################################################

		# Formulate array of employees currently clocked in
		public function clocked_in_employees()
		{
			$in = array();

			foreach($_SESSION['employees'] as $employee)
			{
				$result = $this->get_last_action($employee);

				# If last action of employee in database is 'clock_in', guess what
				if($result['action'] == 'clock_in')
					$in[] = $employee;
			}

			return $in;
		}

		# Draw HTML select element with list of employees returned from LDAP query and saved in session variable
		# Argument passed defines id of drawn element; defaults to 'list'
		public function draw_employees_list($id = 'list')
		{
			$select = "<select id='$id'>";

			foreach($_SESSION['employees'] as $employee)
				$select .= "<option value='$employee'>$employee</option>";

			$select .= "</select>";

			return $select;
		}

		# The Beast Jesus Christ please kill it
		# If argument is passed, editor is editable; else it's simply an overview of all employee actions
		public function draw_editor($employee)
		{
			# If argument is passed (an employee is selected), show only the information for that employee
			$where = $employee ? "WHERE name = '$employee'" : NULL;
			$query = $this->query("SELECT * FROM $this->table $where ORDER BY time, aid");

			$display = "
				<form type='submit' action='../../control/logout.php'>
					<fieldset>
				";

			# Formulate the editor; each iteration is a new period
			# $num is used to assign ids to elements
			# ids are used by Javascript to formulate an AJAX call
			# Submitted values are, of course, validated server-side
			for($num = 0; $row = mysqli_fetch_array($query); $num++)
			{
				$aid		= $row['aid'];
				$name		= $row['name'];
				$time		= $row['time'];
				$action		= $row['action'];
				$ip			= $row['ip'];
				$editor		= $row['editor'];
				$editor_ip	= $row['editor_ip'];
				$edit_time	= $row['edit_time'];

				# Checkboxes are only displayed if an employee is selected
				# Furthermore, only one checkbox per period is displayed
				$checkbox	= $employee && $num % 2 == 0	? "<input id='c$num' type='checkbox'>"		: "&nbsp;";
				# Each period consists of two rows; each row is either upper or lower
				$class		= $employee && $num % 2 == 1	? "lower"									: "upper";
				# All editing and posts are disabled if an employee is not selected
				$disabled	= $employee						? NULL										: "disabled";

				$display .= "
					<p class='$class'>
						<label>$aid</label>
						<a id='i$num' value='$num' hidden>$aid</a>
						<span class='checkbox'>$checkbox</span>
						<input class='textbox' id='n$num' type='text' value='$name' disabled readonly>
						<input class='textbox' id='t$num' type='text' value='$time' $disabled maxlength='19'>
						<input class='textbox' id='a$num' type='text' value='$action' disabled readonly>
						<input class='textbox' type='text' value='$ip' disabled readonly>
						<input class='textbox' type='text' value='$editor' disabled readonly>
						<input class='textbox' type='text' value='$editor_ip' disabled readonly>
						<input class='textbox' type='text' value='$edit_time' disabled readonly>
					</p>
					";
			}

			# Draw our buttons
			$display .= "
				<div id='insert'></div>
				<br>
				<div id='buttons'>
					<input type='button' value='Update' id='update' onclick='commit_changes($num)'>
					<input type='button' value='Remove selected' id='remove' onclick='remove_selected($num)'>
					<input type='button' value='Add period' id='addper' onclick='period_add($num)'>
					<input type='button' value='Add action' id='addact' onclick='action_add($num)'>
					<input type='submit' value='Log out' id='logout'>
				</div>
				</fieldset>
				</form>
				";

			return $display;
		}

		############################################################################################



		############################################################################################
		## Methods for asserting validity of posted data										  ##
		##----------------------------------------------------------------------------------------##
		## assert_employee receives string $netid containing netid of employee being edited		  ##
		## assert_latest receives datetime strings array $times, datetime string $last			  ##
		##	Note: $last is a singular datetime string											  ##
		## assert_timestamp receives datetime strings array $times								  ##
		## assert_datetime receives datetime strings array $times								  ##
		## assert_positive receives datetime strings array $times								  ##
		## assert_periods receives datetime strings arrays $times1 and $times2					  ##
		##	Note: both arguments may or may not be the same object								  ##
		############################################################################################

		# Assert posted NetID is on the list of employees
		public function assert_employee($netid)
		{
			return in_array($netid, $_SESSION['employees']);
		}

		# Assert a given time $last is the latest of any times supplied to $times array
		# $last may or may not be in $times list, should probably change that
		# $times is array of datetime strings; $last is a datetime string
		public function assert_latest($times, $last)
		{
			if($times)
				foreach($times as $time)
					if($this->time_delta($time, $last) < 0)
						return false;

			return true;
		}

		# Assert posted datetime strings are valid and conform to our established standard
		# $times is array of datetime strings
		public function assert_timestamp($times)
		{
			# Timestamp string must be of format "iiii-ii-ii ii:ii:ii" where 'i' is a decimal value
			# Valid datetimes are asserted separately
			$regex = "/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/";

			foreach($times as $time)
				if(preg_match($regex, $time) == false)
					return false;

			return true;
		}

		# Assert posted datetime string represents a possible datetime
		public function assert_datetime($times)
		{
			foreach($times as $time)
			{
				# Ugly manual string extraction
				$hour   = substr($time, 11, 2);
				$minute = substr($time, 14, 2);
				$second = substr($time, 17, 2);

				# Assert 0 <= hours < 24, 0 <= minutes < 60, 0 <= seconds < 60
				# Anything else is obviously not an actual time
				if($hour < 0 || $hour > 24 || $minute < 0 || $minute > 59 || $second < 0 || $second > 59)
					return false;

				# More ugly manual string extraction
				$month  = substr($time, 5, 2);
				$day    = substr($time, 8, 2);
				$year   = substr($time, 0, 4);

				# Assert that month/day/year combination is a possible date
				# Ex: February 29, 2014 is not a possible date
				if(checkdate($month, $day, $year) == false)
					return false;
			}

			return true;
		}

		# Assert each posted period (one clock in, one clock out) results in a positive time
		public function assert_positive($times)
		{
			# If size of times array is uneven, last action is clock in
			# Should not and cannot be compared with a clock out; validate elsewhere
			$periods = sizeof($times) - sizeof($times) % 2;

			for($i = 0; $i < $periods; $i += 2)
				if($this->time_delta($times[$i], $times[$i + 1]) <= 0)
					return false;

			return true;
		}

		# Assert each posted period does not overlap in any way with another posted period
		public function assert_periods($times1, $times2)
		{
			# If array size is uneven, user is clocked in. Seperate asserts will be executed for this action.
			$size1 = sizeof($times1) - sizeof($times1) % 2;
			$size2 = sizeof($times2) - sizeof($times2) % 2;

			# Check every period against every other period
			for($i = 0; $i < $size1; $i += 2)
				for($j = 0; $j < $size2; $j += 2)
					if($this->check_overlap($times1[$i], $times1[$i + 1], $times2[$j], $times2[$j + 1]))
						return false;

			return true;
		}

		############################################################################################



		############################################################################################
		## Auxiliary functions																	  ##
		############################################################################################

		# Determine if given in and out times overlap with established in and out times
		public function check_overlap($ein, $eout, $in, $out)
		{
			# Convert received timestamp strings into DateTime objects
			# ein/eout => established in/out; a period already known to not overlap previously checked periods
			$ein    = new DateTime($ein);
			$eout   = new DateTime($eout);
			$in     = new DateTime($in);
			$out    = new DateTime($out);

			# If in happened before ein, so must have out; else overlap
			if($in < $ein)
				if($out < $ein)
					return false;
				else
					return true;

			# If in happened after ein, it must have happened after eout; else overlap
			if($in > $ein)
				if($in > $eout)
					return false;
				else
					return true;

			return false;
		}

		############################################################################################
	}
?>
