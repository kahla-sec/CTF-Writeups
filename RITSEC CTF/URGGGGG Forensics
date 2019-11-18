# **URGGGGG (480 Pts)**
![TASK](https://i.imgur.com/qvb3bdp.jpg)

This task was one of the most challenging forensics task related to wireshark that i have played , we were given 
a wireshark capture that showed packets using usb protocol ( universal serial block) .

![wireshark](https://i.imgur.com/bNWR4z4.png)

My first thought was that this is probably traffic captured from a keyboard as you can see there's a lot of URB_INTERRUPT in ,
maybe these keystrokes hide within them the beloved FLAG .

However extracting these keystrokes won't be that easy , we have to firstly read about this protocol .
After some researchs i figured that there's four types of "transfer type" : 0: isochronous , 1: interrupt, 2:control , 3:bulk ,
we are here interested in the interrupt type so we have to add this filter to wireshark : **usb.transfer_type==0x01**  

I have also figured that the keystrokes are stored in the 'leftover capture data' in hexadecimal .
so to facilitate the task we will add this simple filter to wireshark : **(usb.transfer_type==0x01)&&!(usb.capdata==00:00:00:00:00:00:00:00)** (data with 00:00:00:00:00:00:00:00 are just empty ones)

![wireshark](https://i.imgur.com/Dr6j06a.png)

After applying these filters we have pretty clean result , we will export it as .CSV to facilitate the manipulation
of leftover data 
After exporting using wireshark (from File menu then Export) we type this on our terminal to only extract the leftover data :

**cat data |cut -d "," -f 7|cut -d "\\"" -f 2 > hexa.txt**

![wireshark](https://i.imgur.com/XEzVH0x.png)

Now we use a simple python script to convert this hexa data to the real keystrokes, i've found this usb HID 
keyboard codes and i've used some of them in the script below ( [LINK](https://gist.github.com/MightyPork/6da26e382a7ad91b5496ee55fdc73db2)) 

```python
newmap = {
2: "PostFail",
4: "a",
5: "b",
6: "c",
7: "d",
8: "e",
9: "f",
10: "g",
11: "h",
12: "i",
13: "j",
14: "k",
15: "l",
16: "m",
17: "n",
18: "o",
19: "p",
20: "q",
21: "r",
22: "s",
23: "t",
24: "u",
25: "v",
26: "w",
27: "x",
28: "y",
29: "z",
30: "1",
31: "2",
32: "3",
33: "4",
34: "5",
35: "6",
36: "7",
37: "8",
38: "9",
39: "0",
40: "Enter",
41: "esc",
42: "del",
43: "tab",
44: "space",
45: "-",
47: "[",
48: "]",
56: "/",
57: "CapsLock",
79: "RightArrow",
80: "LetfArrow"
}
ch=""
myKeys = open('hexoutput.txt')
i = 1
for line in myKeys:
	bytesArray = bytearray.fromhex(line.strip())
	#print "Line Number: " + str(i)
	for byte in bytesArray:
		if byte != 0:
			keyVal = int(byte)

			if keyVal in newmap:
				print "Value map : " + str(keyVal) + "-> " + newmap[keyVal]
				ch=ch+newmap[keyVal]
			else:
				print "No map found for this value: " + str(keyVal)

print format(byte, '02X')
i+=1
print(ch)

```

The hard work didn't finished yet , after extracting you'll see random letters but 
after some search(my eyes really went down) i found the ritsec word which marks the beginning of the flag ,
you can notice the "Post Fail" before each letter so we can guess it's shift key because flag format is RITSEC{}
in uppercase so we will begin to extract the flag considering that every shift key before a letter will mean that it's an uppercase letter , BUT you have to pay attention
to the fact that the author is using a US keyboard .

![wireshark](https://i.imgur.com/xiUGGao.png)

After a long time we  managed to extract the flag with its special letters (the @ ) and the
CTRL+v , CTRL+x and CTRL+v and with also some guess xD 

**RITSEC{wH0_s@1d_n3tw0rk1nG_wAs_tH3_oNlY_pAck3t_TyP3}**

I want to thank the author **@DataFrogMan** for this challenging and fun task and for his precious advices that helped me
to finish the task , i enjoyed  and learned a lot from it .
