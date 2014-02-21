<?php
	############################################################################
	## Initialize script and assert session validity						  ##
	############################################################################

	ini_set('display_errors', 'On');

	include_once '../../model/employee/Clocker_Employee.php';
	include_once '../../session.php';

    session_start();

	set_container();
	assert_session($type = 'employees');

	############################################################################



	############################################################################
	## Prepare interface for user											  ##
	##------------------------------------------------------------------------##
	## Interface consists of button for clocking in or out, button for		  ##
	## displaying work period information, button for logging out.			  ##
	############################################################################

    $db = new Clock_Employee();
	$user = $_SESSION["sess_username"];

	# Used for dynamic labeling of interface buttons
    $last = $db->get_last_action($user);

	if($last['action'] == 'clock_in')
	{
		$clocked = 'in';
		$not = 'out';
	}
	else
	{
		$clocked = 'out';
		$not = 'in';
	}

    $menu = "
		<html>
			<head>
				<link rel='stylesheet' href='//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css'>
				<link rel='stylesheet' href='//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-theme.min.css'>
				<link rel='stylesheet' type='text/css' href='../../../css/clocker.css'>
				<script src='//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js'></script>
				<script src='../../../js/jquery.js'></script>
				<script src='../../../js/employee/actions.js'></script>
				<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
				<title>Home Page</title>
			</head>
			<div class='page-header'><h2 style='text-align:center'>Texas State University<br><small>Department of Computer Science</small></h2></div>
			<div id='outer'>
				<p class='text'>You ($user) are currently clocked <b>$clocked</b>.</p>
				<form class='form-signin' method='post' action='../../control/logout.php'>
					<button class='btn btn-lg btn-primary btn-block' type='button' onclick='clock()'>Clock $not</button>
					<button class='btn btn-lg btn-primary btn-block' type='button' onclick='info()'>Info</button>
					<button class='btn btn-lg btn-primary btn-block' type='submit'>Log Out</button>
				</form>
			</div>
		</html>
		";

	############################################################################



	############################################################################
	## Draw menu, close database											  ##
	############################################################################

	$db->close();

	echo $menu;

	exit();

	############################################################################
?>
