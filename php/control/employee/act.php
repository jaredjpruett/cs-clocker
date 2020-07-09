<?php
	############################################################################
	## Initialize script and assert session validity						  ##
	############################################################################

	ini_set('display_errors', 'On');

	include_once '../../model/employee/Clocker_Employee.php';
	include_once '../../session.php';

	session_start();

	assert_session($type = 'employees');
	assert_container($container = $employee_url['menu']);

	############################################################################



	############################################################################
	## Clock user in or out, depending on their current status				  ##
	############################################################################

	$db = new Clock_Employee();

	$user = $_SESSION['sess_username'];
	# clock_action function toggles clock status
	$action = $db->clock_action($user) == 'clock_in' ? 'in' : 'out';
	$time = $db->get_time_worked($user);
	$time = $db->format_time_worked($time);
	
	############################################################################



	############################################################################
	## Draw result, commit and close database, terminate script				  ##
	############################################################################

	$result = "<p class='text'>Employee '$user' has been clocked <b>$action</b>.</p>
		<p class='text'>$time</p>
		<script>function gotologin() { window.location.replace('../../../index.html'); }</script>
		<form class='form-signin' method='post' action='../../control/logout.php'>
		<button class='btn btn-lg btn-primary btn-block' type='button' onclick='dash()'>Return to Dash</button>
		<button class='btn btn-lg btn-primary btn-block' type='submit'>Log Out</button>";

	$db->commit();
	$db->close();

	echo $result;

	exit();

	############################################################################
?>
