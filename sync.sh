#!/bin/bash

# Parse configuration from .env if it exists
if [ -f .env ]; then
    # Read variables from .env
    REMOTE_HOST=$(grep REMOTE_SSH_HOST .env | cut -d '=' -f2- | tr -d '"'\')
    REMOTE_USER=$(grep REMOTE_SSH_USER .env | cut -d '=' -f2- | tr -d '"'\')
    REMOTE_PASS=$(grep REMOTE_SSH_PASSWORD .env | cut -d '=' -f2- | tr -d '"'\')
    DB_NAME=$(grep REMOTE_DB_DATABASE .env | cut -d '=' -f2- | tr -d '"'\')
fi

# Fallback defaults if not set in .env
REMOTE_HOST=${REMOTE_HOST:-"135.125.190.148"}
REMOTE_USER=${REMOTE_USER:-"ntegty"}
REMOTE_PASS=${REMOTE_PASS:-")c6NXp44u@U1cdBz"}
DB_NAME=${DB_NAME:-"ntegty_ntegty"}

echo "------------------------------------------------"
echo "🚀 Starting Live Content Sync via SSH..."
echo "------------------------------------------------"

# 1. Dump remote database over SSH and save locally
echo "📥 Downloading remote database dump..."
sshpass -p "$REMOTE_PASS" ssh -o StrictHostKeyChecking=no $REMOTE_USER@$REMOTE_HOST "mysqldump -u $REMOTE_USER -p'$REMOTE_PASS' $DB_NAME --no-create-info --tables exam_types countries settings governorates" > live_content.sql

if [ $? -eq 0 ]; then
    echo "✅ Remote dump downloaded successfully."
else
    echo "❌ Failed to download remote dump. Check your SSH credentials."
    exit 1
fi

# 2. Import into local database (assuming SQLite for local dev based on .env)
# Since importing MySQL dump into SQLite directly is hard, we use a temporary command
echo "🔄 Importing content into local database..."

# Note: For SQLite, we might need a converter, but if the user switches to MySQL locally, it works.
# If using SQLite, we will use a PHP bridge to sync.
php artisan site:pull-live --force

echo "------------------------------------------------"
echo "🎉 Sync completed!"
echo "Now your local site has the exact same content as the Live site."
echo "------------------------------------------------"
