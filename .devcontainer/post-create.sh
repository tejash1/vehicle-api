#!/bin/sh
set -e

echo ""
echo "════════════════════════════════════════════"
echo "  Vehicle API — Codespace Setup"
echo "════════════════════════════════════════════"
echo ""

# ── 1. Environment file ───────────────────────────────────────────────────────
if [ ! -f .env ]; then
    echo "📄 Creating .env from .env.example..."
    cp .env.example .env
fi

# ── 2. PHP dependencies ───────────────────────────────────────────────────────
echo "📦 Installing Composer dependencies..."
composer install --no-interaction --prefer-dist --no-progress

# ── 3. Wait for MySQL ─────────────────────────────────────────────────────────
echo "⏳ Waiting for MySQL to be ready..."
MAX_TRIES=30
TRIES=0
until mysqladmin ping -h mysql -u root -p"${MYSQL_ROOT_PASSWORD:-rootpassword}" --silent 2>/dev/null; do
    TRIES=$((TRIES + 1))
    if [ $TRIES -ge $MAX_TRIES ]; then
        echo "❌ MySQL did not become ready in time. Try running: make migrate"
        exit 1
    fi
    sleep 3
done
echo "✅ MySQL is ready."

# ── 4. Database migration ─────────────────────────────────────────────────────
echo "🗄️  Running migrations..."
bin/console doctrine:migrations:migrate --no-interaction

# ── 5. Cache warmup ───────────────────────────────────────────────────────────
echo "🔥 Warming up cache..."
bin/console cache:warmup

# ── 6. Done — print access URLs ───────────────────────────────────────────────
echo ""
echo "════════════════════════════════════════════"
echo "  ✅ Vehicle API is ready!"
echo "════════════════════════════════════════════"
echo ""

if [ -n "$CODESPACE_NAME" ] && [ -n "$GITHUB_CODESPACES_PORT_FORWARDING_DOMAIN" ]; then
    API_URL="https://${CODESPACE_NAME}-8080.${GITHUB_CODESPACES_PORT_FORWARDING_DOMAIN}"
    PMA_URL="https://${CODESPACE_NAME}-8081.${GITHUB_CODESPACES_PORT_FORWARDING_DOMAIN}"
    echo "  API        → ${API_URL}"
    echo "  phpMyAdmin → ${PMA_URL}"
    echo ""
    echo "  ⚠️  IMPORTANT — copy your API URL:"
    echo "  ${API_URL}"
    echo ""
    echo "  You need this URL to configure vehicle-web."
    echo "  In the vehicle-web Codespace, run:"
    echo "    sed -i \"s|API_BASE_URL=.*|API_BASE_URL=${API_URL}|\" .env"
else
    echo "  API        → http://localhost:8080"
    echo "  phpMyAdmin → http://localhost:8081"
fi
echo ""
