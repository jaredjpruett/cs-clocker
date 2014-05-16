<?php
	############################################################################
	## Initialize script and assert session validity						  ##
	############################################################################

	include_once '../../model/admin/Clocker_Admin.php';
	include_once '../../session.php';

	session_start();

	assert_session($type = 'administrators');
	
	############################################################################



	############################################################################
	## All this script does is call draw_editor, because design issues		  ##
	############################################################################
	
	$db = new Clock_Admin();
	
	echo $db->draw_editor($_POST['employee']);
	
	$db->close();

	exit();

	############################################################################
?>
