#!bin/bash
cp .env_old.txt .env
docker compose down
docker compose build
docker compose up -d
echo "http://localhost:8071/"