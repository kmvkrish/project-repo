<?php
session_start();
if(!isset($_SESSION['username']) && !isset($_SESSION['password'])){
    header('location:../login.php');
}
$master = $_SESSION['username'];
$title = isset($_GET['title'])?$_GET['title']:null;
require_once("includes/dbconnect.php");
function checkStatus($conn,$title){
    $status_msg = $status = "";
    try{
        $sql = $conn->prepare("SELECT * FROM projects WHERE master = :master AND title = :title LIMIT 1");
        $sql->execute(array(':master' => $_SESSION['username'],':title' => $title));
        if($sql->rowCount() > 0){
            while($result = $sql->fetch(PDO::FETCH_ASSOC)){
                if($result['published'] == 'yes'){
                    return "ok";
                }else{
                    return "notok";
                }
            }
        }else{
             return "No repository found on this name.Please create one to continue";
        }
    }catch(PDOException $e){
        echo $e->getMessage();
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
        <script src="js/jquery.min.js"></script>
        <style>
            body{
                background:#DCDCDC;   
            }
            textarea,textarea:focus{
            padding:5px;
            height:40px;
            width:98%;
            display:block;
            }
            form > .form-group > input{
            padding:10px;
            margin-top:30px;
            }
            .form-group > .span {
            margin-left: 1.5%;
            color: tomato;
            font-weight:bold;
            text-decoration: none;
            }
            input[type="email"],input[type="password"] ,input[type="text"],input[type="url"]{
            padding:12px;
            width: 100%;
            transition: 0.3s;
            -o-box-sizing: border-box;
            -moz-box-sizing: border-box;
            -webkit-box-sizing: border-box;
            }
            #matchList li{
                margin-top:10px;
                cursor:pointer;
            }
            .error{
                border:solid red;
            }
            .content{
                background:#fff;
                padding:10px auto;
                border:3px solid white;
                border-radius:5px;
                box-shadow:0px 10px 0px white;
            }
            label > input, label > textarea{
                margin-top:10px;
            }
            .content > div{
                margin-top:10px;
                margin-left:10px;
                margin-right:10px;
                margin-bottom:1.5em;
            }
            .content a{
                text-decoration:none;
                font-weight:bold;
            }
            .content .heading{
                border-bottom:1px solid lightgrey;
                margin-bottom:1.5em;
            }
            .content .body{
                margin-bottom:1.5em;
                border-bottom:1px solid lightgrey;
            }
            .contet .footer{
                margin-bottom:15px;
                margin-left:10px;
            }
            .footer a{
                margin-left:10px;
                color:black;
            }
            #radiolabel{
                display:inline-block;
                background:#2980b9;
                color:white;
                padding:4px 12px;
            }
            input[type=radio]{
                width:0;
                height:0;
            }
            input[type=radio]:checked + #radiolabel{
                background:white;
                color:#2980b9;
            }
            .custom-upload {
                background-color: #008000;
                border: 1px solid #006400;
                border-radius: 4px;
                cursor: pointer;
                color: #fff;
                padding: 4px 10px;
            }
            .custom-upload input {
                left: -9999px; 
                position: absolute;
            }
            progress{
                background-color: #f3f3f3;
                border: 0;
                height: 18px;
                border-radius: 3px;
            }
        </style>
        <script>
            function _(el){
                return document.getElementById(el);
            }
            function uploadFile(){
                var file = _("file").files[0];
                // alert(file.name+" | "+file.size+" | "+file.type);
                var formdata = new FormData();
                formdata.append("file", file);
                formdata.append("master","<?php echo $master;?>");
                formdata.append("title","<?php echo $title;?>");
                var ajax = new XMLHttpRequest();
                ajax.upload.addEventListener("progress", progressHandler, false);
                ajax.addEventListener("load", completeHandler, false);
                ajax.addEventListener("error", errorHandler, false);
                ajax.addEventListener("abort", abortHandler, false);
                ajax.open("POST", "uploadfile.php");
                ajax.send(formdata);
            }
            function progressHandler(event){
                var percent = (event.loaded / event.total) * 100;
                _("progressBar").value = Math.round(percent);
                _("status").innerHTML = Math.round(percent)+"% uploaded... please wait";
            }
            function completeHandler(event){
                _("status").innerHTML = event.target.responseText;
                _("progressBar").value = 0;
                window.location.href = "project.php?url="+"<?php echo $master;?>";
            }
            function errorHandler(event){
                _("status").innerHTML = "Upload Failed";
            }
            function abortHandler(event){
                _("status").innerHTML = "Upload Aborted";
            }
        </script>
        <script>
            $(document).ready(function(){
                $('#logout').on('click',function(){
                    window.location.href="../logout.php";
                });
                $(function(){
                    var $element = $('#code').get(0);
                    $element.addEventListener('keyup',function(){
                        this.style.overflow = "hidden";
                        this.style.height = 0;
                        this.style.height = this.scrollHeight + 'px';
                    },false);
                });
                function enableTab(id) {
                    var el = document.getElementById(id);
                    el.onkeydown = function(e) {
                        if (e.keyCode === 9) { 
                            var val = this.value,
                                start = this.selectionStart,
                                end = this.selectionEnd;
                            this.value = val.substring(0, start) + '\t' + val.substring(end);
                            this.selectionStart = this.selectionEnd = start + 1;
                            return false;
                        }
                    };
                }
                enableTab('code');
                $('input[type="radio"]').on('click',function(){
                    var type = $(this).val();
                    switch(type){
                        case "singlefile":
                                    $('#codeform').show();
                                    $('#uploadform').hide();
                                    break;
                        case "morefiles":
                                    $('#codeform').hide();
                                    $('#uploadform').show();
                                    break;
                        default:"";
                                break;
                    }
                });
            });
        </script>
    </head>
    <body>
        <nav role="navigation" >
            <label class="navbar-control">&#9776;</label>
            <a href="./" class="brand">PROJECT-HUB</a>
            <ul class="clearfix">
                <li><a href="../blog/">Blog</a></li>
                <li><a href="../<?php echo $_SESSION['username'];?>">Profile</a></li>
                <li><a href="../settings.php">Settings</a></li>
                <li><a href="Javascript:void()" id="logout">Logout</a></li>
            </ul>
        </nav>
        <div class="section group">
            <div class="col span_2_of_12"></div>
            <div class="col span_8_of_12">
                <?php 
                    $status = checkStatus($con,$title);
                    switch($status){
                        case "ok":
                echo "This repository is already published. Do you want to edit?";
                                    break;
                        case "notok":
                ?>
                <div class="form-group">
                    <input type="radio" name="repo" value="singlefile" id="one" checked/>
                    <label for="one" id="radiolabel">Single File</label>
                    <input type="radio" name="repo" value="morefiles" id="more"/>
                    <label for="more" id="radiolabel">ZIP File</label>
                </div><br/>
                <div class="content">
                    <div class="body">
                        <form method="post" id="codeform">
                            <div class="form-group">
                                <label for="filename">Choose a File Name<input type="text" id="filename" name="filename" /></label>
                            </div><br/>
                            <div class="form-group">
                                <label for="code">Type your code here with proper indentation<textarea id="code" placeholder="your code here..." ></textarea></label>
                            </div><br/>
                            <input type="button" class="pin btn" id="post" value="Submit"/>
                        </form>
                        <form method="post" id="uploadform" style="display:none;" enctype="multipart/form-data">
                            <div class="form-group" align="center">
                                <br/>
                                <label class="custom-upload"><input type="file" id="file" name="file" />Select File</label>
                            </div><br/><br/>
                            <progress id="progressBar" value="0" max="100" style="width:100%;padding:10px 5px;"></progress>
                            <h3 id="status"></h3><br/>
                            <input type="button" class="pin btn" id="upload" value="Submit" onClick="Javascript:uploadFile();"/>
                        </form>
                    </div>
                </div>
                <?php
                                    break;
                        default:
                echo $status;
                                break;
                    }
                ?>
            </div>
            <div class="col span_2_of_12"></div>
        </div>
        <script src="js/jquery.min.js"></script>
        <script>
            $('#post').on('click',function(){
                $(this).attr('disabled','disabled').val('Processing...');
                var filename = $('#filename').val();
                var code = $('#code').val();
                var title = "<?php echo $title;?>";
                if(filename != '' && code != ''){
                    $.ajax({
                        type:"POST",
                        url:"createfile.php",
                        data:{"title":title,"filename":filename,"code":code},
                        cache:false,
                        success:function(data){
                            if(data == "ok"){
                                $('#code').val('');
                                $('#filename').val('');
                                $(this).removeAttr('disabled').val('Submit');
                            }else{
                                alert("Some error occured or "+ data);
                                $(this).removeAttr('disabled').val('Submit');
                            }
                        }
                    });
                }else{
                    $(this).removeAttr('disabled').val('Submit');
                }
            });
        </script>
    </body>
</html>