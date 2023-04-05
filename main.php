<?php
session_start();
require __DIR__.'/admin/lib/db.inc.php';
$cat = ierg4210_cat_fetchall();
foreach ($cat as $value){
  $cat_names .= '<li class="band"><li class="band_name"><a href="category.php?catid='.int_validation($value["CATID"]).'">'.string_validation($value["NAME"]).'</a></li></li>';
}
foreach ($cat as $value){
  $cat_table .= '<li class="nike"><a href="category.php?catid='.int_validation($value["CATID"]).'">'.string_validation($value["NAME"]).'</a></li><ul class="sneaker">';
  $catid = int_validation($value["CATID"]);
  $prod = ierg4210_prod_fetchByCat($catid);
  foreach ($prod as $pvalue){
    $cat_table .= '<li><a href="product.php?pid='.int_validation($pvalue["PID"]).'">'.string_validation($pvalue["NAME"]).'</a></li>';
  }
  $cat_table .= '</ul>';
}
$product = ierg4210_prod_fetchAll();
foreach ($product as $prod){
    $table .= '<ul class="product"><img class="product_picture" src="admin/lib/images/'.int_validation($prod["PID"]).'.jpg" /><li class="product_name"><a href="product.php?pid='.int_validation($prod["PID"]).'">'.string_validation($prod["NAME"]).'</a></li><li class="product_price">'.$prod["PRICE"].'</li><li class="add_to_cart"><button onclick="addtocart('.int_validation($prod["PID"]).')">Add To Cart</button></li></ul>';
}
?>
<link href="style.css" rel="stylesheet" type="text/css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://www.paypal.com/sdk/js?client-id=AVTGQUwUb5Soi2KYSlEeNCN7Z67tpHbin6lt3Zhp6s355nH6Nn0oBBSbyK9wMvE47pdX5qMSvLB-YGhv&currency=USD"></script>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <title>IERG4210 Store Home</title>
  </head>
  <script src="https://unpkg.com/@webcreate/infinite-ajax-scroll@^3.0.0-beta.6/dist/infinite-ajax-scroll.min.js"></script>
<body>

<header>
  <div class=head_content>
  <div class="Header">
  <?php $login=auth(); if($login!=false) echo htmlspecialchars(string_validation($login)); ?>
  <a href="/">Fashion Sneaker</a>
  <a href="admin/admin.php">Admin</a>
  <a href="admin/login.php">Login</a>
  <a href="user_order.php">My Order Record</a>
  <div class="logout">
    <form  method="POST" action="admin/auth-process.php?action=logout" enctype="multipart/form-data">
      <input type="submit" value="Logout"/>
      <input type="hidden" name="nonce" value="<?php echo csrf_getNonce('logout'); ?>"/>
    </form></div>
  </div>



<div class="shopping_list">
  <button class="show_shopping_list">Shopping List $0</button>
  <section class="shopping_list-content">
    <div class="item_list">
      
    </div>

    <div id="paypal-button-container"></div>

    <button class="checkout" onclick='ReturnCart()'>Checkout</button>

  </section>
</div>




</div>

</header>

<section class="main">

  <div class="category">  
    <ul class="band">
    <?php echo $cat_table; ?>
    </ul>
  </div> 
  
  <div class="menu"><a href="/">Home</a></div>

  <ul class="table">
    <?php echo $table; ?>
  </ul>

  <div class="container">
    <div class="item"></div>

</div>
    <div class="pagination">
    <a href="category.php?catid=1" class="next">Next</a>
    </div>
</section>  
</body>
</html>
<script type="text/javascript" src="cart.js"></script>
<script type="text/javascript" src="scroll.js"></script>

