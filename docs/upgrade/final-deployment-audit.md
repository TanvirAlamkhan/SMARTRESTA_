# SMARTRESTA — Final Railway Deployment Audit

## Production Environment Configuration

| Setting / Variable | Production Value | Verification Status |
| :--- | :--- | :--- |
| **Production URL** | `https://web-production-1c1cc.up.railway.app` | ACTIVE (HTTPS Verified) |
| **PHP Runtime** | PHP 8.x (PHP 8.2 / 8.3 PDO MySQL enabled) | CONFIGURED |
| **Database Engine** | Railway MySQL 8.x Plugin | CONFIGURED |
| `APP_ENV` | `production` | CONFIGURED |
| `APP_DEBUG` | `false` | CONFIGURED (Stack traces disabled) |
| `APP_TIMEZONE` | `Asia/Dhaka` | CONFIGURED |
| `SESSION_SECURE` | `true` | CONFIGURED (HTTPS Cookies) |
| **Health Probe Endpoint** | `/public/health.php?type=liveness` | CONFIGURED (HTTP 200 OK) |
| **Readiness Probe Endpoint** | `/public/health.php?type=readiness` | CONFIGURED (Database Connected) |
| **Procfile Command** | `web: vendor/bin/heroku-php-apache2 public/` | CONFIGURED |
