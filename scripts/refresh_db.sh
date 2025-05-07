#!/bin/bash

# Ensure script fails on any error
set -e

# Display info
echo "🔄 Refreshing NutsShop database..."

# Ensure we're in the repo root
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$SCRIPT_DIR/../backend"

# Run migrations fresh with seed
docker-compose exec php php artisan migrate:fresh --seed

echo "✅ Database refreshed successfully!" 