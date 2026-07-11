@echo off
chcp 65001 >nul
echo.
echo  Creando acceso directo de LA FORMULA en el Escritorio...
echo.

powershell -NoProfile -ExecutionPolicy Bypass -File "%~dp0crear-acceso-directo.ps1"

echo.
pause
