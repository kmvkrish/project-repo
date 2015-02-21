<?php
session_start();
$files = array();
$content = '';
$message = $count = $master = $project = $category = $bc = $created = $description = '';
$url = isset($_GET['url'])?$_GET['url']:'';
if($url != ''){
    $fullpath = explode('/',$url);
    $master = $fullpath[0];
    $project = isset($fullpath[1])?$fullpath[1]:'';
}
require_once("includes/dbconnect.php");
function listDir($dir){
    $files = array();
    $links = array();
    if(file_exists($dir)){
        if(count(scandir($dir)) > 2){
            $dh = scandir($dir);
            foreach($dh as $folder){
                if($folder != '.' && $folder != '..'){
                    if(is_dir($dir.'/'.$folder)){
                        $files[] = $folder;
                        $links[$folder] = "project.php?url=".$dir.'/'.$folder;
                    }else{
                        $files[] = $folder;
                        $links[$folder] = "project.php?url=".$dir.'/'.$folder;
                    }
                }
            }
        }
        return [$files,$links];
    }else{
        return "";   
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Project-Hub</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="css/button.css" rel="stylesheet"/>
        <link href="css/font-awesome.css" rel="stylesheet"/>
        <link href="css/grid.css" rel="stylesheet"/>
        <link href="css/reset.css" rel="stylesheet"/>
        <link href="css/navigation.css" rel="stylesheet"/>
        <link rel="stylesheet" href="css/timeline.css"/>
        <link rel="stylesheet" href="css/table.css"/>
        <link rel="stylesheet" href="css/highlight.xcode.css"/>
        <script src="js/jquery.min.js"></script>
        <script src="js/highlight.min.js"></script>
        <style>
            .content{
            background:#fff;
            /*padding:10px auto;*/
            border:3px solid white;
            border-radius:none;
            box-shadow:0px 10px 0px white;
            }
            .content a{
            text-decoration:none;
            font-size:16px;
            }
            .content .heading{
            border-bottom:1px dashed gray;
            margin-bottom:10px;
            }
            .content .body{
            margin-bottom:30px;
            margin-left:10px;
            margin-right:10px;
            word-wrap:break-word;
            border-bottom:1px dashed grey;
            }
            .body a{
                font-size:24px;   
            }
            .contet .footer{
            margin-bottom:10px;
            margin-left:10px;
            }
            .footer a{
            margin-left:10px;
            font-size:12px;
            }
            .files{
                display:block;
                width:100%;
                height:auto;
                text-align:middle;
            }
            .files > div{
                border-bottom:1px dotted #23f4c2;
                height:40px;
                line-height:50px;
            }
            .files > div:hover{
                border:0;
                background:lightgray;
            }
            .file-icon,.file-name {
                margin:10px;
            }
            .breadcrumb a{
                margin:4px;
                font-size:20px;
            }
        </style>
    </head>
    <body>
        
        <nav role="navigation" >
            <label class="navbar-control">&#9776;</label>
            <a href="./" class="brand">PROJECT-REPO</a>
            <ul class="clearfix">
                <li><a href="../blog/">Blog</a></li>
                <?php
                    if(!isset($_SESSION['username']) && !isset($_SESSION['password'])){
                        echo '<li><a href="../login.php">Sign In</a></li>';
                    }else{
                ?>
                        <li><a href="../<?php echo $_SESSION['username'];?>">Profile</a></li>  
                        <li><a href="./settings.php">Settings</a></li>
                        <li><a href="./notification.php" title="You have no unread notifications"><i class="fa fa-globe fa-lg"></i></a></li>
                        <li><a href="Javascript:void()" id="logout" title="Logout">Logout</a></li>
                <?php
                    }
                ?>
            </ul>
        </nav>
        <div class="section group">
            <div class="col span_3_of_12"></div>
            <div class="col span_6_of_12">
                <div class="breadcrumb">
                    <?php 
                        if($url != ''){
                           $b = '';
                           $links = explode('/',rtrim($url,'/'));
                            foreach($links as $l){
                                $b .= $l;
                                if($url == $b){
                                    echo $l;
                                }else{
                                    echo "<a href='project.php?url=".$b."'>".$l."/</a>";
                                }
                                $b .= '/';
                            }
                        }
                    ?>
                </div><br/>
                <?php 
                    if($url != '' && $project == ''){
                        $sql = $con->prepare("SELECT * FROM projects WHERE master = :master AND published = 'yes' ORDER BY created DESC");
                        if($sql->execute(array(':master' => $master))){
                            if($sql->rowCount() > 0){
                                while($result = $sql->fetch(PDO::FETCH_ASSOC)){
                ?>
                            <div class="content">
                                <div class="body">
                                    <h3><a href="project.php?url=<?php echo $result['folder_path'];?>"><?php echo $result['title'];?></a></h3><br/><br/>
                                    <p>
                                        <?php echo $result['description'];?>
                                    </p>
                                </div>
                                <div class="footer">
                                    <i class="fa fa-clock-o"><?php echo date('Y M jS',strtotime($result['created']));?></i>
                                </div>
                            </div><br/><br/>
                <?php
                                }
                            }else{
                                echo "<div class='content'>";
                                    echo "<pre><code>No projects found for this user</code></pre>";
                                echo "</div>";
                            }
                        }
                    }else if($url != '' && $project != ''){
                        $sql = $con->prepare("SELECT * FROM projects WHERE master = :master AND title = :project AND published = 'yes'");
                        if($sql->execute(array(':master' => $master,':project' => $project))){
                            if($sql->rowCount() > 0 ){
                                $file_links = array();
                                if(is_dir($url)){
                                    $file_links = listDir($url);
                                    if(!empty($file_links)){
                                        $files = $file_links[0];
                                        $links = $file_links[1];
                                        echo "<div class='files'>";
                                        foreach($files as $f){
                                            echo "<div>";
                                            foreach($links as $key => $l){
                                                if($f == $key){
                                                    if(is_dir($url.'/'.$key)){
                                                        echo "<span class='file-icon'><img src='images/directory.png'/></span>";
                                                    }else{
                                                        echo "<span class='file-icon'><img src='images/code.png'/></span>";
                                                    }
                                                    echo "<span class='file-name'>";
                                                    echo "<a href='".$l."'>".$key."</a>";
                                                    echo "</span>";
                                                }
                                            }
                                            echo "</div>";
                                        }
                                        echo "</div>";
                                    }
                                }else if(is_file($url)){
                                    if(strlen(file_get_contents($url)) > 0){
                                        echo "<pre><code>".nl2br(htmlentities(file_get_contents($url)))."</code></pre>";
                                    }else{
                                        echo "<pre><code>File is empty</code></pre>";
                                    }
                                }else{
                                    echo "<pre><code>The file extension is unknown.Try saving using context-menu</code></pre>";
                                }
                            }else{
                                echo "<pre><code>No projects found on this name</code></pre>";
                            }
                        }
                    }else{
                        header('location:./');
                    }
                ?>
            </div>
            <div class="col span_3_of_12"></div>
        </div>
        <script src="js/jquery.min.js"></script>
        <script>
            $(document).ready(function() {
                $('pre code').each(function(i, block) {
                    hljs.highlightBlock(block);
                });
            });
        </script>
    </body>
</html>