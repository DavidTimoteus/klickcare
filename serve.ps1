$phpPath = "C:\laragon\bin\php\php-8.3.10-Win32-vs16-x64"
if ($env:PATH -notlike "*$phpPath*") {
    $env:PATH = "$phpPath;" + $env:PATH
}
Write-Host "========================================================" -ForegroundColor Cyan
Write-Host "  KlickCare Polinema - Local Development Server" -ForegroundColor Green
Write-Host "  URL: http://127.0.0.1:8001" -ForegroundColor Yellow
Write-Host "========================================================" -ForegroundColor Cyan
Write-Host ""
php artisan serve --port=8001
