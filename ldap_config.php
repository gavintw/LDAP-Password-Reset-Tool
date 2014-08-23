<?php
define('ENCRYPTION_KEY', 'd0a7e7997b6d5fcd55f4b5c32611b87cd923e88837b63bf2941ef819dc8ca282');
$server = "ldaps://118.102.242.126";
$port = 636;
$ldap_conn = ldap_connect($server, $port);
ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);       



//bind with a dummy user to enable ldap_search       
//TODO:Bind with a dummy user
$password = "*batman12346";
$binddn = "CN=Anant Gupta,CN=Users,DC=zomato,DC=me";
ldap_bind( $ldap_conn,$binddn , $password );

$admin_password = "fZhB96jx9";
$admin_bind_dn = "CN=Administrator,CN=Users,DC=zomato,DC=me";
	
?>
