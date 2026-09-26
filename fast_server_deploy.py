import subprocess
import time
import urllib.request
import ssl

SSH_KEY = r"C:\Users\Acer\.ssh\id_hostinger"
SSH_USER = "u741010203"
SSH_HOST = "145.79.14.133"
SSH_PORT = "65002"
REMOTE_DOC_ROOT = "/home/u741010203/domains/bda-shooting-championship.sbs/public_html"

bash_script = (
    "rm -rf /tmp/bda_deploy && "
    "git clone --depth 1 https://github.com/BDA-shooting-championship/page.git /tmp/bda_deploy && "
    f"cp -rf /tmp/bda_deploy/site/* {REMOTE_DOC_ROOT}/ && "
    "rm -rf /tmp/bda_deploy && "
    "echo DEPLOY_SUCCESSFUL_2026"
)

cmd = [
    "ssh",
    "-p", SSH_PORT,
    "-i", SSH_KEY,
    "-o", "StrictHostKeyChecking=no",
    "-o", "ConnectTimeout=25",
    f"{SSH_USER}@{SSH_HOST}",
    f"bash -c '{bash_script}'"
]

print("Executing direct server-side deployment via GitHub...")
for attempt in range(1, 4):
    print(f"[Attempt {attempt}/3] Connecting to Hostinger...")
    proc = subprocess.run(cmd, capture_output=True, text=True)
    print("STDOUT:", proc.stdout.strip())
    print("STDERR:", proc.stderr.strip())
    print("ReturnCode:", proc.returncode)
    
    if "DEPLOY_SUCCESSFUL_2026" in proc.stdout:
        print("\n>>> Server-side deploy executed successfully! <<<")
        break
    else:
        print("Waiting 25 seconds for Hostinger rate limiter before retry...")
        time.sleep(25)

# Verify live website
print("\nVerifying live website at https://bda-shooting-championship.sbs ...")
ctx = ssl.create_default_context()
ctx.check_hostname = False
ctx.verify_mode = ssl.CERT_NONE

req = urllib.request.Request("https://bda-shooting-championship.sbs", headers={"User-Agent": "Mozilla/5.0 (DeploymentVerifier)"})
try:
    with urllib.request.urlopen(req, context=ctx, timeout=15) as resp:
        html = resp.read().decode('utf-8', errors='ignore')
    rule5_ok = "khusus untuk kategori Dueling Plat 15M (Khusus BDA Korbrimob POLRI)" in html
    logo_ok = "logo-championship.png" in html
    print(f"HTTP Status: {resp.status}")
    print(f"Rule 5 (100k khusus BDA) present: {rule5_ok}")
    print(f"Logo BSC 2026 present: {logo_ok}")
    if rule5_ok:
        print("\n>>> LIVE DEPLOYMENT CONFIRMED PASSING ON PRODUCTION SERVER! <<<")
    else:
        print("\n>>> Warning: content not yet visible on HTTP response. <<<")
except Exception as e:
    print(f"Verification error: {e}")
