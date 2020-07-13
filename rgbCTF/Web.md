# Typeracer (119 Points) #

![TASK](https://imgur.com/WA3jGN8.png)

The task was simple, we have to write the text that is shown so fast, I wrote this Javascript script to simulate keyboard typing :D

```js

var div=document.getElementById('Ym9iYmF0ZWEh').children;
var dict={};
for(var i=0;i<div.length;i++){
var order=div[i].getAttribute("style").split(";")[0].split(' ')[1];
var word=div[i].innerHTML.replace("&nbsp;","");
dict[order]=word;
}
var text="";
for(var i=0;i<Object.keys(dict).length;i++){
text+=dict[i]+" "
}
for(var i=0;i<text.length-1;i++){
var evt = new KeyboardEvent('keypress', {'keyCode':text.charCodeAt(i), 'which':text.charCodeAt(i)});
document.dispatchEvent (evt);
}
console.log("DONE");

```

# Imitation Crab (448 Points) #

![TASK](https://imgur.com/2xzZL1B.png)

The website had a keyboard that send a post request with the key we wrote each time we press any key, there was also a .har file which is used to capture http requests. The idea is to analyze the har file and extract what the admin wrote :D
I wrote this shell script to do it for us 

```sh

charcodes=$(cat export.har |grep -A 64 http://127.0.0.1:3000/search|grep text|cut -d " " -f 14|cut -d : -f2|tr -d "\"}")
for i in $charcodes;do
awk "BEGIN{printf \"%c\",$i }"
done

```

# Countdown (455 Points) #

![TASK](https://imgur.com/EVLII3c.png)

The goal in this task is to change the countdown end value, analyzing the cookies we found that the end date was stored in a flask session cookie so we have to forge a new session cookie with the end date we want :D
I used the flask-unsign tool ( **pip3 install flask-unsign** to install it ) , and the secret was **Time** , to be honest i needed to guess it as it was written **Time is key** in the homepage.

```
flask-unsign --sign --cookie "{'end': '2020-07-13 10:59:59+0000'}" --secret 'Time' --legacy
```

If you have any questions you can contact me on twitter @BelkahlaAhmed1 or contact our team twitter @FwordTeam ! Make sure to visit my personal blog for more useful content :D
