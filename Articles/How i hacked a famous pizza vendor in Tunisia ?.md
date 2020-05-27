# How i hacked a famous pizza vendor in Tunisia ? #

Generally i'm not a fan of bug bounty programs but this time i tried to test my skills in some real world scenario and participate to secure some tunisian websites as they lack a lot of things in term of security :D 
It's 11h pm and i was sitting on my laptop like always doing an annoying java homework for school when i felt hungry :( 
and accidentally an X pizza (we will call it X pizza as i was asked to not reveal the company name) ad catched my eye on facebook. I decided to take a look on their website and try to 
find a way to break it.

## Exploitation ##

I started browsing the website and doing some basic recon , it was a typical wordpress website without any catching 
exploits or some really outdated plugins . I have also ran the famous Wpscan and tried to brute force the admin account but unfortunately i got nothing.

My next step was trying to explore if there's some insecure code management and Bingo it appeared that the developers forgot the .git directory there, so we can dump the source code of the web app.
I used the famous gitdumper and extractor shell scripts to do that and we were so lucky! i found some juicy informations there ( a database backup for all users and promo codes , wp-config file containing all secret codes and salts used to hash users passwords ..)
This was really some juicy informations but i wanted to explore more , i started reviewing the source code and after a while 
i found an awesome api , it checks a hardcoded token and then using only a phone number as input it logins to the phone number owner account :o 

![CODE](https://i.imgur.com/MqDN2Ai.jpg)

### Why do they use it ? ###

This api was meant to be accessed only from the call service employees and the developer told me that only some white listed ips were allowed to visit it. It was like a secret hidden backdoor .

However using the hardcoded token we can access any account , you may be asking how i found the phone number of a privileged user , the database didn't have any useful informations for this (it was an old DB) so i had to think for a while and finally
i remembered that i found and old list of orders informations in the uploads directory and usually the first order will be 
passed by the developer to test the web app so i searched there for a little bit and Bingo after entering the phone 
number w got access to the admin dashboard :D

![DASHBOARD](https://i.imgur.com/vcFJQX9.jpg)

I wanted to stop here but i think it was possible to get a reverse shell by modifying the templating files and 
i believe that they are using a shared server (many websites hosted on the same server) 
so going so far won't be a good idea .

I really had fun trying to break the website, the developer was so nice and fixed this out so quickly and for 
me i had to skip my morning classes as i completed all this at 4 am and i had to complete my annoying homework tomorrow in the lunch break. 

For those asking i didn't get any bounties for this , i didn't even expect that xd 

Thank you for reading this article, i hope you enjoyed your ride, don't forget to take a look 
at my previous articles and follow me on twitter for more exciting content :D 
