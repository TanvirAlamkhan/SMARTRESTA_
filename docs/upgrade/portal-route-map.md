# SMARTRESTA — Portal Route Map & Redirection Spec

## 1. Role-to-Portal Route Mapping

The table below details the authoritative mapping enforced by [core/Router.php](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/core/Router.php):

| User Role | Assigned Portal Route | Entrypoint File | Default Section View |
| :--- | :--- | :--- | :--- |
| **Admin** / **Administrator** | `/admin/` | [admin/index.php](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/admin/index.php) | `#admin` (Manager Dashboard Overview) |
| **Manager** / **Supervisor** | `/manager/` | [manager/index.php](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/manager/index.php) | `#admin` (Operational Performance) |
| **Reception** / **Cashier** | `/reception/` | [reception/index.php](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/reception/index.php) | `#payments` (Billing & Settlement) |
| **Waiter** / **Server** | `/waiter/` | [waiter/index.php](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/waiter/index.php) | `#pos` (POS & Waiter Ordering) |
| **Kitchen** / **Chef** | `/kitchen/` | [kitchen/index.php](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/kitchen/index.php) | `#kds` (Kitchen Display System) |

---

## 2. Redirection & Error Handling Policy

- **Unauthenticated Access**: Direct visits to `/admin/`, `/manager/`, `/reception/`, `/waiter/`, or `/kitchen/` without an active session are intercepted by `Auth::requireAuth()`, redirecting to `public/login.php` or `landing.php`.
- **Unauthorized Cross-Portal Access**: When an authenticated user with role `waiter` manually navigates to `/admin/`, `Router::authorizePortal('admin')` blocks execution, logs an `UNAUTHORIZED_PORTAL_ACCESS` audit event, and renders a styled HTTP 403 Forbidden page containing a link back to `/waiter/`.
- **Post-Login Routing**: `api/v1/auth/login.php` calls `Router::getPortalPath()` to return the exact portal URL for client-side navigation.
