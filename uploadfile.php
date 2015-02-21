<?php
if(isset($_POST) && !empty($_POST)){
    
    require_once("includes/dbconnect.php");
    
    $fileName = $_FILES["file"]["name"]; // The file name
    $fileTmpLoc = $_FILES["file"]["tmp_name"]; // File in the PHP tmp folder
    $fileType = $_FILES["file"]["type"]; // The type of file it is

    $master = $_POST['master'];
    $title = $_POST['title'];
    $okay = '';
    $types = array('application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/x-compressed, application/octet-stream');
    $continue = strtolower(explode('.',$fileName)[1]) == 'zip'?true:false;
    
    if(!$continue){
        echo "Error:Please select .zip file";
        echo $fileType." is not valid";
        exit();
    }
    if (!$fileTmpLoc) { // if file not chosen
        echo "ERROR: Please browse for a file before clicking the upload button.";
        exit();
    }
    
    $targetpath = $master.'/'.$title.'/'.$fileName;
    
    if(move_uploaded_file($fileTmpLoc, $targetpath)){
        $zip = new ZipArchive();
        $x = $zip->open($targetpath);
        if($x === true){
            if($zip->extractTo($master.'/'.$title)){
                $sql = $con->prepare("UPDATE projects SET published= 'yes' WHERE master = :master AND title = :title AND published = 'no' AND folder_path = :path");
                if($sql->execute(array(':master' => $master, ':title' => $title,':path' => $master.'/'.$title))){
                    echo "$fileName upload is complete";
                }   
            }else{
                echo "Error during unpacking...Try after sometime";
                exit();
            }
            $zip->close();
            unlink($targetpath);
        }
        
    } else {
        echo "move_uploaded_file function failed";
    }
}else{
    echo "Only POST method is supported";
}
?>