#!/usr/bin/env bash
#
# One-time onboarding script for developers cloning this project.
# Brings up the Docker Compose stack (nginx + php-fpm app + mysql), installs
# PHP/Node dependencies, and prepares the Laravel app (.env, key, migrations,
# storage link, built frontend assets).
#
# Usage: ./setup.sh   (or: bash setup.sh)

set -euo pipefail
cd "$(dirname "${BASH_SOURCE[0]}")"

APP_URL="http://localhost:8000"
NODE_IMAGE="node:20-alpine"

info()    { printf '\033[1;34m==>\033[0m %s\n' "$1"; }
success() { printf '\033[1;32m==>\033[0m %s\n' "$1"; }
warn()    { printf '\033[1;33m==>\033[0m %s\n' "$1"; }
die()     { printf '\033[1;31mError:\033[0m %s\n' "$1" >&2; exit 1; }

# --- 1. Prerequisites -------------------------------------------------------

command -v docker >/dev/null 2>&1 || die "Docker is required. Install it from https://docs.docker.com/get-docker/"

if docker compose version >/dev/null 2>&1; then
    COMPOSE=(docker compose)
elif command -v docker-compose >/dev/null 2>&1; then
    COMPOSE=(docker-compose)
else
    die "Docker Compose is required (either the 'docker compose' plugin or standalone 'docker-compose')."
fi

# --- 2. Environment file -----------------------------------------------------

set_env() {
    local key="$1" value="$2" tmp
    tmp="$(mktemp)"
    if grep -qE "^#?[[:space:]]*${key}=" .env; then
        sed -E "s|^#?[[:space:]]*${key}=.*|${key}=${value}|" .env > "$tmp" && mv "$tmp" .env
    else
        printf '%s=%s\n' "$key" "$value" >> .env
    fi
}

if [ -f .env ]; then
    info ".env already exists, leaving it as-is."
else
    info "Creating .env from .env.example"
    cp .env.example .env

    info "Pointing .env at the Docker Compose services (mysql host, port 8000)"
    set_env APP_URL "$APP_URL"
    set_env DB_CONNECTION mysql
    set_env DB_HOST mysql
    set_env DB_PORT 3306
    set_env DB_DATABASE laravel
    set_env DB_USERNAME root
    set_env DB_PASSWORD secret
fi

# --- 3. Build and start containers ------------------------------------------

info "Building and starting containers (app, nginx, mysql)"
"${COMPOSE[@]}" up -d --build

# --- 4. PHP dependencies ------------------------------------------------------
# The compose file bind-mounts the repo into the app container, which shadows
# the vendor/ installed at image build time, so install into the mount directly.

info "Installing PHP dependencies"
"${COMPOSE[@]}" exec -T app composer install --no-interaction

if ! grep -qE '^APP_KEY=.+' .env; then
    info "Generating application key"
    "${COMPOSE[@]}" exec -T app php artisan key:generate --ansi
fi

# --- 5. Wait for MySQL --------------------------------------------------------

info "Waiting for MySQL to accept connections"
tries=0
until "${COMPOSE[@]}" exec -T mysql mysqladmin ping -h 127.0.0.1 -uroot -psecret --silent >/dev/null 2>&1; do
    tries=$((tries + 1))
    if [ "$tries" -ge 30 ]; then
        die "MySQL did not become ready in time. Check 'docker compose logs mysql'."
    fi
    sleep 2
done

# --- 6. Migrate + storage link ------------------------------------------------

info "Running database migrations"
"${COMPOSE[@]}" exec -T app php artisan migrate --force

info "Linking storage directory"
"${COMPOSE[@]}" exec -T app php artisan storage:link || true

# --- 7. Frontend assets --------------------------------------------------------
# Built via a throwaway Node container so developers don't need Node installed.

info "Installing Node dependencies and building frontend assets"
docker run --rm \
    -v "$(pwd)":/var/www \
    -w /var/www \
    "$NODE_IMAGE" \
    sh -c "npm install && npm run build"

# --- Done ----------------------------------------------------------------------

success "Setup complete! The app is running at ${APP_URL}"
echo
echo "Useful commands:"
echo "  ${COMPOSE[*]} logs -f          # tail container logs"
echo "  ${COMPOSE[*]} exec app bash    # shell into the app container"
echo "  npm run dev                    # Vite dev server with HMR (run on host, needs Node)"
echo "  ${COMPOSE[*]} down             # stop the stack"
