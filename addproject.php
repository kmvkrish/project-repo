<?php
    session_start();
    if(!isset($_SESSION['username']) && !isset($_SESSION['password'])){
        header('location:../login.php');   
    }else{
        include('includes/dbconnect.php');
        date_default_timezone_set('Asia/Calcutta');
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
            .morecontent span{display:none}.submenu-dd{float:right;position:relative;left:20px}.submenu{display:none;width:auto;height:auto;color:#fff;background:#2bc0d8;position:absolute}.submenu li{list-style:none;display:block}.span_7_of_12 > div{margin-bottom:1.5em}.content{background:#fff;padding:10px auto;border:3px solid #fff}.content > div{margin:10px 10px 1.5em}.content a{text-decoration:none;font-weight:500}.content .heading{border-bottom:1px solid #d3d3d3;margin-bottom:1.5em}.content .body{margin-bottom:1.5em;border-bottom:1px solid #d3d3d3}.contet .footer{margin-bottom:15px;margin-left:10px}.footer a{margin-left:10px}form > .form-group > input{padding:10px;margin-top:30px}input[type="email"],input[type="password"]{padding:12px;width:100%;transition:.3s;-o-border-sizing:border-box;-moz-box-sizing:border-box;-webkit-box-sizing:border-box}input[type="text"],input[type="password"]{padding:12px;width:100%;transition:.3s;-o-border-sizing:border-box;-moz-box-sizing:border-box;-webkit-box-sizing:border-box}.media{padding:1em}.media .img{float:left;margin:0 10px 10px 0}.media-heading{font-size:1.4em}.error{border:solid red}.avatar{width:100%;height:100%}.active{border-bottom:3px solid #2980b9}
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
label > input{
    margin-top:10px;
}
label > textarea{
    margin-top:10px;
}
#matchList li{
    margin-top:10px;
    cursor:pointer;
}
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
                <form method="post" action="create.php" onSubmit="return false;">
                    <div class="form-group">
                        <label for="master">Owner
                            <input type="text"  id="master" name="master" readonly="readonly" value="<?php echo $_SESSION['username'];?>"/>
                        </label>
                    </div><br/>
                    <div class="form-group">
                        <label for="repo">Repository(Required)
                            <input type="text" id="repo" value="" name="title" placeholder="Repository Name" required/>
                        </label>
                        <div id="status"></div>
                    </div><br/>
                    <div class="form-group">
                        <label for="category">Category(Required)
                            <input type="text" id="category" name="cat" placeholder="category" required/>
                        </label>
                        <div id="subintrest-list"></div>
                    </div><br/>
                    <div class="form-group">
                        <label for="description">Description(Optional)
                            <textarea id="description" name="desc" placeholder="Optional. Describe your project" cols="70"></textarea>
                        </label>
                    </div><br/>
                    <input type="submit" name="create" id="create" value="Create" class="btn flat"/>
                </form>
            </div>
            <div class="col span_2_of_12"></div>
        </div>
        <script src="js/jquery.min.js"></script>
        <script>
            $('#category').on('keyup',function(){
                var subintrest = $(this).val();
                if(subintrest.length >= 1){
                    $('#subintrest-list').html('<img src="images/loading.gif" />');
                    var datastring = {"value":subintrest};
                    $.ajax({
                        type:"POST",
                        url:"subintrest.php",
                        data:datastring,
                        cache:false,
                        success:function(data){
                            $('#subintrest-list').html(data);
                            $('#matchList li').on('click',function(){
                                $('#category').val($(this).text());
                                $('#subintrest-list').text('');
                            });
                        }
                    });
                }
            });
            $('#repo').on('blur',function(){
                var title = $(this).val();
                if(title.length >= 1){
                    $('#status').html('<img src="images/loading.gif" />');
                    var datastring = {"title":title};
                    $.ajax({
                        type:"POST",
                        url:"check_repo.php",
                        data:datastring,
                        cache:false,
                        success:function(data){
                            if(data == false){
                                $('#repo').removeClass('error');
                                $('#status').html('<img src="images/ok.png" width="20" height="20" alt="Good"/>');
                            }else{
                                $('#status').html('<img src="images/not-ok.png" width="10" height="10" alt="Project already exists"/>&nbsp;Already exists');
                                $('#repo').addClass('error');
                            }
                        }
                    });
                }
            });
            $('#create').on('click',function(){
                var title = category = "";
                if(!$('#repo').hasClass('error')){
                    title = $('#repo').val();
                }
                category = $('#category').val();
                var description = $('#description').val();
                if(title != '' && category != ''){
                    $.ajax({
                        type:"POST",
                        url:"create.php",
                        data:{"title":title,"description":description,"category":category},
                        cache:false,
                        success:function(data){
                            if(data == "ok"){
                                $('#status').html('<img src="images/ok.png" width="20" height="20" alt="Good"/>');
                                $('#repo').removeClass('error');
                                window.location.href = './'+title;
                            }else{
                                $('#repo').addClass('error');
                                $('#status').html('<img src="images/not-ok.png" width="10" height="10" alt="Some error occured."/>'+data);
                            }
                        }
                    });
                }
            });
        </script>
    </body>
</html>
<?php 
    }
?>