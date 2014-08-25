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
	include('include/ldap_config.php');
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

	if($user == "" ||  $oldPassword == "" || $newPassword=="" || $newPasswordCnf == ""){
		$message[] = "Error E100 - Fields are blank.";
		return false;
			
	}	
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
body { 
font-family: "Helvetica Neue",Helvetica,Calibri,Arial,sans-serif;
font-size: 13px;
color: #2d2d2a;
line-height: 1.4;
margin: 0px;
}
th { text-align: right; padding: 0.8em; }
#container { 
text-align: center; 
width: 500px; 
margin: 5% auto; 
}
.msg_yes { margin: 0 auto; text-align: center; color: green; background: #D4EAD4; border: 1px solid green; border-radius: 10px; margin: 2px; }
.msg_no { margin: 0 auto; text-align: center; color: red; background: #FFF0F0; border: 1px solid red; border-radius: 10px; margin: 2px; }
.header .header--fixed {
position: fixed;
width: 100%;
z-index: 2;
background-color: #2d2d2a;
background-color: rgba(45,45,42,.95);
}
.header {
zoom: 1;
height: 60px;
background-color: #2d2d2a;
background-color: rgba(0,0,0,.8);
}
.container {
width: 992px;
margin: 0 auto;
zoom: 1;
}
.column {
margin: 0 10px;
float: left;
}
.grid_16 {
width: 972px;
}
.logo {
transition: .15s ease-out background-color;
-moz-transition: .15s ease-out background-color;
-webkit-transition: .15s ease-out background-color;
-o-transition: .15s ease-out background-color;
width: 69px;
height: 60px;
display: block;
position: relative;
z-index: 3;
background-color: #cb202d;
text-align: center;
float: left;
}
a {
text-decoration: none;
color: #cb202d;
}
a:-webkit-any-link {
color: -webkit-link;
text-decoration: underline;
cursor: auto;
}
.clearfix:after, .clearfix:before {
content: " ";
display: table;
}
.clearfix:after {
clear: both;
}
.logo img {
width: 56px;
margin: 23px 0 0;
}
img {
vertical-align: middle;
border: 0;
}
form{
margin: 0;
display: block;
}
.label {
line-height: 20px;
letter-spacing: .5px;
font-size: 13px;
font-weight: 700;
text-transform: uppercase;
color: #8d8d85;
display: block;
}
input[type=text], input[type=password]{
background: #f4f4f2;
border: 1px solid #f4f4f2;
border-radius: 3px;
width: 185px;
color: #9a9a93;
border-radius: 3px;
-moz-border-radius: 3px;
height: 18px;
padding: 11px 10px 9px;
line-height: 20px;
-webkit-border-radius: 0;
-webkit-appearance: none;
color: black;
}
input:hover{
border-color: #9a9a93;
}
.btn:hover {
background-color: #cbcbc8;
text-decoration: none;
}
.btn {
text-shadow: none;
border-radius: 3px;
-moz-border-radius: 3px;
-webkit-border-radius: 3px;
text-transform: uppercase;
font-weight: 700;
line-height: 42px;
height: 40px;
padding: 0 10px;
background-color: #e4e4e2;
color: #4d4d49;
border: none;
cursor: pointer;
transition: .15s ease-out background-color;
-moz-transition: .15s ease-out background-color;
-webkit-transition: .15s ease-out background-color;
-o-transition: .15s ease-out background-color;
-webkit-font-smoothing: antialiased;
outline: 0;
font-family: ZomatoIsHiring,"Helvetica Neue",Helvetica,Calibri,Arial,sans-serif;
}
.section-heading {
margin: 0;
padding: 0;
line-height: 30px;
font-size: 18px;
text-transform: uppercase;
color: #4d4d49;
margin-bottom: 5px;
}
h2 {
display: block;
font-size: 1.5em;
-webkit-margin-before: 0.83em;
-webkit-margin-after: 0.83em;
-webkit-margin-start: 0px;
-webkit-margin-end: 0px;
font-weight: bold;
}
p {
padding: 0 15px;
color: #7d7d76;
margin: 1em 0;
display: block;
-webkit-margin-before: 1em;
-webkit-margin-after: 1em;
-webkit-margin-start: 0px;
-webkit-margin-end: 0px;

}
</style>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>
<body>
<header class="header header--fixed " id="header">

  <div class="container">
    <div class="grid_16 column clearfix">
      <a class="logo" href="http://anant.zdev.net" title="Zomato Delhi NCR - Restaurant search, View Menus, Pictures, Reviews, Address and Phone numbers for restaurants in Delhi NCR">
<img src="http://anant.zdev.net/images/zomatologo.png" alt="Zomato - Restaurant Directory and Food Guide for Delhi NCR, Restaurant Reviews, Menus"></a>
      </div>
      <!-- end grid-s-16 -->
  </div>

  <!-- end container -->
</header>

<div id="container">
<h2 class="section-heading">Password Reset</h2>
<p>Your new password must be at least 8 characters long and contain digits,letter and special characters OR digits, capital and small letters.<br/>

<?php
if (isset($_POST["submitted"])) {
	generateKey($_POST['username'],$_POST['oldPassword'],$_POST['newPassword1'],$_POST['newPassword2']);
	global $message_css;
	if ($message_css == "yes") {
		?><div class="msg_yes"><?php
	} else {
		?><div class="msg_no"><?php
	}
	foreach ( $message as $one ) { echo "<p>$one</p>"; }
	?></div><?php
} ?>
<form action="<?php print $_SERVER['PHP_SELF']; ?>" name="passwordChange" method="post">
<table style="width: 400px; margin: 0 auto;">
<tr><th class="label">Username:</th><td><input name="username" type="text" size="20px" autocomplete="off" /></td></tr>
<tr><th class="label">Current password:</th><td><input name="oldPassword" size="20px" type="password" /></td></tr>
<tr><th class="label">New password:</th><td><input name="newPassword1" size="20px" type="password" /></td></tr>
<tr><th class="label">New password (again):</th><td><input name="newPassword2" size="20px" type="password" /></td></tr><br>
 <tr><td colspan="2" style="text-align: center;" >
<input name="submitted" class="btn" type="submit" value="Submit"/>
<button class="btn" onclick="$('frm').action='changepassword.php';$('frm').submit();">Cancel</button>
</td></tr>
</table>
</form>
</div>
</body>
</html>
