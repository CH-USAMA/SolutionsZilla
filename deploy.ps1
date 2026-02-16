# ClinicFlow Automated Deployment Script
# This script automates the process of pushing changes to GitHub and pulling them on the live server.

$SERVER_IP = "192.168.21.230"
$SERVER_USER = "root"
$REMOTE_PATH = "/var/www/html" # Updated per user request

Write-Host "🚀 Starting Deployment Process..." -ForegroundColor Cyan

# 1. Push to GitHub
Write-Host "📦 Pushing changes to GitHub..." -ForegroundColor Yellow
git add .
$commitMsg = "Automatic deployment: $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')"
git commit -m $commitMsg
git push origin main

if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Failed to push to GitHub. Aborting deployment." -ForegroundColor Red
    exit $LASTEXITCODE
}

# 2. Update Live Server
Write-Host "🌐 Updating Live Server..." -ForegroundColor Yellow

$remoteCommands = @"
cd $REMOTE_PATH
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:restart
chown -R www-data:www-data storage bootstrap/cache
"@

ssh "${SERVER_USER}@${SERVER_IP}" "$remoteCommands"

if ($LASTEXITCODE -eq 0) {
    Write-Host "✅ Deployment Successful!" -ForegroundColor Green
} else {
    Write-Host "❌ Deployment failed during remote execution." -ForegroundColor Red
}
