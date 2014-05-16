<?php
	# Gets, formats, displays information about employee:
	#  Clock status
	#  Time worked this pay period
	#  Timestamps of clock ins and clock outs

	############################################################################
	## Initialize script and assert session validity						  ##
	############################################################################

	ini_set('display_errors', 'On');

	include_once '../../model/employee/Clocker_Employee.php';
	include_once '../../session.php';

	session_start();

	assert_session($type = 'employees');

	############################################################################



	############################################################################
	## Clock user in or out, depending on their current status				  ##	
	############################################################################

	$db = new Clock_Employee();
	$netid = $_SESSION['sess_username'];

	# Formulate display
	$time = $db->format_time_worked($db->get_time_worked($netid));
	# Draws user's clocked periods.. I think
	$times = $db->draw_table(); 
	$last = $db->get_last_action($netid);
	$clocked = $last['action'] == 'clock_in' ? 'in' : 'out';

	$result = "
		<p class='text'>You ($netid) are currently clocked <b>$clocked</b>.</p>
		<p class='text'>$time</p>
		<form class='form-signin' method='post' action='../../control/logout.php'>
			<button class='btn btn-lg btn-primary btn-block' type='button' onclick='dash()'>Return</button>
			<button class='btn btn-lg btn-primary btn-block' type='submit'>Log Out</button>
		</form>
		<br>
		<p class='text'>$times</p>
		";

	############################################################################



	############################################################################
	## Close database connection and draw interface							  ##
	############################################################################
	
	$db->close();

	echo $result;

	exit();

	############################################################################
?>
