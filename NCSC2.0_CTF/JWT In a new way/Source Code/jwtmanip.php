<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(1);
include "jwt.php" ;
    
//JWT Decode Function////////////////////////////////////////////////////////////

function decode($jwt, $verify = true)
{
    $tks = explode('.', $jwt);
    if (count($tks) != 3) {
        die('Wrong number of segments');
    }
    list($headb64, $bodyb64, $cryptob64) = $tks;
    if (null === ($header = JWT::jsonDecode(JWT::urlsafeB64Decode($headb64)))) {
        die('Invalid segment encoding');
    }
    if (null === $payload = JWT::jsonDecode(JWT::urlsafeB64Decode($bodyb64))) {
        die('Invalid segment encoding');
    }
    $sig = JWT::urlsafeB64Decode($cryptob64);
    $key=extract_key($header->kid) ;    
    if ($verify) {
        if (empty($header->alg)) {
            throw new DomainException('Empty algorithm');
        }
        if ($sig != JWT::sign("$headb64.$bodyb64", $key, $header->alg)) {
            die('Signature verification failed');
        }
    }
    return $payload;
}
//////////////////////////////////////////////////////////////////////////////////
//JWT token assignment
        // Extract Key Function
            function extract_key($kid){
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
    
            $i=0;
            $blacklist=array('union','select','from','or','all','UNION','SELECT','FROM','OR','ALL');
            for($i=0;$i<count($blacklist);$i++){
            if (preg_match('/'.$blacklist[$i].'/', $kid))
            {
                die('Here is your flag ! Just kidding ');
            }
            }
            $rep=$bdd->query("SELECT * FROM keyss WHERE id='".$kid."'");
            if($resultat=$rep->fetch()){
            return($resultat['jwtkey']);
            }

            }
       if(!(isset($_COOKIE['token']))){         
            $payload=array('user'=>$_SESSION['user'],'type'=>'user') ;
            $kid=rand(1,6);
            $jwt=JWT::encode($payload,extract_key($kid),$kid) ;
            setcookie('token',$jwt,time() + (86400 * 30));
   }
   else {
            @$payl=decode($_COOKIE['token']) ;
            if($payl->type==='admin'){
                $_SESSION['admin']=true ;
            }
   }
/////////////////////////////////////////////////////////////////////
?>
