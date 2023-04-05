<?php
require __DIR__.'/admin/lib/db.inc.php';
global $db;
$db = ierg4210_DB();

$saved_pid=$_REQUEST["saved_pid"];
$saved_quan=$_REQUEST["saved_quan"];
$items=[];
$total_price=0;
$return_arr=[];
$total_price=0;
$items=[];
$item=[];
foreach($saved_pid as $num => $value){
    $pid=int_validation($saved_pid[$num]);
    $quan=int_validation($saved_quan[$num]);
    $p = ierg4210_prod_fetchOne($pid);
    foreach ($p as $prod){
        $name=string_validation($prod["NAME"]);
        $price=string_validation($prod["PRICE"]);
    }
    $price=(int)$price;
    $total_price+=$quan*$price;
    $item = array("name" => $name, "price" => $price, "pid" => $pid, "quan" => $quan);
    $itemname = "item".($num+1);
    array_push($items, $item);
   
}
$merchantemail = "sb-x1g8s15638159@business.example.com";
$currency="USD";
$salt = mt_rand();
$total_price=(int)$total_price;

$info = array("merchantemail"=>$merchantemail, "salt"=>(string)$salt, "currency"=>$currency, "total_price"=>(string)$total_price);
$return_array= array("info"=>$info, "items"=>$items);
$digest =  hash_hmac('sha256', json_encode($return_array), $salt);
$productlist = json_encode($items);

if(!auth()) $user='Guest';
else $user = auth();

global $db;
$db = ierg4210_DB();

$sql="INSERT INTO ORDERS (SALT, USER, DIGEST, PRODUCTLIST, TOTALPRICE, CURRENCY, MERCHANTEMAIL) VALUES (?, ?, ?, ?, ? ,? ,?);";
$q = $db->prepare($sql);
$q->bindParam(1, $salt);
$q->bindParam(2, $user);
$q->bindParam(3, $digest);
$q->bindParam(4, $productlist);
$q->bindParam(5, $total_price);
$q->bindParam(6, $currency);
$q->bindParam(7, $merchantemail);
$q->execute();
$lastInsertId = $db->lastInsertId();
(int)$lastInsertId+=1300;
$lastInsertId=(string)$lastInsertId;

$info = array("merchantemail"=>$merchantemail, "salt"=>$salt, "currency"=>$currency, "total_price"=>$total_price,"digest"=>$digest,"lastInsertId"=>$lastInsertId);
$return_array= array("info"=>$info, "items"=>$items);

echo json_encode($return_array);
?>