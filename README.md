# Product & Order Manager

A Laravel + Inertia.js application for managing a product catalog and viewing orders, built with Vue 3, TypeScript, Tailwind CSS, and shadcn-vue style components.

## Tech stack

- **Backend:** Laravel 12, Inertia.js (server adapter)
- **Frontend:** Vue 3, TypeScript, Vite, Tailwind CSS
- **Auth:** Laravel's built-in authentication (login, registration, password reset, email verification)
- **Database:** MySQL (via Docker)

## Features

- Product catalog with create/edit/delete, status (draft, published, archived), and inventory fields
- Order listing
- Full auth flow: login, registration, password reset, email verification
- User profile and password settings

## Requirements

- **Docker** and **Docker Compose**

## Quick start

```bash
git clone <repo-url>
cd nicole-reyes-fstoreph-test
./setup.sh
```

`setup.sh` builds and starts the `app` (php-fpm), `nginx`, and `mysql` containers, installs PHP and Node dependencies, generates the app key, runs migrations, links storage, and builds frontend assets. Once it finishes, the app is available at **http://localhost:8000**.

Useful commands afterward:

```bash
docker compose logs -f          # tail container logs
docker compose exec app bash    # shell into the app container
npm run dev                     # Vite dev server with HMR (run on host, needs Node)
docker compose down             # stop the stack
```

## Available scripts

| Command          | Description                        |
| ---------------- | ---------------------------------- |
| `npm run dev`    | Start the Vite dev server (HMR)    |
| `npm run build`  | Build production frontend assets   |
| `npm run lint`   | Lint and auto-fix frontend code    |
| `npm run format` | Format frontend code with Prettier |

## Environment

`setup.sh` copies `.env.example` to `.env` on first run. Key variables:

- `DB_CONNECTION`, `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` — database connection. `.env.example` defaults to SQLite, but the Docker stack runs MySQL: `setup.sh` sets `DB_CONNECTION=mysql`, `DB_HOST=mysql`, `DB_PORT=3306`, `DB_DATABASE=laravel`, `DB_USERNAME=root`, `DB_PASSWORD=secret` to match the `mysql` service in `docker-compose.yml`
- `APP_URL` — base URL used for generated links and asset URLs
- `MAIL_MAILER` — defaults to `log`, so outgoing mail is written to the log instead of sent

## Seeding data

`setup.sh` runs migrations but does not seed data. To populate the database with a test user, 50 sample products, and sample orders:

```bash
docker compose exec app php artisan db:seed
```

This runs `database/seeders/DatabaseSeeder.php`, which creates:

- A test user: **test@example.com** / **password**
- 50 products via `ProductSeeder`
- Sample orders via `OrderSeeder`

To reset the database and reseed from scratch:

```bash
docker compose exec app php artisan migrate:fresh --seed
```

## License

MIT
