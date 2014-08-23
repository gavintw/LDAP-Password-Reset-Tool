<?php

function pwd_encryption( $newPassword ) {  
	var_dump($newPassword);	
	$newPassword = "\"" . $newPassword . "\"";
	$len = strlen( $newPassword ); 
	$newPassw = "";
	for ( $i = 0; $i < $len; $i++ ) {
		$newPassw .= "{$newPassword {$i}}\000"; 
	} 
	$userdata["unicodePwd"] = $newPassw;  
	var_dump($userdata);	
	return $userdata; 
} 
function mc_decrypt($decrypt, $key){
	$decrypt = explode('|', $decrypt);
	$decoded = base64_decode($decrypt[0]);
	$iv = base64_decode($decrypt[1]);
	if(strlen($iv)!==mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC)){ return false; }
	$key = pack('H*', $key);
	$decrypted = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $decoded, MCRYPT_MODE_CBC, $iv));
	$mac = substr($decrypted, -64);
	$decrypted = substr($decrypted, 0, -64);
	$calcmac = hash_hmac('sha256', $decrypted, substr(bin2hex($key), -32));
	if($calcmac!==$mac){ return false; }
	$decrypted = unserialize($decrypted);
	return $decrypted;
}

?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Password Change Page</title>
<style type="text/css">
body { font-family: Verdana,Arial,Courier New; font-size: 0.7em; }
th { text-align: right; padding: 0.8em; }
#container { text-align: center; width: 500px; margin: 5% auto; }
.msg_yes { margin: 0 auto; text-align: center; color: green; background: #D4EAD4; border: 1px solid green; border-radius: 10px; margin: 2px; }
.msg_no { margin: 0 auto; text-align: center; color: red; background: #FFF0F0; border: 1px solid red; border-radius: 10px; margin: 2px; }
</style>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>
<body>
<div id="container">
<?php 
include('ldap_config.php');
/*
if (!$_GET['key'] || !$_GET['dn']){
	die('Unauthorized');
}
$key = $_GET['key'];
*/
$key = "lXXSwECnwaXTOCjU3/v6M5a087JSjdZMkkAjNaD2vzxKRqxG1kZi8VV5m9EDs2fgn5IJiAaCAW2TmxaIbEmjkWosQaT5D7yp0qHtcS5fEL2JTzEOY/ED5E2pTFrVNmcaQzMX0TWHZvi1Rk3fslrMCC20u1DR4jLWxH39YvlTXI0=|Fkg4yIi6C1Mjl05Nzbv+R14SHDKRhdL3Etb6tvN6 /I="; 
//$dn = str_replace('_',' ',$_GET['dn']);
$credentials = explode('~`',mc_decrypt($key,ENCRYPTION_KEY));
var_dump($credentials);

/*
$user = $credentials[0];
$oldPassword = $credentials[1];
$newPassword = $credentials[2];

if (!ldap_bind($ldap_conn, $admin_bind_dn, $admin_password)) {
	$message[] = "Error E106 - Something's wrong. Please contact admin.";
	var_dump(1);
	return false;
}
$userdata = pwd_encryption($newPassword); 
$result = ldap_mod_replace($ldap_conn, $dn , $userdata);
var_dump($user_data);
var_dump($result);	


if ($result == true){
	echo '<div class="msg_yes">Password Successfully changed</div>';
}else{
	echo '<div class="msg_no">Password not changed</div>';
}
*/
?>

</div>
</body>
</html>
