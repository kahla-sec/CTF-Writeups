<?php ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style1.css"> 

    <link href="https://fonts.googleapis.com/css?family=Acme|Yanone+Kaffeesatz&display=swap" rel="stylesheet">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Inscription</title>
</head>
<body>
<div class="form-style-6">
<h1>Sign Up</h1>
<form action="" method="POST">
<input type="text" name="name" placeholder="Your Name" />
<input type="email" name="email" placeholder="Email Address" />
<input type="text" name="password" placeholder="Your Password"></textarea>
<input type="submit" value="register" />
</form>
<br>
<a href="connexion.php">Sign In</a>
</div>
</body>
</html>
<?php
if (isset($_POST['name'])&&isset($_POST['password'])&&isset($_POST['email'])){
// BD Connection
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
// Fin    
    $req=$bdd->prepare("INSERT INTO users (id,user,password,email) VALUES (null,?,?,?)");
    $req->execute(array(
        $_POST['name'],
        $_POST['password'],
        $_POST['email']
    ));
    if($req){
        echo '<h3> You are registered successfully </h3>';
    }
}

?>