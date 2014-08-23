<?php
/*
if (!$_GET['key']){
    die('Unauthorized');
}
$key = $_GET['key'];
$dn = str_replace('_',' ',$_GET['dn']);
*/
$key = 'kjbke';
$dn = 'lrvnekr';
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<title>Password Change Page</title>
<style type="text/css">
body { font-family: Verdana,Arial,Courier New; font-size: 0.7em; }
th { text-align: right; padding: 0.8em; }
#container { text-align: center; width: 500px; margin: 5% auto; }
.msg_yes { display: none; margin: 0 auto; text-align: center; color: green; background: #D4EAD4; border: 1px solid green; border-radius: 10px; margin: 2px; }
.msg_no { display: none; margin: 0 auto; text-align: center; color: red; background: #FFF0F0; border: 1px solid red; border-radius: 10px; margin: 2px; }
#hidden{display: none;}
</style>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<script>
$(document).ready(function(){
        
        $('#passwordChange').submit(function(e){
            e.preventDefault();
            var postData = $(this).serializeArray();
            var formURL = $(this).attr("action");
            console.log(postData);            
           
            $.ajax({
                url: forURL,
                type: "POST",
                data: postData,
                success: function(response){
               console.log(response);
                     if(response == "success"){
                    $('.msg_yes').css('display','block');
                    $('.msg_yes').html('Password changed successfully');
                }else{
                    $('.msg_no').css('display','block');
                    $('.msg_no').html('Password not changed');
                }
                     
                }

        });



    });
});

</script>

</head>
<body>
<div id="container">
<h2>Password Change Page</h2>
<p>Your new password must be 8 characters long or longer and have at least:<br/>
<div class="msg_yes"></div>
<div class="msg_no"></div>
<form action="secret.php" name="passwordChange" id= "passwordChange" method="post">
<table style="width: 400px; margin: 0 auto;">
<tr><th>Username:</th><td><input name="username" type="text" size="20px" autocomplete="off" /></td></tr>
<tr><th>Current password:</th><td><input name="oldPassword" size="20px" type="password" /></td></tr>
<tr><th>New password:</th><td><input name="newPassword1" size="20px" type="password" /></td></tr>
<tr><th>New password (again):</th><td><input name="newPassword2" size="20px" type="password" /></td></tr>
<tr id="hidden"><td><input name="key" value="<?php echo $key; ?>"></td></tr>
<tr id="hidden"><td><input name="dn" value="<?php echo $dn; ?>"></td></tr>
                               <tr><td colspan="2" style="text-align: center;" >
                               <input name="submitted" type="submit" value="Change Password"/>
                               <button onclick="$('frm').action='changepassword.php';$('frm').submit();">Cancel</button>
                               </td></tr>
                               </table>
                               </form>
                               </div>
                               </body>
                               </html>
