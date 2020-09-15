# Web RTC (450pts) 39 solves #

![TASK](https://imgur.com/KJrEEZl.png)

Last weekend,we have been a part  of the CSAW CTF 2020 and our team Fword ranked 4th in MENA region.
We managed to solve all web challenges with my awesome teammates [@Hera](https://twitter.com/Hera14165735) and [@Noxious](https://twitter.com/BahaBaghdadi) and we have particularly enjoyed **Web RTC** .

![SCOREBOARD](https://i.imgur.com/X0z8wys.png)

## TL;DL ##

* Exploiting CoTurn server in order to gain SSRF.

* Escalating SSRF to RCE using Redis.

## Overview ##

We have a Real Time Chat web app and we were provided with the source code / Dockerfile / supervisor.conf.

![TASK](https://imgur.com/cBI0d2H.png)

Examining the source code didn't really bring something to the table.But we got some juicy stuff ,reading the Dockerfile / supervisor.conf.
It turned out that the application is using TURN protocol to relay the network traffic between peers and Redis as a caching system.

![TASK](https://imgur.com/thalPtZ.png)

## Exploitation ##

Since the application didn't seem vulnreable, we decide to focus basically on CoTURN server exploitation.
After searching around we found the following [HackerOne report](https://hackerone.com/reports/333419) which claims that it's possible to abuse the TURN Protocol to gain SSRF. In fact,
the attacker may proxy TCP connections to the internal network by setting the **XOR-PEER-ADDRESS** of the TURN connect message (method 0x000A, https://tools.ietf.org/html/rfc6062#section-4.3) to a private IPv4 address.

We used the following **[tool](https://github.com/staaldraad/turner)** to exploit that issue.

![TASK](https://imgur.com/M6NaeIz.png)

We can notice that the Coturn server IP is returned to confirm the SSRF.

![TASK](https://imgur.com/L2aCve1.png)

Unfortunately fetching localhost ip didn't work for us so we fired up wireshark to monitor the STUN (TURN Protocol is an extension of STUN protocol) packets.

![TASK](https://imgur.com/PmES4dZ.png)

We can confirm that the coTurn server has some protections blacklisting local ips. We first tried to bruteforce the internal ip but in vain , finally we managed  to bypass this protection 
using **0.0.0.0** ip:

![TASK](https://imgur.com/zQYbXit.png)

![TASK](https://imgur.com/gAqT0pL.png)

And Bingo we achieved the first part!

### Escalating SSRF to RCE using Redis ##

This part was somehow trivial but it took us some time to exploit Redis properly, we used  some instructions from this **[tool](https://github.com/jas502n/Redis-RCE)** ( we fired a Rogue server from it) and executed the following commands:

![TASK](https://i.imgur.com/jUFM1jt.jpg)

And Voilaa We got our beloved flag :D It was our first time dealing with RTC website and exploiting and it was really fun!

**References:**

* [Article About the vulnerability](https://www.rtcsec.com/2020/04/01-slack-webrtc-turn-compromise/)

* [HackerOne Article](https://hackerone.com/reports/333419)

