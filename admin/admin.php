<link href="adminstyle.css" rel="stylesheet" type="text/css">
<?php
session_start();
require __DIR__.'/lib/db.inc.php';
$auth=string_validation(admin_auth()); 
if($auth==false){
    header('Location: login.php',true,302);
}
$res = ierg4210_cat_fetchall();
$options = '';

foreach ($res as $value){
    $options .= '<option value="'.int_validation($value["CATID"]).'"> '.string_validation($value["NAME"]).' </option>';
}

$res2 = ierg4210_prod_fetchall();
$options2 = '';

foreach ($res2 as $value2){
    $options2 .= '<option value="'.int_validation($value2["PID"]).'"> '.string_validation($value2["NAME"]).' </option>';
}

?>


<html>
<header>
    <div><?php $login=string_validation(auth()); if($login!=false) echo $login; ?></div>
    <div><a href="/">Home</a></div>
    <div><a href="db.php">DB table</a></div>
    <div class="logout">
        <form  method="POST" action="auth-process.php?action=logout" enctype="multipart/form-data">
        <input type="submit" value="Logout"/>
        <input type="hidden" name="nonce" value="<?php echo csrf_getNonce('logout'); ?>"/>
        </form>
    </div>
<header>
  
<body>
    <fieldset>
        <legend> New Product</legend>
        <form id="prod_insert" method="POST" action="admin-process.php?action=prod_insert" enctype="multipart/form-data">
            <label for="prod_catid"> Category *</label>
            <div> <select id="prod_catid" name="catid"><?php echo $options; ?></select></div>
            <label for="prod_name"> Name *</label>
            <div> <input id="prod_name" type="text" name="name" required="required" pattern="^[A-Za-z0-9 -']+$"/></div>
            <label for="prod_price"> Price *</label>
            <div> <input id="prod_price" type="text" name="price" required="required" pattern="^[0-9.']+$"/></div>
            <label for="prod_desc"> Description *</label>
            <div> <input id="prod_desc" type="text" name="description"/> </div>
            <label for="prod_price"> Inventory *</label>
            <div> <input id="prod_inventory" type="text" name="inventory" required="required" pattern="^\d+$"/></div>
            <label for="prod_image">Drag and drop an image/select an image</label>
            <div class = drag-and-drop>
                <input class="file" id="prod_img" type="file" name="file" required="true" accept="image/jpeg, image/gif, image/png" onchange="document.getElementById('pic').src = window.URL.createObjectURL(this.files[0])"/> 
            </div>
            <img class="pic" id="pic" alt="your image" src='' /> 
            <input type="submit" value="Submit"/>
            <input type="hidden" name="nonce" value="<?php echo csrf_getNonce('prod_insert'); ?>"/>
        </form>
    </fieldset>

    <fieldset>
        <legend> Update Product</legend>
        <form id="prod_edit" method="POST" action="admin-process.php?action=prod_edit" enctype="multipart/form-data">
            <label for="prod_pid"> Product *</label>
            <div> <select id="prod_pid" name="pid"><?php echo $options2; ?></select></div>
            <label for="prod_catid"> Category *</label>
            <div> <select id="prod_catid" name="catid"><?php echo $options; ?></select></div>
            <label for="prod_name"> Name *</label>
            <div> <input id="prod_name" type="text" name="name" required="required" pattern="^[A-Za-z0-9 ]+$"/></div>
            <label for="prod_price"> Price *</label>
            <div> <input id="prod_price" type="text" name="price" required="required" pattern="^\d+\.?\d*$"/></div>
            <label for="prod_desc"> Description *</label>
            <div> <input id="prod_desc" type="text" name="description"/> </div>
            <label for="prod_price"> Inventory *</label>
            <div> <input id="prod_inventory" type="text" name="inventory" required="required" pattern="^\d+$"/></div>
            <label for="prod_image">Drag and drop an image/select an image</label>
            <div class = drag-and-drop>
                <input class="file" id="prod_img" type="file" name="file" required="true" accept="image/jpeg, image/gif, image/png" onchange="document.getElementById('pic2').src = window.URL.createObjectURL(this.files[0])"/> 
            </div>
            <img class="pic" id="pic2" alt="your image" src='' /> 
            <input type="submit" value="Submit"/>
            <input type="hidden" name="nonce" value="<?php echo csrf_getNonce('prod_edit'); ?>"/>
        </form>
    </fieldset>

    <fieldset>
        <legend> Delete Product</legend>
        <form id="prod_delete" method="POST" action="admin-process.php?action=prod_delete" enctype="multipart/form-data">
            <label for="prod_pid"> Product *</label>
            <div> <select id="prod_pid" name="pid"><?php echo $options2; ?></select></div>
            <input type="submit" value="Submit"/>
            <input type="hidden" name="nonce" value="<?php echo csrf_getNonce('prod_delete'); ?>"/>
        </form>
    </fieldset>

    <fieldset>
        <legend> New Category</legend>
        <form id="cat_insert" method="POST" action="admin-process.php?action=cat_insert" enctype="multipart/form-data">
            <label for="cat_name"> Name *</label>
            <div> <input id="cat_name" type="text" name="name" required="required" pattern="^[\w\-]+$"/></div>
            <input type="submit" value="Submit"/>
            <input type="hidden" name="nonce" value="<?php echo csrf_getNonce('cat_insert'); ?>"/>
        </form>
    </fieldset>

    <fieldset>
        <legend> Update Category</legend>
        <form id="cat_edit" method="POST" action="admin-process.php?action=cat_edit" enctype="multipart/form-data">
            <label for="cat_catid"> Category *</label>
            <div> <select id="cat_catid" name="catid"><?php echo $options; ?></select></div>
            <label for="cat_name"> New Name *</label>
            <div> <input id="cat_name" type="text" name="name" required="required" pattern="^[\w\-]+$"/></div>
            <input type="submit" value="Submit"/>
            <input type="hidden" name="nonce" value="<?php echo csrf_getNonce('cat_edit'); ?>"/>
        </form>
    </fieldset>

    <fieldset>
        <legend> Delete Category</legend>
        <form id="cat_delete" method="POST" action="admin-process.php?action=cat_delete" enctype="multipart/form-data">
            <label for="cat_catid"> Category *</label>
            <div> <select id="cat_catid" name="catid"><?php echo $options; ?></select></div>
            <input type="submit" value="Submit"/>
            <input type="hidden" name="nonce" value="<?php echo csrf_getNonce('cat_delete'); ?>"/>
        </form>
    </fieldset>

</body>
</html>