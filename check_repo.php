<?php
    session_start();
if(isset($_POST) && !empty($_POST)){
    $title = $_POST['title'];
    require_once("includes/dbconnect.php");
    try{
        $sql = $con->prepare("SELECT * FROM projects WHERE master = :master AND title = :title AND (published = 'yes' OR published = 'no')");
        $sql->execute(array(':master' => $_SESSION['username'],':title' => $title));
        if($sql->rowCount() > 0 && file_exists($_SESSION['username'].'/'.$title)){
            echo true;
        }else{
            echo false;
        }
    }catch(PDOException $e){
        echo $e->getMessage();
    }
}