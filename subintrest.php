<?php
    if(isset($_POST) && !empty($_POST)){
        $value = strip_tags(trim($_POST['value']));
        $input = filter_var($value,FILTER_SANITIZE_STRING);
        include"includes/dbconnect.php";
        try{
            $sql = $con->prepare("SELECT * FROM categories WHERE category_name LIKE '%".$input."%'");
            if($sql->execute()){
                echo "<ul id='matchList'>";
                $matchString = $sql->fetchAll(PDO::FETCH_ASSOC);
                if(!empty($matchString)){
                    foreach($matchString as $matchString){
                        $matchStringBold = preg_replace('/('.$input.')/i','<strong>$1</strong>',$matchString['category_name']);
                        echo "<li>".$matchStringBold."</li>";
                    }
                }
                echo "</ul>";
            }
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }else{
        echo "You can only get data using POST method";
    }
?>