<?php

$message = array();
$message_css = "";

function mc_encrypt($encrypt, $key){
	$encrypt = serialize($encrypt);
	$iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC), MCRYPT_DEV_URANDOM);
	$key = @pack('H*', $key);
	$mac = hash_hmac('sha256', $encrypt, substr(bin2hex($key), -32));
	$passcrypt = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $encrypt.$mac, MCRYPT_MODE_CBC, $iv);
	$encoded = base64_encode($passcrypt).'|'.base64_encode($iv);
	return $encoded;
}

function generateKey($user, $oldPassword, $newPassword, $newPasswordCnf){
	include('ldap_config.php');
	global $message;
	global $message_css;
	if (!ldap_bind( $ldap_conn,$binddn , $password )){
		$message[] = "Error E000 - Please contact admin.";
		return false;
	}


	//get DN of user to validate credentails
	$basedn = "CN=Users,DC=zomato,DC=me";
	$user_name = "(sAMAccountName=$user)";
	$user_search = ldap_search( $ldap_conn, $basedn, $user_name );

	$user_entry = ldap_first_entry($ldap_conn, $user_search);        
	$user_dn = ldap_get_dn($ldap_conn, $user_entry);
	$bind =ldap_bind($ldap_conn, $user_dn, $oldPassword);

	if (!ldap_bind($ldap_conn, $user_dn, $oldPassword)) {
		$message[] = "Error E101 - Current Username or Password is wrong.";
		return false;
	}
	if ($newPassword == $oldPassword ) {
		$message[] = "Error E102 - Your New password and Old password are same!";
		return false;
	}
	if ($newPassword != $newPasswordCnf ) {
		$message[] = "Error E102 - Your New passwords do not match!";
		return false;
	}
	if (strlen($newPassword) < 8 ) {
		$message[] = "Error E103 - Your new password is too short.<br/>Your password must be at least 8 characters long.";
		return false;
	}
	if (!preg_match("/[0-9]/",$newPassword)) {
		$message[] = "Error E104 - Your new password must contain at least one number.";
		return false;
	}
	if (!preg_match("/[a-zA-Z]/",$newPassword)) {
		$message[] = "Error E105 - Your new password must contain at least one letter.";
		return false;
	}


	//get email of user
	$mail = ldap_get_attributes($ldap_conn,$user_entry)["mail"][0];

	// $hash = hash("crc32b",$user."_".$oldPassword);
	$hash = mc_encrypt($user.'~`'.$oldPassword.'~`'.$newPassword,ENCRYPTION_KEY); 
	$dn_link = str_replace(' ','_',$user_dn);  
	$mail_body = "Please go to the following link to reset your password: http://manage.zomans.com/resetpass/verify.php?key=$hash&dn=$dn_link";
	mail($mail, "Password Reset Key",$mail_body); 
	$message_css = "yes";
	$message[] = "The key has been sent to your mail";   
	return true; 	
}
//generateKey('anant.gupta','*batman12345','*batman12346','*batman12346');
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Employee Password Reset</title>
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
<h2>Password Reset</h2>
<p>Your new password must be at least 8 characters long and contain digits and letter:<br/>

<?php
if (isset($_POST["submitted"])) {
	generateKey($_POST['username'],$_POST['oldPassword'],$_POST['newPassword1'],$_POST['newPassword2']);
	global $message_css;
	if ($message_css == "yes") {
		?><div class="msg_yes"><?php
	} else {
		?><div class="msg_no"><?php
			$message[] = "Your password was not changed.";
	}
	foreach ( $message as $one ) { echo "<p>$one</p>"; }
	?></div><?php
} ?>
<form action="<?php print $_SERVER['PHP_SELF']; ?>" name="passwordChange" method="post">
<table style="width: 400px; margin: 0 auto;">
<tr><th>Username:</th><td><input name="username" type="text" size="20px" autocomplete="off" /></td></tr>
<tr><th>Current password:</th><td><input name="oldPassword" size="20px" type="password" /></td></tr>
<tr><th>New password:</th><td><input name="newPassword1" size="20px" type="password" /></td></tr>
<tr><th>New password (again):</th><td><input name="newPassword2" size="20px" type="password" /></td></tr>

			       <tr><td colspan="2" style="text-align: center;" >
			       <input name="submitted" type="submit" value="Change Password"/>
			       <button onclick="$('frm').action='changepassword.php';$('frm').submit();">Cancel</button>
			       </td></tr>
			       </table>
			       </form>
			       </div>
			       </body>
			       </html>
