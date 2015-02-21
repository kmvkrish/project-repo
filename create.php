<?php

session_start();
function create_dir($root,$dir){
    if(file_exists($root)){
        if(file_exists($dir)){
            return false;
        }else{
            mkdir($root.DIRECTORY_SEPARATOR.$dir);
            return true;
        }
    }else{
        if(mkdir($root)){
            mkdir($root.DIRECTORY_SEPARATOR.$dir);
            return true;
        }else{
            return false;   
        }
    }
}
function make_path($dir,$subdir){
    if(file_exists($dir.DIRECTORY_SEPARATOR.$subdir)){
        return $dir.DIRECTORY_SEPARATOR.$subdir;
    }else{
        if(create_dir($dir,$subdir)){
            return $dir.DIRECTORY_SEPARATOR.$subdir;
        }
    }
}
$okay = '';
date_default_timezone_set('Asia/Calcutta');
if(isset($_POST) && !empty($_POST)){
    $master = $_SESSION['username'];
    $title = $_POST['title'];
    $category = $_POST['category'];
    $desc = strlen($_POST['description'] > 0)?$_POST['description']:"Another project on $category";
    include("includes/dbconnect.php");
    try{
        
        $sql = $con->prepare("SELECT * FROM projects WHERE master = :master AND title = :title");
        if($sql->execute(array(':master' => $master,':title' => $title))){
            if($sql->rowCount() > 0){
                echo "Project already exists. Choose a different name.";
            }else{
                if(create_dir($master,$title) == true){
                    $sql = $con->prepare("INSERT INTO projects(pid,title,description,master,folder_path,category,created,published) VALUES('',:title,:description,:master,:folder_path,:category,:created,'no')");
                    $path = file_exists($master.'/'.$title)?$master.'/'.$title:null;
                    if($path != null){
                        $sql->execute(array(':title' => $title,
                                        ':description' => nl2br(htmlentities($desc)),
                                        ':master' => $master,
                                        ':folder_path' => $path,
                                        ':category' => $category,
                                        ':created' => date('Y-m-d')
                                       ));   
                    }
                    if($con->lastInsertId()){
                        echo "ok";
                    }else{
                        echo "Some error occured.Please try after sometime";
                    }
                }
            }
        }
        
        
    }catch(PDOException $e){
        echo $e->getMessage();
    }
}else{
    echo "ONLY POST METHOD IS SUPPORTED.";
}

?>