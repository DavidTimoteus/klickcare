@echo off
title KlickCare Dev Server
set "PATH=C:\laragon\bin\php\php-8.3.10-Win32-vs16-x64;%PATH%"
echo ========================================================
echo   KlickCare Polinema - Local Development Server
echo   URL: http://127.0.0.1:8001
echo ========================================================
echo.
php artisan serve --port=8001
pause
