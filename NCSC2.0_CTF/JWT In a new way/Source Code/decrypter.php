<?php
function decryptage($content){
        $i=0;
        $words=array('kAHl4','$ecUriNets','Cha1m4','th4meUr','WhiT3HacK3Rs','Ani$Bo$$CoUldNtS0Lv31t');
        $decrypted="";
        for($i=0;$i<strlen($content);$i-=-pow(0,0))
        {
            $ser=serialize(array($words[$i % 6],'securinets'));
            $key=intval(explode(":",explode(";",$ser)[1])[1]);
            $decrypted=$decrypted.chr(ord($content[$i])-$key) ;
        }
        return $decrypted ;
    }
$cont="eG9pfH5/c296eu+/vW01On1mP++/vTh4ZVt0SWRLe3tU77+9d2lJO1rvv71kXTZzYklkXk44MDcm77+9EA==" ;
$con=base64_decode($cont);
$res= decryptage($con);
echo $res;
?>
