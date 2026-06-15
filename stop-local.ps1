$ErrorActionPreference = 'Stop'

$pidFile = Join-Path $PSScriptRoot '.dev\php-server.pid'

if (-not (Test-Path $pidFile)) {
    Write-Output 'Aucun serveur PHP local enregistre.'
    exit 0
}

$pid = Get-Content $pidFile -ErrorAction SilentlyContinue
if (-not $pid) {
    Remove-Item $pidFile -ErrorAction SilentlyContinue
    Write-Output 'Fichier PID vide. Rien a arreter.'
    exit 0
}

$process = Get-Process -Id $pid -ErrorAction SilentlyContinue
if ($process) {
    Stop-Process -Id $pid -Force
}

Remove-Item $pidFile -ErrorAction SilentlyContinue
Write-Output "Serveur PHP local arrete (PID $pid)."

