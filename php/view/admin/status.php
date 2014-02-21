<?php
################################################################################
	ini_set('display_errors', 'On');

	include_once '../../model/admin/Clocker_Admin.php';
	include_once '../../session.php';

	session_start();

	assert_session($type = 'administrators');
################################################################################



################################################################################
	$db = new Clock_Admin();

	$clocked = $db->clocked_in_employees();
	$time = Array();
	$content = NULL;

	foreach($clocked as $employee)
		$time[] = $db->get_last_action($employee);

	for($i = 0; $i < sizeof($clocked); ++$i)
		$content .= "<p class='text'><b>" . $clocked[$i] . "</b> clocked in since " . $time[$i]['time'] . "</p>";

	if(!$content)
		$content = "<p class='text'>Apparently nobody's clocked in.</p>";
################################################################################



################################################################################
	$db->close();

	echo $content;

	exit();
################################################################################
?>
