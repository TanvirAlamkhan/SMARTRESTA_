# SMARTRESTA — Five Separate Portal Architecture (Prompt 02)

## 1. Overview & Architectural Goals

Upgrade Prompt 02 establishes a secure, server-side role-gated portal architecture for **SMARTRESTA**.

Instead of loading all role views into a single shared SPA interface and hiding menu items on the client side, the platform now enforces server-side portal routing via [core/Router.php](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/core/Router.php).

### Five Standalone Role Portals:
1. **Admin Portal**: `/admin/` (Entrypoint: [admin/index.php](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/admin/index.php))
2. **Manager Portal**: `/manager/` (Entrypoint: [manager/index.php](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/manager/index.php))
3. **Reception Portal**: `/reception/` (Entrypoint: [reception/index.php](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/reception/index.php))
4. **Waiter Portal**: `/waiter/` (Entrypoint: [waiter/index.php](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/waiter/index.php))
5. **Kitchen Portal**: `/kitchen/` (Entrypoint: [kitchen/index.php](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/kitchen/index.php))

---

## 2. Server-Side Routing Flow

```text
                               ┌─────────────────────────┐
                               │   Login Request (POST)  │
                               │  (api/v1/auth/login.php)│
                               └────────────┬────────────┘
                                            │
                               ┌────────────▼────────────┐
                               │     Auth::login()       │
                               │  Session Authenticated  │
                               └────────────┬────────────┘
                                            │
                               ┌────────────▼────────────┐
                               │  Router::getPortalPath  │
                               │   Role-Based Redirect   │
                               └────────────┬────────────┘
                                            │
       ┌─────────────────┬──────────────────┼─────────────────┬──────────────────┐
       ▼                 ▼                  ▼                 ▼                  ▼
   /admin/           /manager/         /reception/        /waiter/           /kitchen/
   Admin Portal     Manager Portal    Reception Portal   Waiter Portal      Kitchen KDS
```

---

## 3. Guarantees & Constraints

- **Shared Database**: All portals interact with the same underlying MySQL database (`smartresta_db`).
- **Shared Business Engines**: Order creation, ticket routing, payment settlement, and stock deductions use the existing core engines (`core/OrderEngine.php`, `core/KDSEngine.php`, `core/PaymentEngine.php`, etc.).
- **Server-Side Security**: Portal entrypoints execute `Router::authorizePortal($portal)` before serving content. Unauthorized direct URL access attempts trigger HTTP 403 Forbidden pages.
