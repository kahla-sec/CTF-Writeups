<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(1);
session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style2.css"> 
    <link href="https://fonts.googleapis.com/css?family=Acme|Yanone+Kaffeesatz&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Sign In</title>
</head>
<body>
<div class="form-style-6">
<h1> Sign In</h1>
<form action="" method="POST">
<input type="text" name="user" placeholder="Your username" />
<input type="text" name="password" placeholder="Your Password"></textarea>
<input type="submit" value="Sign In" />
</form>
<a href="inscription.php">Sign Up </a>
</div>
    
    <?php
    //bd Connection
try {
    $host='localhost';
    $dbname='shinobi';
    $charset='utf8';
    $bdd=new PDO ("mysql:host=$host;dbname=$dbname;charset=$charset",'task','123') ;
    }
    catch (Exception $e)
    {
    die("Connection Error :".$e->getMessage()) ;
    }
//FIN    
if(isset($_POST['user'])&&isset($_POST['password'])){
$req=$bdd->prepare("SELECT * FROM users WHERE user=? AND password=?");
$req->execute(array(
    $_POST['user'],
    $_POST['password']
)) ;
if($reponse=$req->fetch()){
    $_SESSION['id']=$reponse['id'];
    $_SESSION['user']=$reponse['user'];
    $_SESSION['admin']=false ;
    header("Location: accueil.php") ;
} else {
    if(preg_match("/'|or/i",$_POST['user'])){
      die('Hey dude it\'s 2019 and you are trying SQL injection ? Don\'t waste your time here ');  
    } else{
    die('Wrong username Or password ! Please Try Again');
    }
}
}



?>
</body>
</html>
