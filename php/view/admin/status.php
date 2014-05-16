<?php

	############################################################################
	## Initialize script and assert session validity						  ##
	############################################################################

	ini_set('display_errors', 'On');

	include_once '../../model/admin/Clocker_Admin.php';
	include_once '../../session.php';

	session_start();

	assert_session($type = 'administrators');

	############################################################################



	############################################################################

	$db = new Clock_Admin();

	# Retrieve list of clocked-in employees
	$clocked = $db->clocked_in_employees();
	$time = Array();
	$content = NULL;

	# Function name is kind of a misnomer at this point...
	# get_last_action retrieves all information relating to employee's last action
	#  which is used to formulate display.
	foreach($clocked as $employee)
		$time[] = $db->get_last_action($employee);

	for($i = 0; $i < sizeof($clocked); ++$i)
		$content .= "<p class='text'><b>" . $clocked[$i] . "</b> clocked in since " . $time[$i]['time'] . "</p>";

	if(!$content)
		$content = "<p class='text'>Apparently nobody's clocked in.</p>";
	
	############################################################################



	############################################################################

	$db->close();

	echo $content;

	exit();
	
	############################################################################
?>
