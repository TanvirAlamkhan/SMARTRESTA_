# SMARTRESTA — Kitchen/KDS Security & RBAC Test Matrix

## Security Controls Verified

| Security Test | Vulnerability Category | Protection Mechanism | Verification Status |
| :--- | :--- | :--- | :--- |
| **Direct Portal Access** | Unauthenticated Access | `Router::authorizePortal('kitchen')` checks session and forces redirect to login. | PASSED |
| **Role Authorization** | Privilege Escalation | Only `kitchen`, `chef`, `cook`, `kds`, `admin`, `manager` roles can access `/kitchen/`. | PASSED |
| **Cross-Branch Isolation** | Multi-Tenant IDOR | `KDSEngine` filters all tickets and stations by `$_SESSION['branch_id']`. Foreign branch tickets return `403`/`404`. | PASSED |
| **Station Access Isolation** | Station IDOR | Server validates user station assignment; users cannot transition tickets assigned to unauthorized stations. | PASSED |
| **State Machine Enforcement** | Invalid Status Transition | `KDSEngine` rejects illegal status jumps (e.g. `NEW` -> `SERVED` without `PREPARING` and `READY`). | PASSED |
| **Concurrency Protection** | Race Conditions | `SELECT ... FOR UPDATE` row locking prevents duplicate status transitions when multiple cooks click simultaneously. | PASSED |
| **XSS Prevention** | Output Escaping | All order notes, item names, modifiers, and table numbers are sanitized via `htmlspecialchars()` before rendering. | PASSED |
| **SQL Injection** | Database Exploitation | All queries in `KDSEngine` and KDS APIs use PDO prepared statements with parameter binding. | PASSED |
| **CSRF Defense** | Cross-Site Request Forgery | All state-changing POST requests require valid CSRF tokens verified by `CSRF::verifyToken()`. | PASSED |
| **Audit Logging** | Non-Repudiation | All ticket status transitions, recalls, re-fires, cancellations, and station pause/resume actions log to `AuditLogger`. | PASSED |
