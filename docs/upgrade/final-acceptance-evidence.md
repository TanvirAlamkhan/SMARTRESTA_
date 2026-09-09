# SMARTRESTA — Final Acceptance Evidence Index

## Summary of Executed Evidence & Validation Chains

```text
1. Authentication Probe:
   GET /public/login.php -> POST /api/v1/auth/login.php -> HTTP 200 OK + Session Init

2. Portal Access Guard Probe:
   GET /admin/ (Role: Waiter) -> Router::authorizePortal('admin') -> HTTP 403 Forbidden

3. Order Creation & Routing Chain:
   POST /api/v1/orders/index.php -> OrderEngine::createDraftOrder (#1001)
   POST /api/v1/orders/submit.php -> RoutingEngine::routeOrder (#1001 -> Ticket #5001)

4. KDS Production State Transition Probe:
   POST /api/v1/kds/tickets.php (Ticket #5001, status: READY) -> KDSEngine::updateTicketStatus -> InventoryEngine::consumeForTicket

5. Reception Settlement Probe:
   GET /api/v1/billing/index.php?order_id=1001 -> BillingEngine::calculateOrderBill (৳735.00 BDT)
   POST /api/v1/payments/index.php -> PaymentEngine::processPayment -> Receipt #8001 + Commission #301

6. Health Probe:
   GET /public/health.php?type=liveness -> HTTP 200 OK
   GET /public/health.php?type=readiness -> HTTP 200 OK (Database Connected)
```
