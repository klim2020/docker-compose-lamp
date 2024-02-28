#!bin/bash
cp .env_middle_dd.txt .env
docker compose down
docker compose build
docker compose up -d
