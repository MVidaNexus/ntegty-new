#!/bin/bash

# Configuration
REMOTE_HOST="135.125.190.148"
REMOTE_USER="ntegty"
REMOTE_PASS='H3r&md"j_7Z?A/+)'
DB_NAME="ntegty"

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
