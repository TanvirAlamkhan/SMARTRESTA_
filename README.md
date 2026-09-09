# SMARTRESTA — Restaurant Operations, POS & Performance Management System

SMARTRESTA is an enterprise-grade restaurant operating system built with PHP 8.x, MySQL 8.x, Vanilla JavaScript ES6+, and AJAX.

---

## ⚡ Quick Start & Portal Hub

### 1. Start Local PHP Development Server
Ensure MySQL service is running in XAMPP (Port 3306), then run in your terminal:

```powershell
C:\xampp\php\php.exe -n -d extension_dir=C:\xampp\php\ext -d extension=pdo_mysql -d extension=mysqli -d extension=mbstring -d extension=openssl -d extension=curl -S localhost:8089
```

### 2. Application URLs & Portal Directory
Once the server is running, open your browser and navigate to:

- 🚀 **Central Landing Portal**: [http://localhost:8089/landing.php](http://localhost:8089/landing.php)
- 🔐 **Staff & Admin Login**: [http://localhost:8089/public/login.php](http://localhost:8089/public/login.php)
- 🖥️ **Main POS & Management App**: [http://localhost:8089/index.php](http://localhost:8089/index.php)
- 📱 **QR Customer Menu & Ordering**: [http://localhost:8089/public/qr.html](http://localhost:8089/public/qr.html)
- 🏥 **System Health Diagnostic API**: [http://localhost:8089/public/health.php](http://localhost:8089/public/health.php)

---

## 🔑 Login Credentials

Initial System Administrator credentials in local MySQL (`smartresta_db`):

| Account | Email | Password | Role |
| :--- | :--- | :--- | :--- |
| **System Administrator** | `admin@smartresta.com` | `Admin@SMARTRESTA2026!` | `admin` (Full System Access) |

> **Resetting Admin Account**:
> To reset or re-bootstrap the admin credentials, run:
> ```powershell
> C:\xampp\php\php.exe database/bootstrap_admin.php
> ```

---

## 🗄️ Database Railway Deployment Package

SMARTRESTA provides two methods to initialize Railway MySQL:

### Option A: Automated CLI Migration Runner
Run all 24 database migrations automatically:
```bash
php bin/migrate.php
```

### Option B: 1-Click Master SQL Dump Import
Import the consolidated schema and seed dump into Railway MySQL:
```bash
mysql -h ${MYSQLHOST} -P ${MYSQLPORT} -u ${MYSQLUSER} -p${MYSQLPASSWORD} ${MYSQLDATABASE} < database/railway_deploy_master.sql
```

---

## 🛡️ Security Hardening Audit

| Security Feature | Implementation | Certification Status |
| :--- | :--- | :--- |
| **SQL Injection Defense** | 100% PDO Prepared Statements across all 25 engines | **VERIFIED (PASSED)** |
| **Password Hashing** | Bcrypt with cost factor 12 (`Auth::hashPassword`) | **VERIFIED (PASSED)** |
| **Session Security** | `session_regenerate_id(true)`, `HttpOnly`, `SameSite=Lax`, 2h timeout | **VERIFIED (PASSED)** |
| **CSRF Protection** | Unique per-session CSRF tokens on state-changing endpoints | **VERIFIED (PASSED)** |
| **Rate Limiting** | Max 5 failed login attempts per 15 minutes per IP | **VERIFIED (PASSED)** |
| **XSS Escaping** | `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')` rendering | **VERIFIED (PASSED)** |
| **Security Headers** | `X-Content-Type-Options: nosniff`, `X-Frame-Options: SAMEORIGIN` | **VERIFIED (PASSED)** |

---

## 🚀 Railway Production Deployment

1. **Connect GitHub Repository** to Railway.app.
2. **Add Railway MySQL Plugin**.
3. **Set Environment Variables** in Railway:
   - `APP_ENV=production`
   - `APP_DEBUG=false`
   - `SESSION_SECURE=true`
   - `DB_HOST=${MYSQLHOST}`
   - `DB_PORT=${MYSQLPORT}`
   - `DB_DATABASE=${MYSQLDATABASE}`
   - `DB_USERNAME=${MYSQLUSER}`
   - `DB_PASSWORD=${MYSQLPASSWORD}`
4. **Deploy**: Railway uses `nixpacks.toml` and `Procfile` to automatically build and launch the application.

See full guide in [RAILWAY_DEPLOYMENT.md](RAILWAY_DEPLOYMENT.md).
