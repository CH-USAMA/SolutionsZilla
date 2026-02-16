---
description: how to deploy the application to the live server
---

This workflow automates the deployment process by pushing local changes to GitHub and then triggering a pull and update on the live server.

### Prerequisites
1. **GitHub Access**: Ensure your local machine is logged into GitHub and has push access to the repository.
2. **Server Access**: Ensure your local machine can reach `192.168.21.230` (IP) directly or via VPN.
3. **SSH Key (Recommended)**: To avoid typing the password every time, run the following on your local machine if you haven't already:
   ```powershell
   ssh-keygen -t ed25519
   ssh-copy-id root@192.168.21.230
   ```
   *(If `ssh-copy-id` is not available on Windows, you can manually append your `~/.ssh/id_ed25519.pub` content to `/root/.ssh/authorized_keys` on the server).*

### 📦 Steps to Deploy

1. Check that all your changes are ready.
// turbo
2. Run the deployment script:
   ```powershell
   .\deploy.ps1
   ```

### 🛠️ What happens during deployment?
- **GitHub**: All local changes are added, committed with a timestamp, and pushed to the `main` branch.
- **Server**: 
  - Navigates to `/var/www/html`.
  - Performs `git pull origin main`.
  - Runs `composer install` for production.
  - Executes `php artisan migrate` (forced).
  - Optimizes the application (`config:cache`, `route:cache`, `view:cache`).
  - Restarts the queue worker.
