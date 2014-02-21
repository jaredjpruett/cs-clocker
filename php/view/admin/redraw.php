<?php
	include_once '../../model/admin/Clocker_Admin.php';
	include_once '../../session.php';

	session_start();

	assert_session($type = 'administrators');

	$db = new Clock_Admin();
	
	echo $db->draw_editor($_POST['employee']);
	
	$db->close();

	exit();
?>
