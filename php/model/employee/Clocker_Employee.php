<?php
	ini_set('display_errors', 'On');

	include_once '../../model/Clocker.php';

	class Clock_Employee extends Clock
	{
		public function __construct()
		{
			parent::__construct();
		}

		# Update user's clock status
		# Independent of input data; queries database to ascertain user's last action, inserts inverse (clock_in -> clock_out, clock_out -> clock_in)
		public function clock_action($username) # Remove argument, looks like we're not using it anymore
		{
			$name   = strtolower(preg_replace('/[^a-z0-9-]+/i', '_', $_SESSION['sess_username']));
			$last   = $this->get_last_action($name);
			$action = $last['action'] == 'clock_in' ? 'clock_out' : 'clock_in';

			$this->query("INSERT INTO $this->table (name, time, action, ip) VALUES ('$name', NOW(), '$action', '$this->address')");

			return $action;
		}

		# Formulates 'table' consisting of in and out times for each period the user has clocked
		public function draw_table() # Give less terrible name
		{
			$query  = $this->query("SELECT time FROM $this->table WHERE name = '$this->username' ORDER BY aid, time");
			$data   = Array();
			$table  = NULL;

			while($result = mysqli_fetch_array($query))
				$data[] = $result['time'];

			$size = sizeof($data) - sizeof($data) % 2;

			for($i = 0; $i < $size; $i += 2)
			{
				$in		= new DateTime($data[$i]);
				$out	= new DateTime($data[$i + 1]);

				$sec1	= $in->format('s');
				$min1	= $in->format('i');
				$hour1	= $in->format('h');
				$month1	= $in->format('m');
				$day1	= $in->format('d');
				$year1	= $in->format('Y');

				$sec2	= $out->format('s');
				$min2	= $out->format('i');
				$hour2	= $out->format('h');
				$month2	= $out->format('m');
				$day2	= $out->format('d');
				$year2	= $out->format('Y');

				$table .= "Clocked in on $year1-$day1-$month1 at $hour1:$min1:$sec1 | Clocked out on $year2-$day2-$month2 at $hour2:$min2:$sec2<br>";
			}

			return $table;
		}
	}
?>
