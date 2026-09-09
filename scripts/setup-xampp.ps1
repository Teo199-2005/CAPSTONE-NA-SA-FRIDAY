# Point XAMPP at this CodeIgniter project (run PowerShell as Administrator)
#
# Usage:
#   cd "C:\Users\Cocotantan\Downloads\public_html (4)\public_html"
#   powershell -ExecutionPolicy Bypass -File scripts\setup-xampp.ps1
#
# Then open: http://localhost/cscs/about

$ErrorActionPreference = 'Stop'

$projectRoot = Split-Path -Parent $PSScriptRoot
$linkName    = 'cscs'
$htdocs      = 'C:\xampp\htdocs'
$linkPath    = Join-Path $htdocs $linkName

if (-not (Test-Path $htdocs)) {
    Write-Host "XAMPP htdocs not found at $htdocs" -ForegroundColor Red
    Write-Host "Install XAMPP or edit `$htdocs in this script."
    exit 1
}

if (Test-Path $linkPath) {
    $item = Get-Item $linkPath -Force
    if ($item.Target -eq $projectRoot) {
        Write-Host "Already linked: $linkPath -> $projectRoot" -ForegroundColor Green
    } else {
        Write-Host "Path exists and is not this project: $linkPath" -ForegroundColor Yellow
        Write-Host "Remove or rename it, then run this script again."
        exit 1
    }
} else {
    cmd /c mklink /J "$linkPath" "$projectRoot" | Out-Null
    Write-Host "Created junction: $linkPath -> $projectRoot" -ForegroundColor Green
}

Write-Host ""
Write-Host "Open in browser:" -ForegroundColor Cyan
Write-Host "  http://localhost/$linkName/"
Write-Host "  http://localhost/$linkName/about"
Write-Host ""
Write-Host "Update .htaccess RewriteBase to /$linkName/ for this URL (or run with -LinkName public_html after backing up the old folder)."
Write-Host ""
Write-Host "Dev server (no Apache needed):" -ForegroundColor Cyan
Write-Host "  php spark serve"
Write-Host "  http://localhost:8080/about"
