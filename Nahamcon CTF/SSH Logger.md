# SSH Logger (175pts) 75 solves #

![TASK](https://imgur.com/mvBLdEe.png)

We are given some ssh credentials , our goal is to find the password of the user that is connecting to the box. Running pspy64 we can notice the flag user that is connecting.

![TASK](https://imgur.com/zSN8BCK.png)

Firstly i tried to examine the ssh settings and logs but i didn't find anything useful then after a lot of search i found this useful article [HERE](https://mthbernardes.github.io/persistence/2018/01/10/stealing-ssh-credentials.html)

So if we attach the sshd process to strace we can literally trace everything that is happening , running this command will pop the flag for us : 

```sh
strace -f -p $(pgrep -f "/usr/sbin/sshd") -s 128 2>&1|grep flag{
```

 **Flag:** flag{okay_so_that_was_cool}
 
 If you have any questions you can contact me on twitter @BelkahlaAhmed1 or contact our team twitter @FwordTeam ! Make sure to visit my personal blog for more useful content :D
