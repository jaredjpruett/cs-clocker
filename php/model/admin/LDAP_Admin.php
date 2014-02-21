<?php
	ini_set('display_errors', 'On');

	include_once '../model/LDAP.php';

	class LDAP_Admin extends LDAP
	{
		public function __construct($username, $password)
		{
			parent::__construct($username, $password);

			$this->administrators = $this->create_group_array("CS\$TechStaff", "CS Personnel Groups");
			$this->administrators[] = "jjp54";
		}

		public function assert_exists($username)
		{
			in_array($username, $this->administrators) or die("$username is not an administrator. You're totally in trouble now.");
		}
	}
?>
