$filesToDeploy = @(
    @{ local = "site\schema.sql"; remote = "domains/bda-shooting-championship.sbs/public_html/schema.sql" },
    @{ local = "site\includes\config.php"; remote = "domains/bda-shooting-championship.sbs/public_html/includes/config.php" },
    @{ local = "site\includes\footer.php"; remote = "domains/bda-shooting-championship.sbs/public_html/includes/footer.php" },
    @{ local = "site\index.php"; remote = "domains/bda-shooting-championship.sbs/public_html/index.php" },
    @{ local = "site\daftar.php"; remote = "domains/bda-shooting-championship.sbs/public_html/daftar.php" },
    @{ local = "site\live-score.php"; remote = "domains/bda-shooting-championship.sbs/public_html/live-score.php" },
    @{ local = "site\admin\index.php"; remote = "domains/bda-shooting-championship.sbs/public_html/admin/index.php" },
    @{ local = "site\admin\export-excel.php"; remote = "domains/bda-shooting-championship.sbs/public_html/admin/export-excel.php" },
    @{ local = "site\api\public-scores.php"; remote = "domains/bda-shooting-championship.sbs/public_html/api/public-scores.php" },
    @{ local = "site\api\scores-dueling.php"; remote = "domains/bda-shooting-championship.sbs/public_html/api/scores-dueling.php" },
    @{ local = "site\JUKNIS_BDA_SHOOTING_CHAMPIONSHIP_2026.pdf"; remote = "domains/bda-shooting-championship.sbs/public_html/JUKNIS_BDA_SHOOTING_CHAMPIONSHIP_2026.pdf" }
)

$allSuccess = $true

foreach ($f in $filesToDeploy) {
    $localFullPath = Join-Path "d:\BDA-shooting-championship" $f.local
    Write-Host ">>> Deploying $($f.local) -> $($f.remote)..."
    try {
        & "d:\BDA-shooting-championship\deploy_file.ps1" -LocalFile $localFullPath -RemoteFile $f.remote
        if ($LASTEXITCODE -ne 0 -and -not $?) {
            Write-Host "Error deploying $($f.local)" -ForegroundColor Red
            $allSuccess = $false
        }
    } catch {
        Write-Host "Exception deploying $($f.local): $_" -ForegroundColor Red
        $allSuccess = $false
    }
    Start-Sleep -Seconds 1
}

if ($allSuccess) {
    Write-Host "ALL FILES SUCCESSFULLY DEPLOYED!" -ForegroundColor Green
} else {
    Write-Host "SOME FILES FAILED DEPLOYMENT!" -ForegroundColor Red
}
