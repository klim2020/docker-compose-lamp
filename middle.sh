#!bin/bash
cp .env_middle.txt .env
docker-compose down
docker-compose build
docker-compose up -d
