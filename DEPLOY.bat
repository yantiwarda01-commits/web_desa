@echo off
REM ============================================
REM Website Desa - Quick Deploy Script
REM ============================================

echo.
echo ================================================
echo   Website Desa - Deployment Script
echo ================================================
echo.

REM Check if Docker is running
docker info >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERROR] Docker is not running!
    echo Please start Docker Desktop first.
    pause
    exit /b 1
)

echo [1/7] Docker is running... OK
echo.

REM Stop existing containers
echo [2/7] Stopping existing containers...
docker-compose -f docker-compose-simple.yml down
echo.

REM Clean build
echo [3/7] Building new images (this may take a few minutes)...
docker-compose -f docker-compose-simple.yml build --no-cache
if %errorlevel% neq 0 (
    echo [ERROR] Build failed!
    pause
    exit /b 1
)
echo.

REM Start containers
echo [4/7] Starting containers...
docker-compose -f docker-compose-simple.yml up -d
if %errorlevel% neq 0 (
    echo [ERROR] Failed to start containers!
    pause
    exit /b 1
)
echo.

REM Wait for MySQL to be ready
echo [5/7] Waiting for MySQL to initialize (60 seconds)...
timeout /t 60 /nobreak >nul
echo.

REM Check status
echo [6/7] Checking container status...
docker-compose -f docker-compose-simple.yml ps
echo.

REM Final check
echo [7/7] Testing website...
timeout /t 5 /nobreak >nul

echo.
echo ================================================
echo   Deployment Complete!
echo ================================================
echo.
echo Open these URLs in your browser:
echo.
echo   1. Debug Info:       http://localhost:8080/debug-info.php
echo   2. Connection Test:  http://localhost:8080/test-connection.php
echo   3. Main Website:     http://localhost:8080
echo   4. PhpMyAdmin:       http://localhost:8081
echo.
echo Opening debug page...
start http://localhost:8080/debug-info.php
echo.
echo If you see errors, check logs with:
echo   docker-compose -f docker-compose-simple.yml logs
echo.
echo To stop: docker-compose -f docker-compose-simple.yml down
echo.
pause
