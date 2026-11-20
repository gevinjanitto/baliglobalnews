@echo off
cd /d %~dp0
echo ==^> Bali Global News berjalan di http://localhost:8000  (admin: http://localhost:8000/admin)
php -d upload_max_filesize=10M -d post_max_size=12M artisan serve --port=8000
