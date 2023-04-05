<?php
session_start();
require __DIR__.'/admin/lib/db.inc.php';
$cat = ierg4210_cat_fetchall();
$get_pid = int_validation($_GET['pid']);
$pid_num=0;
foreach ($cat as $value){
    $cat_table .= '<li class="nike"><a href="category.php?catid='.int_validation($value["CATID"]).'">'.string_validation($value["NAME"]).'</a></li><ul class="sneaker">';
    $band[$value["CATID"]]=string_validation($value["NAME"]);
    $catid = int_validation($value["CATID"]);
    $prods = ierg4210_prod_fetchByCat($catid);
    foreach ($prods as $pvalue){
      $cat_table .= '<li><a href="product.php?pid='.int_validation($pvalue["PID"]).'">'.string_validation($pvalue["NAME"]).'</a></li>';
      $pid_num++;
    }
    $cat_table .= '</ul>';
  }
  
  $p = ierg4210_prod_fetchOne($get_pid);
  foreach ($p as $pvalue){
    $prod = $pvalue;
  }
  $prod["CATID"]=int_validation($prod["CATID"]);
  $prod["PID"]=int_validation($prod["PID"]);
  $prod["NAME"]=string_validation($prod["NAME"]);
  $prod["DESCRIPTION"]=string_validation($prod["DESCRIPTION"]);
  $prod["PRICE"]=string_validation($prod["PRICE"]);
  $prod["INVENTORY"]=string_validation($prod["INVENTORY"]);

?>

<link href="style.css" rel="stylesheet" type="text/css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://www.paypal.com/sdk/js?client-id=AVTGQUwUb5Soi2KYSlEeNCN7Z67tpHbin6lt3Zhp6s355nH6Nn0oBBSbyK9wMvE47pdX5qMSvLB-YGhv&currency=USD"></script>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <title>IERG4210 Store Product</title>
  </head>
  <script src="https://unpkg.com/@webcreate/infinite-ajax-scroll@^3.0.0-beta.6/dist/infinite-ajax-scroll.min.js"></script>
<body>

<header>
<div class=head_content>
  <div class="Header">
  <?php $login=string_validation(auth()); if($login!=false) echo $login; ?>
  <a href="/">Fashion Sneaker</a>
  <a href="admin/admin.php">Admin</a>
  <a href="admin/login.php">Login</a>
  <div class="logout">
    <form  method="POST" action="admin/auth-process.php?action=logout" enctype="multipart/form-data">
      <input type="submit" value="Logout"/>
      <input type="hidden" name="nonce" value="<?php echo string_validation(csrf_getNonce('logout')); ?>"/>
    </form></div>
  </div>


<div class="shopping_list">
  <button class="show_shopping_list">Shopping List $0</button>
  <section class="shopping_list-content">
    <div class="item_list">
      
    </div>

    <div id="paypal-button-container"></div>
  </section>
</div>

</div>

</header>
<div class="container">
    <div class="item">
<section class="main">

  <div class="category">  
    <ul class="band">
    <?php echo $cat_table; ?>
    </ul>
  </div> 
  
  <div class="menu"><a href="/">Home</a> > <a href="category.php?catid=<?php echo $prod["CATID"]; ?>"><?php echo $band[$prod["CATID"]]; ?></a> > <a href="product.php?pid=<?php echo $prod["PID"]; ?>"><?php echo $prod["NAME"]; ?></a></div>

  <ul class="product_content">
            <img class="product_picture" src="admin/lib/images/<?php echo $prod["PID"]; ?>.jpg" />
            <ul class="detail">
                <li class="product_name"><?php echo $prod["NAME"]; ?></li>
                <li class="product_description"><?php echo $prod["DESCRIPTION"]; ?></li>
                <li class="product_price"><?php echo $prod["PRICE"]; ?></li>     
                <li class="add_to_cart"><button onclick='addtocart(<?php echo $prod["PID"]; ?>)'>Add To Cart</button></li> 
                <li class="inventory">Inventory: <?php echo $prod["INVENTORY"]; ?></li> 
            </ul>
            
        </ul>


</section>  
</div>

</div>

</div>
    <div class="pagination">
    <a href="<?php if($get_pid<$pid_num) { $get_pid++; echo "product.php?pid=".$get_pid; } 
    else echo"null"; ?>" class="next">Next</a>
    </div> 
</body>
</html>

<script type="text/javascript" src="cart.js"></script>
<script type="text/javascript" src="scroll.js"></script>
