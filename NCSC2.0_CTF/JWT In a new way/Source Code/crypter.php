<?php 
class crypt {
    public static $error ;

    public static function cryptage($content){
        $i=0;
        $words=array('kAHl4','$ecUriNets','Cha1m4','th4meUr','WhiT3HacK3Rs','Ani$Bo$$CoUldNtS0Lv31t');
        $crypted="";
        for($i=0;$i<strlen($content);$i-=-pow(0,0))
        {
            $ser=serialize(array($words[$i % 6],'securinets'));
            $key=intval(explode(":",explode(";",$ser)[1])[1]);
            $crypted=$crypted.chr(ord($content[$i])+$key) ;          
        }
        return $crypted;

    }
    public static function read_file($url){
        if (crypt::final_sanitize($url)){
            $content=crypt::get_content($url);
            $error=""; 
            $valid=true ;
            return($content);
        }
        else if(preg_match("/127.0.0.1|0x7f.0x0.0x0.0x1|0177.0.0.01|2130706433|::1|127.1|127.0.1|file|localhost/i",$url)){
            crypt::$error="A little script kiddie is detected" ;
        }else{
            crypt::$error="File Type not Valid";
        }
    }
    public static function final_sanitize($url){
    if ((crypt::sanitize_url($url))&&(!preg_match("/127.0.0.1|0x7f.0x0.0x0.0x1|0177.0.0.01|2130706433|::1|127.1|127.0.1|file|localhost/i",$url))){
    return true ;
    }
    else false ;
    }

    public static function sanitize_url($url){
        $array=explode(".",$url);
        $extension=$array[count($array)-1] ;
        if ($extension ==="txt"){
            return true ;
        }
        else return false ;
    }
    public static function get_content($url){
        // create curl resource
        $ch = curl_init();

        // set url
        curl_setopt($ch, CURLOPT_URL, $url);

        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // $output contains the output string
        $output = curl_exec($ch);
        curl_close($ch);     
        return $output ;     

    }

}




?>