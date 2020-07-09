<?php
	ini_set('display_errors', 'On');

	# to do: move path arrays into paths.php
    #        rename config.php to credentials.php

	# Using string literal instead of PHP_SELF because I'm guessing that's somehow safer
	$base = "/~jaredp/Clocker/";

	$url = array (
		"Clock" => "$base/php/model/Clocker.php",
		"LDAP" => "$base/php/model/LDAP.php",

		"login" => "$base/control/login.php",
		"logout" => "$base/control/logout.php",
		"session" => "$base/session.php",
	);

	$admin_url = array (
		"Clock" => "$base/php/model/admin/Clocker_Admin.php",
		"LDAP" => "$base/php/model/admin/LDAP_Admin.php",

		"hours" => "$base/php/control/admin/hours.php",
		"logged" => "$base/php/control/admin/logged.php",
		"remove" => "$base/php/control/admin/remove.php",
		"update" => "$base/php/control/admin/update.php",

		"menu" => "$base/php/view/admin/menu.php",
		"editor" => "$base/php/view/admin/editor.php",
		"redraw" => "$base/php/view/admin/redraw.php",
	);

	$employee_url = array (
		"Clock" => "$base/php/model/employee/Clocker_Employee.php",
		"LDAP" => "$base/php/model/employee/LDAP_Employee.php",

		"act" => "$base/php/control/employee/act.php",

		"menu" => "$base/php/view/employee/menu.php",
		"info" => "$base/php/view/employee/info.php",
	);

	# Receives session type [admin or employee] as argument from invoking script
	# Session type is used to enforce access control
	function assert_session($type)
	{
		# Assert that session user id is set
		if(!isset($_SESSION['sess_user_id']) || trim($_SESSION['sess_user_id']) == '')
		{
			session_destroy();

			header("location: ../../../index.html");
		}

		# Assert that user invoking script is a member of the appropriate group (employee or admin)
		in_array($_SESSION['sess_username'], $_SESSION[$type]) or destroy("session user group mismatch");
		# Depending on which login was posted, type is set to either employees or administrators
		# Admins logged in to employee area will have an employee session type
		# Each script requires either an employee or admin session; admin can't manually navigate to admin section if logged into employee session
		# All that is to say, this helps combat shenanigans
		$_SESSION['type'] == $type or destroy("session type mismatch");
	}

	# Certain pages are "containers" from which data is supposed to be posted to database control scripts
	# Assert that these scripts are being called from the proper location to avoid shenanigans
	function assert_container($container)
	{
		$_SESSION['container'] == $container or destroy("session current container mismatch");
	}

	# Sets current 'container' to current page
	function set_container()
	{
		$_SESSION['container'] = $_SERVER['PHP_SELF'];
	}

	# Called when an assert fails, destroy any current session.
	# Upon refresh, assert_session will detect lack of session and redirect user to login
	function destroy($error)
	{
		session_destroy();

		die("Fatal error: $error. Please refresh the page and log back in.");

		exit();
	}
?>
