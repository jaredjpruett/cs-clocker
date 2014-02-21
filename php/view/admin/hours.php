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
	## Prepare display of total time worked this period by each employee	  ##
	##------------------------------------------------------------------------##
	## If an employee has no time worked this period, they are not displayed. ##
	## If no employee has time worked this period, a default is displayed.	  ##
	############################################################################

	$db = new Clock_Admin();

	$employees = $_SESSION['employees'];
	$size = sizeof($employees);
	$periods = NULL;

	for($i = 0; $i < $size; ++$i)
	{
		# Retreive time worked in seconds by employee
		$seconds = $db->get_time_worked($employees[$i]);

		# Format line of HTML block with assitance of format_time_worked function
		# Gross and ungainly. Ew.
		if($seconds)
			$periods .= "<p class='text'><b>" . $employees[$i] . "</b> - " . $db->format_time_worked($seconds) . "</p>";
	}

	if($periods == NULL)
		$periods = "<p class='text'>There are no hours logged for this period.</p>";

	############################################################################



	############################################################################
	## Redraw editor, commit and close database								  ##
	############################################################################

	echo $periods;

	$db->close();

	exit();

	############################################################################

?>
