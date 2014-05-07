<?php
	# You'll notice that the software pattern isn't really MVC
	# I just used those names to give myself some structural idea
	class Clock extends mysqli
	{
		var $username;
		var $address;
		var $table;

		public function __construct()
		{
			parent::__construct($_SESSION['host'], $_SESSION['netid'], $_SESSION['pass'], $_SESSION['database'], $_SESSION['port']);

			if(mysqli_connect_error()) die ("Connection error: (" . mysqli_connect_errno() . ") " . mysqli_connect_error());

			$this->username = $_SESSION['sess_username'];
			$this->address = $_SESSION['address'];

			# Table name is formulated using current time to automate pay period handling
			# Two pay periods per month; first for days 1 - 15, second for >15
			$period = date('d') <= 15 ? 1 : 2;
			# Table name format is Actions_YY_MM_pP
			$this->table = "Actions_" . date('y') . "_" . date('m') . "_p$period";

			$this->query("USE " . $_SESSION['database']);
			$this->query("CREATE TABLE IF NOT EXISTS $this->table (
				aid INTEGER NOT NULL AUTO_INCREMENT, name TEXT NOT NULL, time TIMESTAMP NOT NULL,
				action ENUM('clock_in', 'clock_out'), ip TEXT NOT NULL,
				editor TEXT, editor_ip TEXT, edit_time TEXT,
				PRIMARY KEY (aid))");

			filter_var($this->address, FILTER_VALIDATE_IP) or die("$this->address appears to be an invalid IP address.<br>");
		}
		
		public function get_last_action($name) # trash
		{
			$result = $this->query("SELECT * FROM $this->table WHERE name = '$name' ORDER BY time, aid");
			$action = NULL;

			while($sentry = mysqli_fetch_array($result))
				$action = $sentry;

			return $action;
		}

		public function get_last_action2($name)
		{
			$last = $this->query("SELECT MAX(time) FROM $this->table WHERE name = '$name' ORDER BY aid");
			$last = $last['time'];

			return $last;
		}

		# Returns the time elapsed in a given period
		# Will manually return -1 if in > out since delta function yields absolute values
		public function time_delta($in, $out)
		{
			$in = new DateTime($in);
			$out = new DateTime($out);

			if($in > $out) return -1;

			$delta = $in->diff($out);

			$seconds = $delta->days * 86400;
			$seconds += $delta->h * 3600;
			$seconds += $delta->i * 60;
			$seconds += $delta->s;

			return $seconds;
		}

		# Returns total seconds worked by an employee
		public function get_time_worked($name)
		{
			$result = $this->query("SELECT time FROM $this->table WHERE name = '$name' ORDER BY time, aid");
			$times = Array();
			$time = 0;

			while($r = mysqli_fetch_array($result))
				$times[] = $r['time'];

			if(!$times) return NULL;

			$size = sizeof($times) % 2 == 0 ? sizeof($times) : sizeof($times) - 1;

			for($i = 0; $i < $size; $i += 2)
				$time += $this->time_delta($times[$i], $times[$i + 1]);

			return $time;
		}

		# Receives a total in seconds as $time
		# Calculates hours, minutes, seconds and formats a pretty little string with results
		public function format_time_worked($time)
		{
			if(!$time) return NULL;

			$hours		= (int)($time / 3600);
			$minutes	= (int)($time % 3600 / 60);
			$seconds	= (int)($time % 60);

			$worked		= "Time worked this pay period:<br>$hours hours, $minutes minutes, $seconds seconds";

			return $worked;
		}
	}
?>
