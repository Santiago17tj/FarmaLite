#Requires -Version 5.1
<#
.SYNOPSIS
    Crea un acceso directo en el Escritorio para abrir LA FORMULA con doble clic.

.DESCRIPTION
    Genera un .lnk que ejecuta launcher-la-formula.vbs (sin ventana de consola).
    Inicia Apache/MySQL de XAMPP si no están corriendo y abre el login en el navegador.

.EXAMPLE
    powershell -ExecutionPolicy Bypass -File ".\crear-acceso-directo.ps1"
#>

$ErrorActionPreference = "Stop"

$ScriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
$LauncherVbs = Join-Path $ScriptDir "launcher-la-formula.vbs"
$LauncherBat = Join-Path $ScriptDir "launcher-la-formula.bat"

if (-not (Test-Path $LauncherVbs)) {
    Write-Error "No se encontró: $LauncherVbs"
}

$ShortcutName = "LA FORMULA - Droguería.lnk"
$Desktop = [Environment]::GetFolderPath("Desktop")
$ShortcutPath = Join-Path $Desktop $ShortcutName

$WshShell = New-Object -ComObject WScript.Shell
$Shortcut = $WshShell.CreateShortcut($ShortcutPath)
$Shortcut.TargetPath = "wscript.exe"
$Shortcut.Arguments = "`"$LauncherVbs`""
$Shortcut.WorkingDirectory = $ScriptDir
$Shortcut.WindowStyle = 7
$Shortcut.Description = "Abrir sistema de gestión LA FORMULA (farmacia)"

# Icono: intenta usar el favicon del proyecto; si no, icono de Windows
$ProjectRoot = Split-Path $ScriptDir -Parent
$IconCandidates = @(
    (Join-Path $ProjectRoot "assets\uploadImage\Logo\favicon.png"),
    (Join-Path $ProjectRoot "assets\uploadImage\Logo\logo.png"),
    "$env:SystemRoot\System32\imageres.dll"
)

foreach ($icon in $IconCandidates) {
    if (Test-Path $icon) {
        if ($icon -like "*.dll") {
            $Shortcut.IconLocation = "$icon,109"
        } else {
            $Shortcut.IconLocation = $icon
        }
        break
    }
}

$Shortcut.Save()

Write-Host ""
Write-Host "Acceso directo creado:" -ForegroundColor Green
Write-Host "  $ShortcutPath"
Write-Host ""
Write-Host "Prueba con doble clic. Debe abrir:" -ForegroundColor Cyan
Write-Host "  http://localhost/farmacia/login.php"
Write-Host ""
Write-Host "Requisitos en esta PC:" -ForegroundColor Yellow
Write-Host "  - XAMPP instalado (por defecto C:\xampp)"
Write-Host "  - Proyecto copiado en C:\xampp\htdocs\farmacia"
Write-Host "  - Base de datos 'farmacia' importada"
Write-Host ""

if (-not (Test-Path "C:\xampp\xampp-control.exe")) {
    Write-Warning "No se detectó XAMPP en C:\xampp. Edite scripts\launcher-la-formula.bat si la ruta es otra."
}

if (-not (Test-Path "C:\xampp\htdocs\farmacia\login.php")) {
    Write-Warning "No se encontró la app en C:\xampp\htdocs\farmacia. Copie el proyecto antes de usar el acceso directo."
}

Write-Host "Listo." -ForegroundColor Green
