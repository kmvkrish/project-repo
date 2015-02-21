<?php
    session_start();
    date_default_timezone_set('Asia/Calcutta');
    require_once('includes/dbconnect.php');
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
        <script src="js/jquery.min.js"></script>
        <style>
            .content{background:#fff;padding:10px auto;border:3px solid #fff}.content > div{margin:10px 10px 1.5em}.content a{text-decoration:none;font-weight:500}.content .heading{border-bottom:1px solid #d3d3d3;margin-bottom:1.5em}.content .body{margin-bottom:1.5em;border-bottom:1px solid #d3d3d3}.contet .footer{margin-bottom:15px;margin-left:10px}.footer a{margin-left:10px}.media{padding:1em}.media .img{float:left;margin:0 10px 10px 0}.media-heading{font-size:1.4em}.error{border:solid red}.avatar{width:100%;height:100%}.active{border-bottom:3px solid #2980b9}
        </style>
        <script>
            $(document).ready(function(){
                $('#logout').on('click',function(){
                    window.location.href="../logout.php";
                });
            });
        </script>
        <script src="js/modernizr.js"></script>
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
                <li><a href="./addproject.php" title="Add New Project"><i class="fa fa-plus"></i></a></li>
                <li><a href="./settings.php">Settings</a></li>
                <li><a href="./notification.php" title="You have no unread notifications"><i class="fa fa-globe fa-lg"></i></a></li>
                <li><a href="Javascript:void()" id="logout" title="Logout">Logout</a></li>
                <?php
                    }
                ?>
            </ul>
        </nav>
        <?php 
            $sql = $con->prepare("SELECT * FROM projects WHERE published = 'yes' ORDER BY created  DESC");
            $sql->execute();
            if($sql->rowCount() > 0){
        ?>
                <section class="cd-container" id="cd-timeline">
        <?php
                while($result = $sql->fetch(PDO::FETCH_ASSOC)){
        ?>            
                    <div class="cd-timeline-block">
                        <div class="cd-timeline-img cd-picture">
                            <img src="images/<?php echo $result['category'];?>.png"/>
                        </div>
                        <div class="cd-timeline-content">
                            <h2><?php echo $result['title'];?></h2>
                            <p><?php echo $result['description'];?></p>
                            <a class="cd-read-more" href="./project.php?url=<?php echo $result['master'];?>/<?php echo $result['title'];?>">Read More</a>
                            <span class="cd-date"><?php echo date('Y M jS',strtotime($result['created']));?></span>
                        </div>
                    </div>
        <?php
                }      
        ?>
                </section>
        <?php
            }else{
        ?>
                <div class="section group">
                    <div class="col span_1_of_12"></div>
                    <div class="col span_10_of_12">
                        It seems there are no projects registered yet. Be the first one by adding one.
                        <p>
                            Click the <kbd> + </kbd> sign to add new project
                        </p>
                    </div>
                    <div class="col span_1_of_12"></div>
                </div>
        <?php
            }
        ?>
        <script></script>
    </body>
</html>