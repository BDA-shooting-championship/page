param(
    [Parameter(Mandatory=$true)][string]$LocalFile,
    [Parameter(Mandatory=$true)][string]$RemoteFile
)

$bytes = [System.IO.File]::ReadAllBytes($LocalFile)
$maxRetries = 3
$success = $false
$lastError = ""

for ($attempt = 1; $attempt -le $maxRetries; $attempt++) {
    try {
        $psi = New-Object System.Diagnostics.ProcessStartInfo
        $psi.FileName = "ssh"
        $psi.Arguments = "-p 65002 -i C:\Users\Acer\.ssh\id_hostinger -o ConnectTimeout=15 -o StrictHostKeyChecking=no u741010203@145.79.14.133 `"cat > '$RemoteFile'`""
        $psi.UseShellExecute = $false
        $psi.RedirectStandardInput = $true
        $psi.RedirectStandardError = $true
        $psi.RedirectStandardOutput = $false

        $p = [System.Diagnostics.Process]::Start($psi)
        $p.StandardInput.BaseStream.Write($bytes, 0, $bytes.Length)
        $p.StandardInput.BaseStream.Flush()
        $p.StandardInput.Close()

        $err = $p.StandardError.ReadToEnd()
        $p.WaitForExit()

        if ($p.ExitCode -eq 0) {
            $success = $true
            Write-Host "Successfully deployed $LocalFile -> $RemoteFile ($($bytes.Length) bytes)"
            break
        } else {
            $lastError = $err
            Write-Warning "Attempt $attempt failed for ${LocalFile}: ${err}. Retrying..."
            Start-Sleep -Seconds 2
        }
    } catch {
        $lastError = $_.Exception.Message
        Write-Warning "Attempt $attempt encountered exception: ${lastError}. Retrying..."
        Start-Sleep -Seconds 2
    }
}

if (-not $success) {
    Write-Error "Deployment failed for ${LocalFile} after $maxRetries attempts: ${lastError}"
    exit 1
}
