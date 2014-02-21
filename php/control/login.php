<?php
	############################################################################
	## Initialize script and assert session validity						  ##
	############################################################################

	ini_set('display_errors', 'On');

	# credentials.php is a PHP file hosted outside of the public root
	# It contains variables host, netid, pass, database
	include_once '../../../../credentials.php';
	# Contains session asserts and paths to our scripts
	include_once '../session.php';
	include_once '../model/admin/LDAP_Admin.php';
	include_once '../model/employee/LDAP_Employee.php';

	# Also this here login script is clearly kind of repulsive at the moment
	############################################################################
	


	############################################################################
	## Validate posted data, set variables used in generating session		  ##
	############################################################################

	isset($_POST['type']) or die("Abort: session type not specified");

	# LDAP login credentials
	$username = $_POST['netid'];
	$password = $_POST['password'];
	$type = $_POST['type'];

	if($type == 'administrators')
	{
		$location = $admin_url['menu'];
		$ldap = new LDAP_Admin($username, $password);
	}
	else
	{
		$location = $employee_url['menu'];
		$ldap = new LDAP_Employee($username, $password);
	}

	# Assert user is either employee or administrator, depending on login mode
	$ldap->assert_exists($username);

	############################################################################



	############################################################################
	## Generate session, set session variables								  ##
	## 
	## Sessios user id and username provide the session identity and are used
	##	to assert sessions existence.
	## Database credentials are provided by credentials.php, hosted outside
	##	of the public root.
	############################################################################

	session_start();
	session_regenerate_id();

	$_SESSION['sess_user_id'] = session_id();
	$_SESSION['sess_username'] = $username;

	# Credentials for the database
	$_SESSION['host'] = $host;
	$_SESSION['netid'] = $netid;
	$_SESSION['pass'] = $pass;
	$_SESSION['database'] = $database;

	$_SESSION['address'] = $ldap->get_client_address();
	# Session variable 'container' is used by certain scripts to assert that they're being invoked propery
	$_SESSION['container'] = $_SERVER['PHP_SELF'];
	# Session variable 'type' denotes the type of session the user is logged into; administrator or employee
	# Used to further security and robustness of application
	$_SESSION['type'] = $type;

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
