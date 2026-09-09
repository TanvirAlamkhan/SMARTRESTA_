# SMARTRESTA — Quick Start & Login Guide

## ⚡ 1. Start Local Web Server

Run MySQL in XAMPP (Port 3306), then start the local PHP server from your project folder:

```powershell
C:\xampp\php\php.exe -n -d extension_dir=C:\xampp\php\ext -d extension=pdo_mysql -d extension=mysqli -d extension=mbstring -d extension=openssl -d extension=curl -S localhost:8089
```

---

## 🌐 2. Application URLs & Portal Directory

Once the server is running, navigate to any of the following URLs in your browser:

- 🚀 **Central Landing Portal**: [http://localhost:8089/landing.php](http://localhost:8089/landing.php)
- 🔐 **Staff & Admin Login**: [http://localhost:8089/public/login.php](http://localhost:8089/public/login.php)
- 🖥️ **Main POS & Management App**: [http://localhost:8089/index.php](http://localhost:8089/index.php)
- 📱 **QR Customer Menu & Ordering**: [http://localhost:8089/public/qr.html](http://localhost:8089/public/qr.html)
- 🏥 **System Health Diagnostic API**: [http://localhost:8089/public/health.php](http://localhost:8089/public/health.php)

---

## 🔑 3. Login Credentials

| Role | Email | Password |
|---|---|---|
| **System Administrator** | `admin@smartresta.com` | `amer25` |

> **To Reset Admin Credentials**:
> ```powershell
> C:\xampp\php\php.exe database/bootstrap_admin.php
> ```

---

## 🗄️ 4. Railway Database Migration & Master Dump

- Run automated migration runner:
  ```bash
  php bin/migrate.php
  ```
- Or import master SQL dump into Railway MySQL:
  ```bash
  mysql -h ${MYSQLHOST} -P ${MYSQLPORT} -u ${MYSQLUSER} -p${MYSQLPASSWORD} ${MYSQLDATABASE} < database/railway_deploy_master.sql
  ```
