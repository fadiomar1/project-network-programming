# Apprise WordPress Integration Project

## Project Overview

This project demonstrates the integration between **WordPress**, **Apprise API**, **Telegram**, **Docker**, **Containerlab**, **Proxmox VE**, and **zrok**.

The objective of this project is to send Telegram notifications directly from a custom WordPress plugin through the Apprise API while making the WordPress website publicly accessible using zrok.

---

# Project Architecture

```
                    Internet
                        │
                        ▼
          https://fadi-apprise.shares.zrok.io
                        │
                     zrok Tunnel
                        │
                        ▼
                Ubuntu Server VM
                 (Proxmox VE)
                        │
        ┌───────────────┴───────────────┐
        │                               │
        ▼                               ▼
   WordPress Container            Apprise Container
        │                               │
        └───────────────HTTP────────────┘
                        │
                        ▼
                  Telegram Bot
                        │
                        ▼
              Telegram Notification
```

---

# Technologies Used

- Proxmox VE
- Ubuntu Server
- Docker
- Docker Compose
- WordPress
- MariaDB
- PHP
- Apprise
- Telegram Bot API
- Containerlab
- zrok
- GitHub

---

# Project Workflow

1. Start the Ubuntu Server virtual machine from Proxmox.
2. Docker automatically starts:
   - WordPress
   - MariaDB
   - Apprise
3. Open the WordPress dashboard.
4. Open the **Apprise Integration** plugin.
5. Enter:
   - Notification Title
   - Notification Message
   - Notification Type
6. Click **Send Notification**.
7. The plugin sends an HTTP POST request to the Apprise API.
8. Apprise forwards the notification to Telegram.
9. Telegram immediately receives the notification.

---

# Public WordPress URL

```
https://fadi-apprise.shares.zrok.io/wp-admin
```

### Username

```
fadiomer120@gmail.com
```

### Password

```
fadi123
```

---

# Local URLs

## WordPress

```
http://192.168.1.200:8080/wp-admin
```

---

## Apprise

```
http://192.168.1.200:8000
```

---

## Proxmox VE

```
https://192.168.1.200:8006/#v1:0:=qemu%2F100:4:::::8::
```

---

# Start zrok

Run the following command inside Ubuntu Server:

```bash
zrok share public http://127.0.0.1:8080 -n public:fadi-apprise
```

This command publishes the local WordPress website to the Internet.

Public URL:

```
https://fadi-apprise.shares.zrok.io
```

---

# Project Files

This repository contains:

- docker-compose.yml
- apprise-docker-compose.yml
- router.clab.yml
- apprise-integration.php
- .gitignore

---

# Containerlab Topology

```
PC1 -------- Router -------- PC2
```

Network:

```
192.168.10.0/24
        │
        │
     Router
        │
        │
192.168.20.0/24
```

---

# Verification Commands

## Check Docker

```bash
docker ps
```

---

## Check WordPress

```bash
curl -I http://127.0.0.1:8080
```

Expected:

```
HTTP/1.1 200 OK
```

---

## Check Apprise

```bash
curl http://127.0.0.1:8000/status
```

Expected:

```
OK
```

---

## Check Containerlab

```bash
containerlab inspect -t router.clab.yml
```

---

## Check Public Website

```bash
curl -I https://fadi-apprise.shares.zrok.io
```

Expected:

```
HTTP/2 200
```

---

# How the Notification Works

```
User
   │
   ▼
WordPress Dashboard
   │
   ▼
Apprise Integration Plugin
   │
HTTP POST Request
   │
   ▼
Apprise API
   │
   ▼
Telegram Bot
   │
   ▼
Telegram Notification
```

---

# Demonstration

A complete demonstration video is included with this project.

The video shows:

- Starting Proxmox VE
- Starting Ubuntu Server
- Running Docker containers
- Displaying Docker Compose files
- Displaying the Containerlab topology
- Opening WordPress
- Opening the Apprise plugin
- Sending notifications
- Receiving notifications in Telegram
- Publishing the website using zrok

Everything required to evaluate the project is ready.

---

# Author

**Fadi Omar**

Computer Science Student

Palestine
