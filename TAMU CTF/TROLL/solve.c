#include<stdlib.h>
#include<stdio.h>
#include<time.h>

int main(int argc, char *argv[]){
    int i=0;
   // int seed=808464433;
int seed=3472328296227680305    
srand(seed);
for(i=0;i<=99;i++){
    int a=rand()% 100000 + 1;
//printf("%d :: %d - %d - %d\n",seed,a,b,c);
    printf("%d\n",a);  
} 
 return 0;
}
