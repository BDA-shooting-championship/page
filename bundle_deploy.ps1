$sshKey = "C:\Users\Acer\.ssh\id_hostinger"
$sshUser = "u741010203"
$sshHost = "145.79.14.133"
$sshPort = "65002"
$remoteRoot = "/home/u741010203/domains/bda-shooting-championship.sbs/public_html"

$files = @(
    @{ local = "site\includes\config.php"; remote = "includes/config.php" },
    @{ local = "site\includes\footer.php"; remote = "includes/footer.php" },
    @{ local = "site\index.php"; remote = "index.php" },
    @{ local = "site\admin\index.php"; remote = "admin/index.php" }
)

Write-Host "Deploying updated dates (17-18 Oktober) to Hostinger..." -ForegroundColor Cyan

foreach ($item in $files) {
    $localPath = Join-Path "d:\BDA-shooting-championship" $item.local
    $remoteDest = "${sshUser}@${sshHost}:${remoteRoot}/$($item.remote)"

    Write-Host "Uploading $($item.local) -> $($item.remote)..." -NoNewline
    $output = & scp -O -P $sshPort -i $sshKey -o StrictHostKeyChecking=no -o ConnectTimeout=15 $localPath $remoteDest 2>&1
    if ($LASTEXITCODE -eq 0) {
        Write-Host " [OK]" -ForegroundColor Green
    } else {
        Write-Host " [FAILED] $output" -ForegroundColor Red
    }
    Start-Sleep -Seconds 5
}

Write-Host "Deployment finished!" -ForegroundColor Cyan
