<?php
	############################################################################
	## Initialize script and assert session validity						  ##
	############################################################################

	#ini_set('display_errors', 'On'); #debug

	# config.php contains the variables host, netid, pass, database
	include_once '../config.php';
	# Contains session asserts and paths to our source files
	include_once '../session.php';
	include_once '../model/admin/LDAP_Admin.php';
	include_once '../model/employee/LDAP_Employee.php';

	############################################################################

	############################################################################
	## Validate posted data, set variables used in generating session		  ##
	############################################################################

	isset($_POST['type']) or die("Abort: session type not specified");

	# LDAP login credentials
	$username = $_POST['netid'];
	$password = $_POST['password'];
	$type = $_POST['type'];

	# Our next location will be the main menu for either admin or regular user,
	# depending on the type of login executed.
	# Similarly, the LDAP object type used is set.
	# Boy howdy how clunky is that
	if($type == 'administrators') {
		$location = $admin_url['menu'];
		$ldap = new LDAP_Admin($username, $password);
	} else {
		$location = $employee_url['menu'];
		$ldap = new LDAP_Employee($username, $password);
	}

	# Assert user is either employee or administrator, depending on login mode
	$ldap->assert_exists($username);

	############################################################################



	############################################################################
	## Generate session, set session variables								  ##
	##------------------------------------------------------------------------##
	## Session's user id and username constitute the session identity and are ##
	##  used to assert sessions existence.									  ##
	## Database credentials are provided by credentials.php, hosted outside	  ##
	##	of the public root for some raisin.									  ##
	############################################################################

	# Begin session and generate an id
	session_start();
	session_regenerate_id();

	# Set session variables to track login name and session id.
	# I really need to consolidate this manner of variable.
	$_SESSION['sess_user_id'] = session_id();
	$_SESSION['sess_username'] = $username;

	# Credentials for the database
	$_SESSION['host'] = $host;
	$_SESSION['port'] = $port;
	$_SESSION['netid'] = $netid;
	$_SESSION['pass'] = $pass;
	$_SESSION['database'] = $database;

	# Gets IPv4 address of connecting client
	$_SESSION['address'] = $ldap->get_client_address();
	# Session variable 'container' is used by certain scripts to assert that they're being invoked propery
	# Set it to denote that our most recent 'container' was the login page
	$_SESSION['container'] = $_SERVER['PHP_SELF'];
	# Session variable 'type' denotes the type of session the user is logged into; administrator or employee
	# Used to further security and robustness of application or something
	$_SESSION['type'] = $type;

	# Create session variables to keep track of our list of employees and administrators as grabbed from LDAP
	$_SESSION['employees'] = $ldap->employees;
	$_SESSION['administrators'] = $ldap->administrators;

	session_write_close();

	############################################################################



	############################################################################
	## Disconnect from LDAP and navigate to home menu						  ##
	############################################################################

	$ldap->disconnect();

	header("location: $location");

	exit();

	############################################################################
?>
