#!/usr/bin/env bash
# Menjalankan Bali Global News secara lokal di http://localhost:8000
cd "$(dirname "$0")"
PORT="${PORT:-8000}"
echo "==> Bali Global News berjalan di http://localhost:$PORT  (admin: http://localhost:$PORT/admin)"
cd public && PHP_CLI_SERVER_WORKERS=4 exec php -d upload_max_filesize=10M -d post_max_size=12M -S 127.0.0.1:$PORT ../vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php
