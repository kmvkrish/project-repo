<?php
session_start();
$master = $_SESSION['username'];
require_once("includes/dbconnect.php");
if(!empty($_POST) && isset($_POST)){
    $filename = $_POST['filename'];
    $title = $_POST['title'];
    $code = $_POST['code'];
    if(file_exists($master.'/'.$title)){
        $f = fopen($master.'/'.$title.'/'.$filename,"a+");
        if(fwrite($f,$code,strlen($code))){
            $sql = $con->prepare("UPDATE projects SET published = 'yes' WHERE master = :master AND title = :title");
            if($sql->execute(array(':master' => $master,':title' => $title))){
                echo "ok";
            }else{
                echo "Some error occured.";   
            }
        }else{
            echo "Could not write into file";
        }
    }else{
        echo "Could not locate the repository. Please try after sometime.";
    }
}else{
    echo "Only POST method is supported";
}