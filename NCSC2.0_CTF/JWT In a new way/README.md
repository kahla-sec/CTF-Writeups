# **JWT In a new way (1000pts) (0 Solves)**
![TASK](https://i.imgur.com/YuMeMZE.png)

And finally i'm writing a writeup for this task xD i published this task in Securinets Mini CTF and NCSC2.0_CTF but unfortunately it had 0 solves (in fact no one managed to pass the first step) , it took me a lot of time to prepare this task so i hope you enjoy it :D
However bring your coffee cup and let's begin the road . This Task has 3 steps , the first one is bypassing JWT token with a custom attack (not the regular ones) actually using an SQL injection in one of the token parameters , then we will have to exploit an ssrf to crypt the content of flag file (too much filters here haha)
## 1st Step : JWT Bypass ##

After visiting the link we have a simple web page with a sign in and sign up page ; 

![HOME](https://i.imgur.com/y8vDSCL.png)

So after registering with random creds and signing in we check the cookies and as the task name refers we find a jwt token

![HOME](https://i.imgur.com/mmFsMEd.png)

We go as usual to the famous jwt.io to see the content of the token 

![JWT](https://i.imgur.com/TRIhUBU.png)

We can try all the regular attacks but nothing will work , the kid value in the header is suspicious as we dont see it always in JWT tokens so after some googling we can conclude
that the kid parameter is used when the jwt token is signed using multiple keys , each kid value refers to a different key (Example: kid:1 refers for ex to the key 'blabla' 
and when we have kid:2 we are using a different key to sign the jwt token) Hmmm things are getting interesting here, we can suppose that the keys are fetched from a database maybe ,
let's read again the description , we can notice a hint for SQL so maybe we have an SQL injection ? Let's try to trigger an error by injecting this in the kid parameter 
> ' UNION SELECT 2 -- -

But Before you will have to fully understand how JWT tokens are forged, because there is no libraries that include the kid implementation so let's take some notes here :

As we all know JWT token have 3 sections : Header + Payload + signature

Header : typically consists of two parts: the type of the token, which is JWT, and the signing algorithm being used, such as HMAC SHA256 or RSA but here we have also the kid part which is optional

Payload : it contains statements about an entity (typically, the user) and additional data .

Signature : Simply if we are using HS256 the sig is : Signature= HMACSHA256(BASE64URL(HEADER)+'.'+BASE64URL(PAYLOAD))

Be careful it's BASE64URL not a regular BASE64 you can read about it here : [LINK](https://fr.wikipedia.org/wiki/Base64#base64url)

NOTE: you can use the php function hash_hmac('sha256', $msg, $key, true) to sign the token

so after forging the JWT token and replacing it we got this page !

Token i used (i have used a random key for the signature) : 
> eyJ0eXAiOiAiSldUIiwiYWxnIjogIkhTMjU2Iiwia2lkIjogIiAnIFVOSU9OIFNFTEVDVCAyIC0tIC0ifQ.eyJ1c2VyIjogImFhIiwidHlwZSI6ICJhZG1pbiJ9.xXh6UOMjm0YtgmiIsL6VExdDCLiUhIEF28kjv8UzWpo

![JWT](https://imgur.com/GHbrcmm.png)

Hmmmmmmm an interesting message i think we are on a good path , now let's think wisely , we have two paths now , the first one is to try to leak the keys with a blind SQL injection which is really painful especially if we face some filters and the second one which we will follow.

We will force the jwt token to use a key we provide to sign the token ; after some regular SQL injection tests we found that we had two columns which is expected (a column for the id and a column for the key ) we use this payload in the kid parameter
> 'UNION SELECT 1,'kahla' -- -

Thus the key will be forced to be kahla BUT we still get the annoying page "Here is your flag ! Just kidding " :'( (What an evil author 3:) )

I think there are some filters here :'( Let's try this payload in the kid parameter :
> 'UNiOn SElEcT 1,'abc' -- -

The final token used:

eyJ0eXAiOiAiSldUIiwiYWxnIjogIkhTMjU2Iiwia2lkIjogIidVTmlPbiBTRWxFY1QgMSwnYWJjJyAtLSAtIn0.eyJ1c2VyIjogIiIsInR5cGUiOiAiYWRtaW4ifQ.3cOHXg1U7Mj_I3ag37oeg5KWJYA11T74bbD4NrcMC8A

And yeeeeeeees we did it ! but WTF where is our flag ! we need to finish this step before

## 2nd Step : SSRF + Decrypt ##

![CRYPT](https://imgur.com/BLjbHaj.png)

It's a web application that crypts a txt file and after some tries i figured that it only accepts urls that finish with .txt extension 

![CRYPT](https://imgur.com/9mPssf6.png)

We can all notice that it's an ssrf but:
- file protocol is filtered we only can use http or https protocol
- localhost,127.0.0.1,127.0.1,the octal form of ip , and even ipv6 localhost address are filtered

Finally i used this payload that bypassed all the filters below :
> http://0x7f000001/flag.txt

I used the hexadecimal format of local ip address :D 

And Bingo we have our flag but it's encrypted 
![CRYPT](https://imgur.com/cFehJVI.png)

If you check the source code you find a hint that leads us to visit robots.txt file and BINGO we find the crypting function

```php
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
```
 
 But it's somehow a little bit obfuscated , however after digging here is the decrypt function
 

```php
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

```

Executing this will give you the flag but with some unreadable characters (because of the non printable characters after crypting it) that's why we can use curl in our cli and extract the crypted flag and save it or simply we can use a little python script
that automates this and FINALLY we got The flag :
> securinets{W00w_3v3n_Th3_AutHor_C4Nt_S0lV3_TH1$!!}

*** Conclusion ***

I hope you liked this task , it took me 4 continuous days of hard work to implement this idea ( i had to write a big part of jwt generator ) you can find the source code in the same directory ! Anyways dont forget to star me and if you faced any problems please contact me!





