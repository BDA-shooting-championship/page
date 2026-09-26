import subprocess
import time

SSH_KEY = r"C:\Users\Acer\.ssh\id_hostinger"
SSH_USER = "u741010203"
SSH_HOST = "145.79.14.133"
SSH_PORT = "65002"
DOC_ROOT = "/home/u741010203/domains/bda-shooting-championship.sbs/public_html"

clean_cmd = (
    f"cd {DOC_ROOT} && "
    "rm -rf a .git .github hostinger bahan site src public && "
    "rm -f *.zip *.bak* *.md *.ps1 index.html next.config.js package*.json postcss.config.js tailwind.config.js tsconfig.json && "
    "rm -f includes/*.bak* && "
    "rm -rf assets/backups && "
    "rm -f assets/*LOGO* && "
    "rm -f assets/juknis-bda-shooting-championship-2026.pdf && "
    "ls -la && "
    "echo CLEANUP_SUCCESSFUL"
)

cmd = [
    "ssh",
    "-p", SSH_PORT,
    "-i", SSH_KEY,
    "-o", "StrictHostKeyChecking=no",
    "-o", "ConnectTimeout=25",
    f"{SSH_USER}@{SSH_HOST}",
    f"bash -c '{clean_cmd}'"
]

print("Executing Hostinger public_html cleanup...")
for attempt in range(1, 4):
    print(f"[Attempt {attempt}/3] Connecting to Hostinger...")
    proc = subprocess.run(cmd, capture_output=True, text=True)
    print("STDOUT:\n", proc.stdout.strip())
    if proc.stderr:
        print("STDERR:\n", proc.stderr.strip())
    
    if "CLEANUP_SUCCESSFUL" in proc.stdout:
        print("\n>>> CLEANUP WAS SUCCESSFUL! <<<")
        break
    else:
        print("Waiting 20 seconds before retry...")
        time.sleep(20)
