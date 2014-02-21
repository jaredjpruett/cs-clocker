<?php
	include_once '../model/LDAP.php';

	class LDAP_Employee extends LDAP
	{
		public function __construct($username, $password)
		{
			parent::__construct($username, $password);
		}

		public function assert_exists($username)
		{
			in_array($username, $this->employees) or die("$username is not an employee.");
		}
	}
?>
