param(
    [int]$Port = 8000
)

$ErrorActionPreference = 'Stop'

$projectRoot = $PSScriptRoot
$stateDir = Join-Path $projectRoot '.dev'
$pidFile = Join-Path $stateDir 'php-server.pid'
$stdoutLog = Join-Path $stateDir 'php-server.log'
$stderrLog = Join-Path $stateDir 'php-server-error.log'

function Get-PhpExe {
    $command = Get-Command php -ErrorAction SilentlyContinue
    if ($command) {
        return $command.Source
    }

    $candidates = @(
        (Join-Path $env:LOCALAPPDATA 'Microsoft\WinGet\Packages\PHP.PHP.8.3_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe'),
        (Join-Path $env:LOCALAPPDATA 'Microsoft\WinGet\Packages\PHP.PHP.8.4_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe'),
        (Join-Path $env:LOCALAPPDATA 'Microsoft\WinGet\Packages\PHP.PHP.8.2_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe')
    )

    foreach ($candidate in $candidates) {
        if (Test-Path $candidate) {
            return $candidate
        }
    }

    throw "PHP est introuvable. Relance l'installation ou verifie le PATH."
}

if (-not (Test-Path $stateDir)) {
    New-Item -ItemType Directory -Path $stateDir | Out-Null
}

if (Test-Path $pidFile) {
    $existingPid = Get-Content $pidFile -ErrorAction SilentlyContinue
    if ($existingPid) {
        $existingProcess = Get-Process -Id $existingPid -ErrorAction SilentlyContinue
        if ($existingProcess) {
            Stop-Process -Id $existingPid -Force
            Start-Sleep -Milliseconds 300
        }
    }
    Remove-Item $pidFile -ErrorAction SilentlyContinue
}

$phpExe = Get-PhpExe
$phpRoot = Split-Path -Parent $phpExe
$extensionDir = Join-Path $phpRoot 'ext'

if (-not (Test-Path $extensionDir)) {
    throw "Le dossier d'extensions PHP est introuvable : $extensionDir"
}

$arguments = @(
    '-d', "extension_dir=$extensionDir",
    '-d', 'extension=curl',
    '-d', 'extension=openssl',
    '-S', "127.0.0.1:$Port",
    '-t', $projectRoot
)

$process = Start-Process `
    -FilePath $phpExe `
    -ArgumentList $arguments `
    -WorkingDirectory $projectRoot `
    -RedirectStandardOutput $stdoutLog `
    -RedirectStandardError $stderrLog `
    -PassThru `
    -WindowStyle Hidden

Set-Content -Path $pidFile -Value $process.Id
Start-Sleep -Seconds 2

if ($process.HasExited) {
    $errorOutput = ''
    if (Test-Path $stderrLog) {
        $errorOutput = Get-Content $stderrLog -Raw
    }

    throw "Le serveur PHP n'a pas demarre correctement.`n$errorOutput"
}

Write-Output "Serveur PHP actif sur http://127.0.0.1:$Port/index.html"
Write-Output "Formulaire devis : http://127.0.0.1:$Port/devis.html"
Write-Output "PID : $($process.Id)"
Write-Output "Logs : $stdoutLog"

