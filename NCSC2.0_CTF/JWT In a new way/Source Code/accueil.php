<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(1);
session_start(); 
include "jwtmanip.php" ;

if(!$_SESSION['admin']) : ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Accueil</title>
</head>
<body>

  <?php  echo "<h1> Welcome ".$_SESSION['user']."</h1>" ?>
    <br/> <h2>Congrats you are now a shinobi ! You can try hard and become a hockage </h2>
    <br/><br/><a href="deconnexion.php">Deconnexion </a>

</body>
</html>

<?php endif ?>


<?php if($_SESSION['admin']) : ?>
<?php include("crypter.php") ; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Hockage</title>
</head>
<body>
<h1> Welcome Admin </h1>
<h2> File Cryptor V1 </h2>
<!-- check robots they may help you -->
<br/>
<form method="POST" action="accueil.php">
<input type="text" name="url" placeholder="Enter a link to .txt file example.com/xyz.txt" >
<br/><br/>
<input type="submit" name="crypt" value="Crypt">
</form>

<?php  
if (@isset($_POST['url'])){
    @$content=crypt::read_file($_POST['url']) ;
    if (!crypt::final_sanitize($_POST['url'])){
        echo '<h3>'.crypt::$error.'</h3>' ;
    }
    else if (strlen($content)!==strlen(crypt::cryptage($content))){
        echo 'What you have written is not supported ' ;
    }
    else {
        echo '<h3> Here is your text crypted (Flag in flag.txt) </h3><br/>' ;
        echo crypt::cryptage($content) ;

    }

}

?>

    
</body>
</html>

<?php endif ?>
