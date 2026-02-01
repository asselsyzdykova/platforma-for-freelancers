# platforma-for-freelancers

Platform for freelancers — full‑stack web app built as a bachelor’s project.

## Tech stack

- Backend: Laravel (PHP 8.2)
- Frontend: Vue 3 + Vite (Node 20)
- Database: MySQL 8
- Dev environment: Docker + Docker Compose

## Quick start (Docker)

From the repository root:

```sh
docker compose up --build
```

Services:

- Backend: http://localhost:8000
- Frontend: http://localhost:5173
- MySQL: localhost:3306

## Database setup

After starting Docker containers, run migrations and seeders:

```sh
docker compose exec freelancers-backend php artisan migrate --seed
```

## Local development (without Docker)

### Backend

```sh
cd backend
composer install
php artisan serve --host=0.0.0.0 --port=8000
```

### Frontend

```sh
cd frontend
npm install
npm run dev -- --host 0.0.0.0 --port 5173
```

## Environment configuration

The Docker setup uses the database credentials from [docker-compose.yml](docker-compose.yml). If you run locally without Docker, set the same values in your backend .env file:

- DB_HOST=127.0.0.1
- DB_PORT=3306
- DB_DATABASE=bezrab
- DB_USERNAME=bezrab
- DB_PASSWORD=bezrab

## Test accounts

- Client: client.alice@example.com / password
- Freelancer: freelancer.martin@example.com / password

## Troubleshooting

- If you see missing dependencies inside containers, rebuild with:

```sh
docker compose up --build --force-recreate
```

- If Vite is not found, ensure node_modules are installed and the frontend container was rebuilt.
