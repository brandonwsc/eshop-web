<?php
session_start();
require __DIR__.'/lib/db.inc.php';
?>
<html>
<header>
    <div><?php $login=auth(); if($login!=false) echo string_validation($login); ?></div>
    <div><a href="/">Home</a></div>
    <div class="logout">
        <form  method="POST" action="auth-process.php?action=logout" enctype="multipart/form-data">
        <input type="submit" value="Logout"/>
        <input type="hidden" name="nonce" value="<?php echo string_validation(csrf_getNonce('logout')); ?>"/>
        </form>
    </div>
<header>

    <fieldset>
        <legend>IERG4210 Shop49 Login</legend>
        <form id="login" method="POST" action="auth-process.php?action=login" enctype="multipart/form-data">
            <label for="username"> Username:</label>
            <div> <input id="username" type="email" name="username" required="required"></div>
            <label for="password"> Password:</label>
            <div> <input id="password" type="password" name="password" required="required"></div>
            <input type="submit" value="Submit"/>
            <input type="hidden" name="nonce" value="<?php echo string_validation(csrf_getNonce('login')); ?>"/>
        </form>
    </fieldset>

    <fieldset>
        <legend>IERG4210 Shop49 Register</legend>
        <form id="register" method="POST" action="auth-process.php?action=register" enctype="multipart/form-data">
            <label for="username"> Username:</label>
            <div> <input id="username" type="email" name="username" required="required"></div>
            <label for="password"> Password:</label>
            <div> <input id="password" type="password" name="password" required="required"></div>
            <label for="password"> Re-enter Password:</label>
            <div> <input id="repassword" type="password" name="repassword" required="required"></div>
            <input type="submit" value="Submit"/>
            <input type="hidden" name="nonce" value="<?php echo string_validation(csrf_getNonce('register')); ?>"/>
        </form>
    </fieldset>

    <fieldset>
        <legend>IERG4210 Shop49 Change Password</legend>
        <form id="change" method="POST" action="auth-process.php?action=change" enctype="multipart/form-data">
            <label for="username"> Username:</label>
            <div> <input id="username" type="email" name="username" required="required"></div>
            <label for="password"> Current Password:</label>
            <div> <input id="password" type="password" name="password" required="required"></div>
            <label for="password"> New Password:</label>
            <div> <input id="newpassword" type="password" name="newpassword" required="required"></div>
            <label for="password"> Re-enter New Password:</label>
            <div> <input id="renewpassword" type="password" name="renewpassword" required="required"></div>
            <input type="submit" value="Submit"/>
            <input type="hidden" name="nonce" value="<?php echo string_validation(csrf_getNonce('change')); ?>"/>
        </form>
    </fieldset>

</html>