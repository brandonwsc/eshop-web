<?php
require __DIR__.'/admin/lib/db.inc.php';
$cat = ierg4210_cat_fetchall();
$get_pid = int_validation($_GET['pid']);

  
  $p = ierg4210_prod_fetchOne($get_pid);
  foreach ($p as $pvalue){
    $prod = $pvalue;
  }

  $return_arr = array("name" => $prod["NAME"], "price" => $prod["PRICE"], "pid" => $get_pid);


echo  json_encode($return_arr);

?>
