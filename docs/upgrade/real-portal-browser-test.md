# SMARTRESTA Upgrade — Real Portal Browser Acceptance Test Log

## Test Overview

This document records the acceptance verification of the repaired role-based portal architecture across all five SMARTRESTA portals (`/admin/`, `/manager/`, `/reception/`, `/waiter/`, `/kitchen/`).

---

## 1. Login & Routing Verification

| User Role | Credentials Used | Post-Login Final URL | Portal Title Rendered | Sidebar Navigation Items Rendered | Pass / Fail |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **System Admin** | `admin@smartresta.com` | `http://localhost/admin/` | Admin Portal | 17 Authorized Admin Navigation Items | **PASS** |
| **Manager** | `manager@smartresta.com` | `http://localhost/manager/` | Manager Portal | 13 Authorized Manager Navigation Items | **PASS** |
| **Receptionist / Cashier** | `cashier@smartresta.com` | `http://localhost/reception/` | Reception Portal | 5 Authorized Reception Navigation Items | **PASS** |
| **Waiter / Server** | `waiter@smartresta.com` | `http://localhost/waiter/` | Waiter Portal | 4 Authorized Waiter Navigation Items | **PASS** |
| **Kitchen Staff** | `kitchen@smartresta.com` | `http://localhost/kitchen/` | Kitchen Portal | 4 Authorized Kitchen Navigation Items | **PASS** |

---

## 2. Portal Identity & URL Test Matrix

1. **Direct Path Navigation (`/admin/`)**: Loads Admin Portal shell.
2. **Direct Path Navigation (`/manager/`)**: Loads Manager Portal shell.
3. **Direct Path Navigation (`/reception/`)**: Loads Reception Front Desk shell.
4. **Direct Path Navigation (`/waiter/`)**: Loads Waiter Workspace shell.
5. **Direct Path Navigation (`/kitchen/`)**: Loads Kitchen Production shell.
6. **No Hash Dependence**: Changing hash fragments does not breach role boundaries or render unauthorized views.

---

## 3. Operational Integrity Verification

- **Shared Data Flow**: Order created in `/waiter/` appears immediately in `/kitchen/` KDS and `/reception/` billing list.
- **Database Persistence**: Payments processed in `/reception/` update order status to `COMPLETED` and payment status to `PAID` in MySQL database.
- **Audit Logging**: Role logins, order status transitions, and unauthorized route access attempts are written to `audit_logs`.
