<?php
	############################################################################
	## Initialize script and assert session validity						  ##
	############################################################################

	include_once '../../model/admin/Clocker_Admin.php';
	include_once '../../session.php';

	session_start();

	set_container();
	assert_session($type = 'administrators');

	############################################################################



	############################################################################
	# Draw editing interface												  ##
	#-------------------------------------------------------------------------##
	# Div 'content' is drawn/redrawn by database class' draw_editor method.   ##
	# Once page is drawn, only this div's contents will be changed by actions ##
	# which redraw the editor by recalling said function.					  ##
	############################################################################

	$db = new Clock_Admin();
	$user = $_SESSION['sess_username'];

	# Used to select employee to be viewed and edited
	$select = $db->draw_employees_list($id = 'employees');
	# editor.php will only be called when drawing the page for the first time
	# On first draw, no employee should be selected, hence NULL
	# This will show the entirity of the table
	$editor = $db->draw_editor($employee = NULL);

	$page = "
		<html>
			<head>
				<link rel='stylesheet' type='text/css' href='http://cs.txstate.edu/~jaredp/Clocker/css/editor.css'>
				<script src='../../../js/jquery.js'></script>
				<script src='../../../js/admin/editor/tools.js'></script>
				<script src='../../../js/admin/editor/edits.js'></script>
				<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
				<title>Control Panel</title>
			</head>
			<div class='page-header'><h2 style='text-align:center'>Control Panel<br><small>Logged in as $user</small></h2></div>
			<div class='list'>$select</div>
			<div class='outer' id='content'>$editor</div>
			<script src='../../../js/admin/editor/abomination.js'></script>
		</html>
		";
	
	############################################################################



	############################################################################
	## Draw editor, close database connection								  ##
	############################################################################

	$db->close();

	echo $page;

	exit();

	############################################################################
?>
