const express = require("express");
const rateLimit = require("express-rate-limit");
const app = express();
const { Pool, Client } = require("pg");
const port = process.env.PORT || 9090;
const path = require("path");

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
			console.log(q);
			let censored = false;
			for (let i = 0; i < q.length; i ++) {
				if (censored || "'-\".".split``.some(v => v == q[i])) {
					censored = true;
					q = q.slice(0, i) + "*" + q.slice(i + 1, q.length);
				}
			}
			q = q.substring(0, 80);
			console.log(q);
			res.writeHead(200,{"Content-Type":"text/html"});
			res.end("Hello World");
		} catch (err) {
			console.log(err);
			res.status(500);
			res.writeHead(200,{"Content-Type":"text/html"});
			res.end("ERROOOOOR");

		}
	} else {
		res.render("home", {results: [], err: ""});
	}
});

app.listen(port, function() {
	console.log("App listening on port " + port);
});

