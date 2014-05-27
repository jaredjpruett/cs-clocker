<?php
	class LDAP
	{
		var $conn;
		var $bind;

		var $employees;
		var $administrators; # Move to Admin child class?

		public function __construct($username, $password)
		{
			$this->conn = ldap_connect("matrix.txstate.edu") or die("Could not connect to LDAP server.");
			$this->bind = @ldap_bind($this->conn, "txstate\\$username", $password) or die("Incorrect login information.");

			$this->employees = $this->create_group_array("CS\$Labstaff", "Lab Assistant Groups");
		}

		public function disconnect()
		{
			ldap_unbind($this->conn);
		}

		function get_client_address()
		{
			if(getenv('HTTP_CLIENT_IP'))
				$ipaddress = getenv('HTTP_CLIENT_IP');
			else if(getenv('HTTP_X_FORWARDED_FOR'))
				$ipaddress = getenv('HTTP_X_FORWARDED_FOR');
			else if(getenv('HTTP_X_FORWARDED'))
				$ipaddress = getenv('HTTP_X_FORWARDED');
			else if(getenv('HTTP_FORWARDED_FOR'))
				$ipaddress = getenv('HTTP_FORWARDED_FOR');
			else if(getenv('HTTP_FORWARDED'))
				$ipaddress = getenv('HTTP_FORWARDED');
			else if(getenv('REMOTE_ADDR'))
				$ipaddress = getenv('REMOTE_ADDR');
			else
				$ipaddress = 'UNKNOWN';

			return $ipaddress;
		}

		# Create an array of strings consisting of the members of a group $cn
		public function create_group_array($cn, $ou)
		{
			$tree = "OU=$ou, OU=SecurityGroups, OU=CS, DC=matrix, DC=txstate, DC=edu";
			$filter = "CN=$cn";
			$attribute = array('member');

			$result = ldap_search($this->conn, $tree, $filter, $attribute) or die("Error: " . ldap_err2str(ldap_errno($conn)));
			$entries = ldap_get_entries($this->conn, $result) or die("Error: was unable to parse results.");

			$members = array();

			foreach($entries[0]['member'] as $entry)
			{
				$name = explode(',', $entry);
				$members[] = substr($name[0], 3);
			}

			return $members;
		}
	}
?>
