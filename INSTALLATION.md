# Installation Guide

Clone the repository

git clone https://github.com/fadiomar1/project-network-programming.git

Start WordPress

docker compose up -d

Start Apprise

docker compose -f apprise-docker-compose.yml up -d

Deploy Containerlab

sudo containerlab deploy -t router.clab.yml

Run zrok

zrok share public http://127.0.0.1:8080 -n public:fadi-apprise

Public URL

https://fadi-apprise.shares.zrok.io
