<?php
	############################################################################
	## Initialize script and assert session validity						  ##
	############################################################################

	ini_set('display_errors', 'On');

	include_once '../../model/admin/Clocker_Admin.php';
	include_once '../../session.php';

	session_start();

	assert_session($type = 'administrators');
	assert_container($container = $admin_url['editor']);

	############################################################################



	############################################################################
	## Remove specified entries by aid										  ##
	############################################################################

	isset($_POST['employee']) or die('stahp');

	$db = new Clock_Admin();

	if(isset($_POST['data']))
		foreach($_POST['data'] as $aid)
			$db->remove_entry($_POST['employee'], $aid);

	############################################################################



	############################################################################
	## Redraw editor, commit and close database, terminate script			  ##
	############################################################################

	$editor = $db->draw_editor($_POST['employee']);

	$db->commit();
	$db->close();

	echo $editor;

	exit();

	############################################################################
?>
