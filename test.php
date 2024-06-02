<?php
    define("HOST", "localhost");
    define("USER", "root");
    define("PASS", "");
    define("DB", "cycle_shop_db");

    function db_connect(){
        $database = new mysqli(HOST, USER, PASS, DB);

        if($database->connect_errno){
            $msg = "DB connect failed: ";
            $msg .= $database->connect_error;
            $msg .= "(" . $database->connect_errno . ")";
            exit($msg);
        }else{
            return $database;
        }

    }

    function db_disconnect($database){
        if(isset($database)){
            $database->close();
        }
    }

    $db = db_connect();
    $sql = 'select * from bicycles';
    $result = $db->query($sql);
    $row = $result->fetch_assoc();
    $result->free();
    echo "Brand: " . $row['brand'];
    db_disconnect($db);
?>
