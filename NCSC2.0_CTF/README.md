# Shinobis World (1000pts) (1 Solves) #

![TASK](https://imgur.com/7X3dgtW.png)

Hello guys again , in this task we are give the Settings.py of a django Web application , we can notice that the website is using caching with redis that is listening on port 6379 on locally !

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

So the idea is to forge our payload using pickle and the set the default key of django (:1:views.decorators.cache.cache_page..GET.a906279c6f1b8c76747a8ba71e866d8c.d41d8cd98f00b204e9800998ecf8427e.en-us.UTC) with our payload , here is what will happen :


