<?php
return($_POST);
die():
$key = $_POST['key'];
$dn = $_POST['dn'];
$user = $_POST['user'];
$oldPassword = $_POST['oldPassword'];
$newPassword = $_POST['newPassword'];
function pwd_encryption( $newPassword ) {  
        $newPassword = "\"" . $newPassword . "\"";
        $len = strlen( $newPassword ); 
        $newPassw = "";
        for ( $i = 0; $i < $len; $i++ ) {
                $newPassw .= "{$newPassword {$i}}\000"; 
        } 
        $userdata["unicodePwd"] = $newPassw;  
        return $userdata; 
} 

function changePassword($user,$oldPassword,$newPassword,$newPasswordCnf){
        include('ldap_config.php');
        global $key;
        global $dn;
var_dump($key);
var_dump($dn);        
if (hash("crc32b",$user."_".$oldPassword) != $key) {
                $message[] = "Error E101 - Key or credentials is incorrect.";
                return false;
        }
        /*       
                 if ($newPassword == $oldPassword ) {
                 $message[] = "Error E102 - Your New password and Old password are same!";
                 return false;
                 }
         */
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

        $password = "fZhB96jx9";
        $bind_dn = "CN=Administrator,CN=Users,DC=zomato,DC=me";
        if (!ldap_bind($ldap_conn, $bind_dn, $password)) {
                $message[] = "Error E106 - Something's wrong. Please contact admin.";
                return false;
        }
        echo 1;
        $userdata = pwd_encryption($newPassword); 
        $result = ldap_mod_replace($ldap_conn, $dn , $userdata);
        var_dump($result);        
        if ($result){
                $message[] = "Password changed successfully";
                $message_css = "yes";
                return true;        
        }else{
                $message[] = "Error in changing password";
                return false;
        }
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
<h2>Password Change Page</h2>
<p>Your new password must be 8 characters long or longer and have at least:<br/>

<?php
if (isset($_POST["submitted"])) {
        changePassword($_POST['username'],$_POST['oldPassword'],$_POST['newPassword1'],$_POST['newPassword2']);
        global $message_css;
        if ($message_css == "yes") {
                ?><div class="msg_yes"><?php
        } else {
                ?><div class="msg_no"><?php
                        $message[] = "Your password was not changed.";
        }
        foreach ( $message as $one ) { echo "<p>$one</p>"; }
        ?></div><?php
}else{
        if (!$_GET['key']){
                die('Unauthorized');
        }
        $key = $_GET['key'];
        $dn = str_replace('_',' ',$_GET['dn']);



}


?>

