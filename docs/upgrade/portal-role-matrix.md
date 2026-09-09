# SMARTRESTA — Portal Role Matrix & Server-Side Access Control

## 1. Matrix of Portal Route Authorization

| Target Portal | Admin Role | Manager Role | Reception Role | Waiter Role | Kitchen Role | Unauthenticated |
| :--- | :---: | :---: | :---: | :---: | :---: | :---: |
| `/admin/` | **ALLOW** | **DENY** (403) | **DENY** (403) | **DENY** (403) | **DENY** (403) | Redirect Login |
| `/manager/` | **ALLOW** | **ALLOW** | **DENY** (403) | **DENY** (403) | **DENY** (403) | Redirect Login |
| `/reception/` | **ALLOW** | **ALLOW** | **ALLOW** | **DENY** (403) | **DENY** (403) | Redirect Login |
| `/waiter/` | **ALLOW** | **ALLOW** | **DENY** (403) | **ALLOW** | **DENY** (403) | Redirect Login |
| `/kitchen/` | **ALLOW** | **ALLOW** | **DENY** (403) | **DENY** (403) | **ALLOW** | Redirect Login |

---

## 2. Server-Side Enforcement Mechanism

Server-side enforcement is executed directly at the top of each portal entrypoint file:

```php
// Example: inside /waiter/index.php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Router.php';

// Enforces session authentication & checks if role is allowed
Router::authorizePortal('waiter');
```

Role spoofing via URL parameters (`?role=admin`) or client-side `localStorage` modifications is impossible because `Router::authorizePortal()` reads the user's role exclusively from trusted server-side PHP session state (`$_SESSION['user_role']`).
