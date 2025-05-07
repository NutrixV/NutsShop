#!/bin/bash

# Ensure script fails on any error
set -e

# Display info
echo "🚀 Deploying NutsShop to Render..."

# Ensure we're in the repo root
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$SCRIPT_DIR/.."

# Install blueprint tool if not already installed
if ! command -v render &> /dev/null
then
    echo "📥 Installing Render CLI..."
    npm install -g @render/cli
fi

# Deploy using blueprint
echo "🔄 Deploying using blueprint..."
render blueprint apply

echo "✅ Deployment initiated! Check your Render dashboard for progress." 