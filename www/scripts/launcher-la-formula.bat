@echo off
setlocal EnableExtensions

REM ============================================================
REM  LA FORMULA - Lanzador del sistema (XAMPP + navegador)
REM  Edite estas rutas si XAMPP está instalado en otra carpeta.
REM ============================================================

set "XAMPP=C:\xampp"
set "APP_URL=http://localhost/farmacia/login.php"
set "WAIT_APACHE=5"
set "WAIT_MYSQL=6"

if not exist "%XAMPP%\apache_start.bat" (
    msg * "No se encontró XAMPP en %XAMPP%. Edite launcher-la-formula.bat y corrija la ruta XAMPP."
    exit /b 1
)

call :EnsureService 80 "%XAMPP%\apache_start.bat" %WAIT_APACHE%
call :EnsureService 3306 "%XAMPP%\mysql_start.bat" %WAIT_MYSQL%

start "" "%APP_URL%"
exit /b 0

:EnsureService
set "PORT=%~1"
set "START_SCRIPT=%~2"
set "SECONDS=%~3"

netstat -ano | findstr ":%PORT% " | findstr "LISTENING" >nul 2>&1
if not errorlevel 1 exit /b 0

start "" /min "%START_SCRIPT%"
timeout /t %SECONDS% /nobreak >nul
exit /b 0
