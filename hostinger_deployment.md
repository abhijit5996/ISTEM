# Laravel Deployment & CI/CD for Hostinger Cloud by : Suman Chatterjee

This document serves as the complete reference for how the **ISTEM Catalyst** Laravel 12 application was deployed to Hostinger Cloud, including both the initial manual setup and the automated GitHub Actions pipeline.

---

## Part 1: Initial Manual Server Setup

Before automation could be set up, the server needed to be configured correctly.

### 1. File Structure & Routing
Since Laravel is designed to only expose its `public/` folder, traffic needed to be securely routed there. An `.htaccess` file was placed in the `public_html` folder to act as a bridge without causing Apache 500 infinite redirect loops:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine on
    RewriteCond %{REQUEST_URI} !^/public/
    RewriteRule ^(.*)$ /public/$1 [L]
</IfModule>
```

### 2. PHP Version
Hostinger subdomains often default to older PHP versions. Under **Advanced > PHP Configuration** in hPanel, the subdomain was explicitly upgraded to **PHP 8.2** (required for Laravel 12).

### 3. Database & `.env`
A new MySQL database was created in hPanel. The application's local `.env` settings (including custom Mail, OTP, and Resend API configurations) were merged with the new Hostinger database credentials to prevent local SQLite permission errors.

```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=u121931420_laravelapp
DB_USERNAME=u121931420_dbadmin
DB_PASSWORD=[redacted]
```

---

## Part 2: Automated CI/CD Pipeline

To eliminate the need for manual zip uploads and terminal commands in the future, an automated CI/CD pipeline was created using GitHub Actions.

### 1. The GitHub Workflow (`deploy.yml`)
A workflow file was added to `.github/workflows/deploy.yml`. Upon pushing code to the `main` branch, this pipeline:
1. Provisions an Ubuntu runner and sets up PHP 8.2 and Node.js 20.
2. Installs dependencies (`composer install`) and compiles frontend assets (`npm run build`) on GitHub's fast servers.
3. Uses `rsync` to securely copy the compiled files directly into Hostinger's `public_html` directory, intentionally excluding `.env` and `storage` so they are never accidentally overwritten.
4. SSHs into Hostinger to execute a final script that creates missing cache folders, sets `chmod 775` permissions, runs database migrations, and clears caches.

### 2. Required GitHub Secrets
To allow GitHub to communicate with Hostinger, an SSH Key was generated on the server using `ssh-keygen` and authorized. The following secrets were then added to the GitHub repository:

* `HOSTINGER_HOST`: Server IP Address
* `HOSTINGER_PORT`: `65002` (Hostinger Cloud SSH Port)
* `HOSTINGER_USERNAME`: `u121931420`
* `HOSTINGER_SSH_KEY`: The generated private RSA key

### 3. Production Optimizations
For the live production environment, the `.env` file must always have the following set to secure the application against exposing stack traces to the public:
```ini
APP_ENV=production
APP_DEBUG=false
```

> [!TIP]
> **Deployment Process:** From now on, simply pushing to the `main` branch on GitHub will automatically trigger the pipeline, build the assets, and deploy the application in under 2 minutes.
