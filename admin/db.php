<link href="adminstyle.css" rel="stylesheet" type="text/css">
<?php
session_start();
require __DIR__.'/lib/db.inc.php';
$auth=string_validation(admin_auth()); 
if($auth==false){
    header('Location: login.php',true,302);
}
$orders_string="";
$orders = ierg4210_order_fetchall();
foreach($orders as $order){
    $UUID=string_validation($order["UUID"]);
    $SALT=string_validation($order["SALT"]);
    $USER=string_validation($order["USER"]);
    $DIGEST=string_validation($order["DIGEST"]);
    $PRODUCTLIST=string_validation($order["PRODUCTLIST"]);
    $TOTALPRICE=string_validation($order["TOTALPRICE"]);
    $CURRENCY=string_validation($order["CURRENCY"]);
    $MERCHANTEMAIL=string_validation($order["MERCHANTEMAIL"]);
    $TXNID=string_validation($order["TXNID"]);
    $PATMENTSTATUS=string_validation($order["PATMENTSTATUS"]);
    $orders_string.="<br>UUID: ".$UUID." SALT: ".$SALT." USER: ".$USER." DIGEST: ".$DIGEST." PRODUCTLIST: ".$PRODUCTLIST." TOTALPRICE: ".$TOTALPRICE." CURRENCY: ".$CURRENCY." MERCHANTEMAIL: ".$MERCHANTEMAIL." TXNID: ".$TXNID." PATMENTSTATUS: ".$PATMENTSTATUS."</br>";
}

?>

<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>IERG4210 Store Home</title>
</head>
<header>
    <div><?php $login=string_validation(auth()); if($login!=false) echo $login; ?></div>
    <div><a href="/">Home</a></div>
    <div class="logout">
        <form  method="POST" action="auth-process.php?action=logout" enctype="multipart/form-data">
        <input type="submit" value="Logout"/>
        <input type="hidden" name="nonce" value="<?php echo csrf_getNonce('logout'); ?>"/>
        </form>
    </div>
<header>

<body>
<?php
    echo $orders_string;
?>

</body>
</html>