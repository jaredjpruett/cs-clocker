<?php	
	############################################################################
	## Initialize script and assert session validity						  ##
	############################################################################

	ini_set('display_errors', 'On');

	include_once '../../session.php';

    session_start();

	assert_session($type = 'administrators');
	
	############################################################################



	############################################################################
	## Draw and display home menu.											  ##
	##------------------------------------------------------------------------##
	## Uses Javascript function show_menu to draw menu.						  ##
	## Consigned to JS function due to use in multiple places.				  ##
	## Includes button to enter edit mode, redirecting to edit page; button	  ##
	## to show hours worked by all employees this period; button to show	  ##
	## employees currently clocked in; button to log out.					  ##
	############################################################################

    echo "<html>
		<head>
		<link rel='stylesheet' href='//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css'>
		<link rel='stylesheet' href='//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-theme.min.css'>
		<link rel='stylesheet' type='text/css' href='../../../css/clocker.css'>
		<script src='//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js'></script>
		<script src='../../../js/jquery.js'></script>
		<script src='../../../js/admin/menu/menu.js'></script>
		<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
		<title>Administrative Blah</title>
		</head>
		<div class='page-header'><h2 style='text-align:center'>Texas State University<br><small>Department of Computer Science</small></h2></div>
		<div id='content'>
		<script>show_dash();</script>
		</div>
		</html>";

	exit();

	############################################################################
?>
