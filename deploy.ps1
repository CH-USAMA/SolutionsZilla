# ClinicFlow Automated Deployment Script
# This script automates the process of pushing changes to GitHub and pulling them on the live server.

$SERVER_IP = "192.168.21.230"
$SERVER_USER = "root"
$SERVER_PASS = "hobo123"
$REMOTE_PATH = "/var/www/html" # Updated per user request

Write-Host "Starting Deployment Process..." -ForegroundColor Cyan

# 1. Push to GitHub
Write-Host "Pushing changes to GitHub..." -ForegroundColor Yellow
git add .
$commitMsg = "Automatic deployment: $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')"
git commit -m $commitMsg
git push origin main

if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Failed to push to GitHub. Aborting deployment." -ForegroundColor Red
    exit $LASTEXITCODE
}

# 2. Update Live Server
Write-Host "Update Live Server..." -ForegroundColor Yellow

$remoteCommands = @"
cd $REMOTE_PATH
git pull origin main
export COMPOSER_ALLOW_SUPERUSER=1
composer install --no-dev --optimize-autoloader
php artisan migrate --force

# Frontend Assets
npm install --no-audit
npm run build

# WhatsApp Gateway Management
echo "Updating WhatsApp Gateway..."
cd $REMOTE_PATH/whatsapp-gateway
npm install --no-audit
if ! command -v pm2 &> /dev/null
then
    echo "PM2 not found, installing globally..."
    npm install -g pm2
fi

# Start or Restart with PM2
pm2 describe whatsapp-gateway > /dev/null
if [ $? -eq 0 ]
then
    echo "Restarting existing WhatsApp Gateway..."
    pm2 restart whatsapp-gateway
else
    echo "Starting new WhatsApp Gateway..."
    pm2 start server.js --name whatsapp-gateway
fi

# Laravel Finalization
cd $REMOTE_PATH
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:restart
chown -R www-data:www-data storage bootstrap/cache public
"@

# Normalize line endings from CRLF to LF for Linux Bash compatibility
$normalizedCommands = $remoteCommands -replace "`r`n", "`n"

$sshTarget = "$SERVER_USER@$SERVER_IP"
Write-Host "Connecting to $sshTarget using password..." -ForegroundColor Gray

# Use plink (PuTTY) to automate password input if available
if (Get-Command plink -ErrorAction SilentlyContinue) {
    plink -batch -pw $SERVER_PASS $sshTarget $normalizedCommands
}
else {
    Write-Host "plink not found, falling back to standard ssh (manual password required)..." -ForegroundColor Magenta
    ssh $sshTarget $normalizedCommands
}

if ($LASTEXITCODE -eq 0) {
    Write-Host "Deployment Successful!" -ForegroundColor Green
}
else {
    Write-Host "Deployment failed during remote execution." -ForegroundColor Red
}


# powershell -ExecutionPolicy Bypass -File .\deploy.ps1