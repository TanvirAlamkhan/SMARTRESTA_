# SMARTRESTA — System Upgrade Roadmap

## Phase 1: Complete System Audit & Blueprint (Prompt 01 - CURRENT)
- [x] Full codebase, database, API, and engine audit.
- [x] Fix missing `helpers/response.php` and subfolder asset path resolution.
- [x] Produce architecture blueprint for 5 separate role portals (`/admin`, `/manager`, `/reception`, `/waiter`, `/kitchen`).
- [x] Create 8 comprehensive audit documentation files in `docs/upgrade/`.

---

## Phase 2: Role Authentication & Router Middleware (Prompt 02 Target)
- [ ] Create role-gated router middleware in PHP (`core/Router.php`).
- [ ] Implement server-side route access guards for `/admin`, `/manager`, `/reception`, `/waiter`, and `/kitchen`.
- [ ] Ensure non-authorized direct URL visits (e.g. waiter visiting `/admin/`) trigger clean HTTP 403 Forbidden redirects to their designated portal.

---

## Phase 3: Portal Construction & UI Separation (Prompt 03 Target)
- [ ] Build **Waiter Portal** (`/waiter`): Touch-friendly mobile/tablet order taking, table map, cart.
- [ ] Build **Kitchen Portal** (`/kitchen`): Station-filtered 4-column KDS with real-time timers and recall.
- [ ] Build **Reception Portal** (`/reception`): Fast table lookup, order itemization, multi-payment, receipt printing.
- [ ] Build **Manager Portal** (`/manager`): Floor overview, waiter matrix, shift close, void approvals.
- [ ] Build **Admin Portal** (`/admin`): Complete system config, RBAC, master menu, global reports, audit logs.

---

## Phase 4: Shared Database & Cross-Module Connection Verification (Prompt 04 Target)
- [ ] Trace Waiter order creation -> Kitchen ticket dispatch -> Reception settlement -> Manager shift reconciliation -> Admin audit log.
- [ ] Verify 100% data consistency across single authoritative MySQL database tables.

---

## Phase 5: Automated Testing & Production Readiness (Prompt 05 Target)
- [ ] Run full Selenium JUnit 5 automated test suite (`SMARTRESTAclass.java`).
- [ ] Deploy to Railway Linux production (`https://web-production-1c1cc.up.railway.app`).
- [ ] Final verification & release sign-off.
