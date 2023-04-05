<?php
session_start();
function ierg4210_DB() {
	// connect to the database
	// TODO: change the following path if needed
	// Warning: NEVER put your db in a publicly accessible location
	$db = new PDO('sqlite:/var/www/cart.db');

	// enable foreign key support
	$db->query('PRAGMA foreign_keys = ON;');

	// FETCH_ASSOC:
	// Specifies that the fetch method shall return each row as an
	// array indexed by column name as returned in the corresponding
	// result set. If the result set contains multiple columns with
	// the same name, PDO::FETCH_ASSOC returns only a single value
	// per column name.
	$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

	return $db;
}

function ierg4210_cat_fetchall() {
    // DB manipulation
    global $db;
    $db = ierg4210_DB();
    $q = $db->prepare("SELECT * FROM categories LIMIT 100;");
    if ($q->execute())
        return $q->fetchAll();
}

// Since this form will take file upload, we use the tranditional (simpler) rather than AJAX form submission.
// Therefore, after handling the request (DB insert and file copy), this function then redirects back to admin.html
function ierg4210_prod_insert() {
    // input validation or sanitization

    // DB manipulation
    global $db;
    $db = ierg4210_DB();

    // TODO: complete the rest of the INSERT command
    if (!preg_match('/^\d*$/', $_POST['catid']))
        throw new Exception("invalid-catid");
    $_POST['catid'] = (int) int_validation($_POST['catid']);
    if (!preg_match("/^[A-Za-z0-9 -']+$/", $_POST['name']))
        throw new Exception("invalid-name");
    if (!preg_match('/^[\d\.]+$/', $_POST['price']))
        throw new Exception("invalid-price");
    if (!preg_match("/^[A-Za-z0-9 ''‘’,-:.?!;()]+$/", string_validation($_POST['description'])))
        throw new Exception("invalid-description");
    if (!preg_match('/^[\d]+$/', $_POST['inventory']))
        throw new Exception("invalid-inventory");

    $sql="INSERT INTO PRODUCTS (CATID, NAME, PRICE, DESCRIPTION, INVENTORY) VALUES (?, ?, ?, ?, ?)";
    $q = $db->prepare($sql);

    $a = $_FILES["file"]["type"];
    // Copy the uploaded file to a folder which can be publicly accessible at incl/img/[pid].jpg
    if (($_FILES["file"]["error"] == 0)
        && ($_FILES["file"]["type"] == "image/jpeg" || $_FILES["file"]["type"] == "image/png" || $_FILES["file"]["type"] == "image/gif")
        && ((mime_content_type($_FILES["file"]["tmp_name"]) == "image/jpeg") || (mime_content_type($_FILES["file"]["tmp_name"]) == "image/png") || (mime_content_type($_FILES["file"]["tmp_name"]) == "image/gif"))
        && ($_FILES["file"]["size"] < 10000000) ) {

        $catid = int_validation($_POST["catid"]);
        $name = string_validation($_POST["name"]);
        $price = string_validation($_POST["price"]);
        $desc = string_validation($_POST["description"]);
        $inventory = string_validation($_POST["inventory"]);
        $sql="INSERT INTO PRODUCTS (CATID, NAME, PRICE, DESCRIPTION, INVENTORY) VALUES (?, ?, ?, ?, ?);";
        $q = $db->prepare($sql);
        $q->bindParam(1, $catid, PDO::PARAM_INT|PDO::PARAM_INPUT_OUTPUT, 10);
        $q->bindParam(2, $name, PDO::PARAM_STR|PDO::PARAM_INPUT_OUTPUT, 50);
        $q->bindParam(3, $price, PDO::PARAM_STR|PDO::PARAM_INPUT_OUTPUT, 10);
        $q->bindParam(4, $desc, PDO::PARAM_STR|PDO::PARAM_INPUT_OUTPUT, 250);
        $q->bindParam(5, $inventory, PDO::PARAM_INT|PDO::PARAM_INPUT_OUTPUT, 10);
        $q->execute();
        $lastId = $db->lastInsertId();



        // Note: Take care of the permission of destination folder (hints: current user is apache)
        $upload = move_uploaded_file($_FILES["file"]["tmp_name"], "/var/www/html/admin/lib/images/" . $lastId . ".jpg");
      
        
        if ($upload) {
            // redirect back to original page; you may comment it during debug


            header('Location: admin.php');
            exit();
        }
    }
    // Only an invalid file will result in the execution below
    // To replace the content-type header which was json and output an error message
    header('Content-Type: text/html; charset=utf-8');
    echo 'Invalid file detected. <br/><a href="javascript:history.back();">Back to admin panel.</a>';
    exit();

}

// TODO: add other functions here to make the whole application complete
function ierg4210_cat_insert() {
    global $db;
    $db = ierg4210_DB();

    if (!preg_match('/^[\w\- ]+$/', $_POST['name'])) 
        throw new Exception("invalid-name");
    $name = string_validation($_POST["name"]);

    $sql="INSERT INTO CATEGORIES (NAME) VALUES (?);";
    $q = $db->prepare($sql);
    $q->bindParam(1, $name, PDO::PARAM_STR|PDO::PARAM_INPUT_OUTPUT, 50);
    $q->execute();

    header('Location: admin.php');
    exit();
}
function ierg4210_cat_edit(){
    global $db;
    $db = ierg4210_DB();

    if (!preg_match('/^\d*$/', $_POST['catid']))
        throw new Exception("invalid-catid");
    $_POST['catid'] = (int) $_POST['catid'];
    if (!preg_match("/^[A-Za-z0-9 -']+$/", $_POST['name'])) 
        throw new Exception("invalid-name");
    $catid = int_validation($_POST["catid"]);
    $name = string_validation($_POST["name"]);

    $sql="UPDATE CATEGORIES SET NAME = ? WHERE CATID = ?;";
    $q = $db->prepare($sql);
    $q->bindParam(1, $name, PDO::PARAM_STR|PDO::PARAM_INPUT_OUTPUT, 50);
    $q->bindParam(2, $catid, PDO::PARAM_INT|PDO::PARAM_INPUT_OUTPUT, 10);
    $q->execute();

    header('Location: admin.php');
    exit();
}
function ierg4210_cat_delete(){
    global $db;
    $db = ierg4210_DB();

    if (!preg_match('/^\d*$/', $_POST['catid']))
        throw new Exception("invalid-catid");
    $catid = int_validation((int) $_POST['catid']);

    $sql1="DELETE FROM PRODUCTS WHERE CATID = ?;";
    $q1 = $db->prepare($sql1);
    $q1->bindParam(1, $catid, PDO::PARAM_INT|PDO::PARAM_INPUT_OUTPUT, 10);
    $q1->execute();
    
    $sql2="DELETE FROM CATEGORIES WHERE CATID = ?;";
    $q2 = $db->prepare($sql2);
    $q2->bindParam(1, $catid, PDO::PARAM_INT|PDO::PARAM_INPUT_OUTPUT, 10);
    $q2->execute();

    header('Location: admin.php');
    exit();
}
function ierg4210_prod_delete_by_catid(){}
function ierg4210_prod_fetchAll(){
    global $db;
    $db = ierg4210_DB();
    $q = $db->prepare("SELECT * FROM PRODUCTS LIMIT 100;");
    if ($q->execute())
        return $q->fetchAll();
}
function ierg4210_prod_fetchByCat($catid){
    global $db;
    $db = ierg4210_DB();
    $sql="SELECT * FROM PRODUCTS WHERE CATID = ?;";
    $q = $db->prepare($sql);
    $q->bindParam(1, int_validation($catid), PDO::PARAM_INT|PDO::PARAM_INPUT_OUTPUT, 10);
    if ($q->execute())
        return $q->fetchAll();
}
function ierg4210_prod_fetchOne($pid){
    global $db;
    $db = ierg4210_DB();
    $sql="SELECT * FROM PRODUCTS WHERE PID = ?;";
    $q = $db->prepare($sql);
    $q->bindParam(1, int_validation($pid), PDO::PARAM_INT|PDO::PARAM_INPUT_OUTPUT, 10);
    if ($q->execute())
        return $q->fetchAll();
}
function ierg4210_prod_edit(){
    global $db;
    $db = ierg4210_DB();

    if (!preg_match('/^\d*$/', $_POST['pid']))
        throw new Exception("invalid-pid");
    $pid = int_validation((int) $_POST['pid']);
    if (!preg_match('/^\d*$/', $_POST['catid']))
        throw new Exception("invalid-catid");
    $_POST['catid'] = int_validation((int) $_POST['catid']);
    if (!preg_match("/^[A-Za-z0-9 -']+$/", $_POST['name']))
        throw new Exception("invalid-name");
    if (!preg_match('/^[\d\.]+$/', $_POST['price']))
        throw new Exception("invalid-price");
    if (!preg_match("/^[A-Za-z0-9 ''‘’,-:.?!;()]+$/", string_validation($_POST['description'])))
        throw new Exception("invalid-description");
    if (!preg_match('/^[\d]+$/', $_POST['inventory']))
        throw new Exception("invalid-inventory");

    $sql="INSERT INTO PRODUCTS (CATID, NAME, PRICE, DESCRIPTION, INVENTORY) VALUES (?, ?, ?, ?, ?)";
    $q = $db->prepare($sql);

    $a = $_FILES["file"]["type"];
      
    // Copy the uploaded file to a folder which can be publicly accessible at incl/img/[pid].jpg
    if (($_FILES["file"]["error"] == 0)
        && ($_FILES["file"]["type"] == "image/jpeg" || $_FILES["file"]["type"] == "image/png" || $_FILES["file"]["type"] == "image/gif")
        && ((mime_content_type($_FILES["file"]["tmp_name"]) == "image/jpeg") || (mime_content_type($_FILES["file"]["tmp_name"]) == "image/png") || (mime_content_type($_FILES["file"]["tmp_name"]) == "image/gif"))
        && ($_FILES["file"]["size"] < 10000000) ) {
        $catid = int_validation($_POST["catid"]);
        $name = string_validation($_POST["name"]);
        $price = string_validation($_POST["price"]);
        $desc = string_validation($_POST["description"]);
        $inventory = string_validation($_POST["inventory"]);
        $sql="UPDATE PRODUCTS SET CATID = ?, NAME = ?, PRICE = ?, DESCRIPTION = ?, INVENTORY = ? WHERE PID = ?;";
        //$sql="INSERT INTO PRODUCTS (CATID, NAME, PRICE, DESCRIPTION, INVENTORY) VALUES (?, ?, ?, ?, ?);";
        $q = $db->prepare($sql);
        $q->bindParam(1, $catid, PDO::PARAM_INT|PDO::PARAM_INPUT_OUTPUT, 10);
        $q->bindParam(2, $name, PDO::PARAM_STR|PDO::PARAM_INPUT_OUTPUT, 50);
        $q->bindParam(3, $price, PDO::PARAM_STR|PDO::PARAM_INPUT_OUTPUT, 10);
        $q->bindParam(4, $desc, PDO::PARAM_STR|PDO::PARAM_INPUT_OUTPUT, 250);
        $q->bindParam(5, $inventory, PDO::PARAM_INT|PDO::PARAM_INPUT_OUTPUT, 10);
        $q->bindParam(6, $pid, PDO::PARAM_INT|PDO::PARAM_INPUT_OUTPUT, 10);
        $q->execute();
        $lastId = $pid;


        unlink("/var/www/html/admin/lib/images/" . $pid . ".jpg");
        $upload = move_uploaded_file($_FILES["file"]["tmp_name"], "/var/www/html/admin/lib/images/" . $pid . ".jpg");
      
        
        if ($upload) {
            // redirect back to original page; you may comment it during debug
            header('Location: admin.php');
            exit();
        }
    }
    // Only an invalid file will result in the execution below
    // To replace the content-type header which was json and output an error message
    header('Content-Type: text/html; charset=utf-8');
    echo 'Invalid file detected. <br/><a href="javascript:history.back();">Back to admin panel.</a>';
    exit();
}
function ierg4210_prod_delete(){
    global $db;
    $db = ierg4210_DB();

    if (!preg_match('/^\d*$/', $_POST['pid']))
        throw new Exception("invalid-pid");
    $pid = int_validation((int) $_POST['pid']);

    $sql1="DELETE FROM PRODUCTS WHERE PID = ?;";
    $q1 = $db->prepare($sql1);
    $q1->bindParam(1, $pid, PDO::PARAM_INT|PDO::PARAM_INPUT_OUTPUT, 10);
    $q1->execute();

    header('Location: admin.php');
    exit();
}

function ierg4210_login() {
    global $db;
    $db = ierg4210_DB();
    
    if (empty($_POST['username'])||empty($_POST['password'])
    || !preg_match("/^[\w=+\-\/][\w='+\-\/\.]*@[\w\-]+(\.[\w\-]+)*(\.[\w]{2,6})$/", $_POST['username'])
    || !preg_match("/^[\w@#$%!\^\&\*\-]+$/", $_POST['password'])){
        throw new Exception("Wrong Credentials");
    } 

    $username=string_validation($_POST['username']);
    $password=string_validation($_POST['password']);
    $ac=ierg4210_account_fetchall($username);
    $ok=0;
    if(($username==email_validation($ac["email"]))&&($ac['password']==hash_hmac('sha256', $password, $ac['salt']))){
        $exp = time() + 3600 * 24 * 3;
        $token = array('em'=>email_validation($ac['email']), 'exp'=>$exp, 'k'=> hash_hmac('sha256', $exp.$ac['password'], $ac['salt']));
        setcookie('auth', json_encode($token), $exp, "/", "secure.s49.ierg4210.ie.cuhk.edu.hk", true, true);
        $_SESSION['auth'] = $token;
        session_regenerate_id();

        if ($ac['flag']==1) header('Location: admin.php', true, 302);
        if ($ac['flag']==0) header('Location: ../', true, 302);
    }else {
        throw new Exception('Wrong Credentials');
    }
    exit();
}

function ierg4210_register() {
    global $db;
    $db = ierg4210_DB();

    if (empty($_POST['username'])||empty($_POST['password'])||empty($_POST['repassword'])
    || !preg_match("/^[\w=+\-\/][\w='+\-\/\.]*@[\w\-]+(\.[\w\-]+)*(\.[\w]{2,6})$/", $_POST['username'])
    || !preg_match("/^[\w@#$%!\^\&\*\-]+$/", $_POST['password'])
    || !preg_match("/^[\w@#$%!\^\&\*\-]+$/", $_POST['repassword'])){
        throw new Exception("Invalid input");
    } 

    if (string_validation($_POST['password'])!=string_validation($_POST['repassword'])){
        throw new Exception("Please re-enter a same password!");
    }
    $username=string_validation($_POST['username']);
    $salt = mt_rand(); 
    $password=hash_hmac('sha256', string_validation($_POST['password']), $salt);
    $type=0;

    $sql="INSERT INTO account (email, salt, password, flag) VALUES (?, ?, ?, ?);";
    $q = $db->prepare($sql);
    $q->bindParam(1, $username, PDO::PARAM_STR|PDO::PARAM_INPUT_OUTPUT, 50);
    $q->bindParam(2, $salt, PDO::PARAM_INT|PDO::PARAM_INPUT_OUTPUT, 30);
    $q->bindParam(3, $password, PDO::PARAM_STR|PDO::PARAM_INPUT_OUTPUT, 250);
    $q->bindParam(4, $type, PDO::PARAM_INT|PDO::PARAM_INPUT_OUTPUT, 1);
    $q->execute();


    header('Location: login.php');
    exit();
}
function ierg4210_account_fetchall($username) {
    // DB manipulation
    global $db;
    $db = ierg4210_DB();
    $q = $db->prepare("SELECT * FROM account WHERE email = ?;");
    $q->bindParam(1, email_validation($username));
    if ($q->execute())
        return $q->fetch();
}
function ierg4210_change() {
    global $db;
    $db = ierg4210_DB();

    if (empty($_POST['username'])||empty($_POST['password'])||empty($_POST['newpassword'])||empty($_POST['renewpassword'])
    || !preg_match("/^[\w=+\-\/][\w='+\-\/\.]*@[\w\-]+(\.[\w\-]+)*(\.[\w]{2,6})$/", $_POST['username'])
    || !preg_match("/^[\w@#$%!\^\&\*\-]+$/", $_POST['password'])
    || !preg_match("/^[\w@#$%!\^\&\*\-]+$/", $_POST['newpassword'])
    || !preg_match("/^[\w@#$%!\^\&\*\-]+$/", $_POST['renewpassword'])){
        throw new Exception("Invalid input");
    } 
    if ($_POST['newpassword']!=$_POST['renewpassword']){
        throw new Exception("Please re-enter a same new password!");
    }
    
    if (($_POST['newpassword']==$_POST['password'])||($_POST['renewpassword']==$_POST['password'])){
        throw new Exception("The new password should not be the same as the previous one!");
    }
    $username=string_validation($_POST['username']);
    $password=string_validation($_POST['password']);
    $ac=ierg4210_account_fetchall($username);
    if(($username==email_validation($ac["email"]))&&($ac['password']==hash_hmac('sha256', $password, $ac['salt']))){
        $newpassword=hash_hmac('sha256', $_POST['newpassword'], $ac['salt']);
        

        $sql="UPDATE account SET password = ? WHERE userid = ?;";
        $q = $db->prepare($sql);
        $q->bindParam(1, $newpassword, PDO::PARAM_STR|PDO::PARAM_INPUT_OUTPUT, 250);
        $q->bindParam(2, int_validation($ac['userid']), PDO::PARAM_INT|PDO::PARAM_INPUT_OUTPUT, 10);
        $q->execute();

    }
    header('Location: login.php');
    exit();
}

function auth() {
    if (!empty($_SESSION['auth']))
    // return existing session data if exists
        return email_validation($_SESSION['auth']['em']);
    // otherwise construct one after authentication
    if (!empty($_COOKIE['auth'])) {
        if ($t = json_decode($_COOKIE['auth'], true)) {
            if (time() > $t['exp']) return false;
            global $db; // validate if token matches our record
            $db = ierg4210_DB();
            $q = $db->prepare('SELECT salt, password FROM account WHERE email = ?');
            if ($q->execute(array(email_validation($t['em'])))
            && ($r = $q->fetch())
            && $t['k'] == hash_hmac('sha256',$t['exp'] . $r['password'], $r['salt'])) {
                $_SESSION['auth'] = $_COOKIE['auth'];
                return email_validation($t['em']); 
            }
            // if code arrives here, then authentication failed
            return false;
        } 
    } 
    return false;
}

function ierg4210_logout() {
    unset($_COOKIE['auth']);
    $res = setcookie('auth', "", time() - 3600 * 24 * 3, "/", "secure.s49.ierg4210.ie.cuhk.edu.hk", true, true);

    unset($_SESSION['auth']);
    header('Location: ../',true,302);
    exit();
}

function csrf_getNonce($action){
    $nonce = mt_rand() . mt_rand();
    if (!isset($_SESSION['csrf_nonce']))
        $_SESSION['csrf_nonce'] = array();
    $_SESSION['csrf_nonce'][string_validation($action)] = $nonce;
    return $nonce;
}

function csrf_verifyNonce($action, $receivedNonce){
    $action=string_validation($action);
    $receivedNonce=string_validation($receivedNonce);
    if (isset($receivedNonce) && $_SESSION['csrf_nonce'][$action] == $receivedNonce) {
        if ($_SESSION['auth']==null)
            unset($_SESSION['csrf_nonce'][$action]);
        return true;
    }
    throw new Exception('csrf-attack');
}

function admin_auth() {
    if (!empty($_SESSION['auth'])){
        global $db; // validate if token matches our record
        $db = ierg4210_DB();
        $q = $db->prepare('SELECT salt, password, flag FROM account WHERE email = ?');
        if ($q->execute(array($_SESSION['auth']['em']))&& ($r = $q->fetch())){
           if($r['flag']==1) {
               return email_validation($_SESSION['auth']['em']);
           }
           else if($r['flag']==0) return false;
        }

        
    }
        
    // otherwise construct one after authentication
    if (!empty($_COOKIE['auth'])) {
        if ($t = json_decode($_COOKIE['auth'], true)) {
            if (time() > $t['exp']) return false;
            global $db; // validate if token matches our record
            $db = ierg4210_DB();
            $q = $db->prepare('SELECT salt, password, flag FROM account WHERE email = ?');
            if ($q->execute(array(email_validation($t['em'])))
            && ($r = $q->fetch())
            && $t['k'] == hash_hmac('sha256',$t['exp'] . $r['password'], $r['salt'])) {
                $_SESSION['auth'] = $_COOKIE['auth'];
                if($r['flag']==1) {
                    return email_validation($t['em']); 
                }
                else if($r['flag']==0) return false;
            }
            // if code arrives here, then authentication failed
            return false;
        } 
    } 
    return false;
}

function int_validation($input){
    $input=htmlspecialchars($input);
    $sanitized_input = filter_var($input,FILTER_SANITIZE_NUMBER_INT);
    if (filter_var($sanitized_input,FILTER_VALIDATE_INT)){
        return $sanitized_input;
    }
    else
    {

        header('Content-Type: text/html; charset=utf-8');
        echo $input;
        echo 'Int input not valid<br/><a href="javascript:history.back();">Back to previous page.</a>';
        exit();
    }
}

function string_validation($input){
    $input=htmlspecialchars($input);
    $sanitized_input = filter_var($input, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
    return $sanitized_input;
}

function email_validation($input){
    $input=htmlspecialchars($input);
    $sanitized_input = filter_var($input,FILTER_SANITIZE_EMAIL);
    if (filter_var($sanitized_input,FILTER_VALIDATE_EMAIL)){
        return $sanitized_input;
    }
    else
    {
        header('Content-Type: text/html; charset=utf-8');
        echo $input;
        echo 'Email input not valid<br/><a href="javascript:history.back();">Back to previous page.</a>';
        exit();
    }
}


function ierg4210_order_fetchall() {
    // DB manipulation
    global $db;
    $db = ierg4210_DB();
    $q = $db->prepare("SELECT * FROM ORDERS LIMIT 100;");
    if ($q->execute())
        return $q->fetchAll();
}

function ierg4210_update_orders($uuid, $txn_id, $status){
	global $db;
    $db = ierg4210_DB();

	//UPDATE ORDERS SET PATMENTSTATUS='Completed' WHERE UUID=7;
	//$sql="UPDATE ORDERS SET TXNID=?, PATMENTSTATUS=? WHERE UUID=?;";
	$sql="UPDATE ORDERS SET TXNID=?, PATMENTSTATUS=? WHERE UUID=?;";
    $q = $db->prepare($sql);
	$q->bindParam(1, $txn_id);
    $q->bindParam(2, $status);
	$q->bindParam(3, $uuid);
    $q->execute();
}

function ierg4210_order_fetchByUser($username) {
    // DB manipulation
    global $db;
    $db = ierg4210_DB();
    $q = $db->prepare("SELECT * FROM ORDERS WHERE USER=?;");
    $q->bindParam(1, $username);
    if ($q->execute())
        return $q->fetchAll();
}