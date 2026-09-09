# SMARTRESTA — Cross-Portal Data Visibility Matrix

| Business Entity | Admin (`/admin/`) | Manager (`/manager/`) | Reception (`/reception/`) | Waiter (`/waiter/`) | Kitchen (`/kitchen/`) |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Users & Roles** | CREATE / READ / UPDATE | READ | READ | NOT AUTHORIZED | NOT AUTHORIZED |
| **Dining Tables** | CREATE / READ / UPDATE | CREATE / READ / UPDATE | READ / UPDATE | READ / UPDATE | NOT AUTHORIZED |
| **Dining Sessions** | READ | READ | CREATE / READ / UPDATE | CREATE / READ / UPDATE | NOT AUTHORIZED |
| **Menu & Products** | CREATE / READ / UPDATE | CREATE / READ / UPDATE | READ | READ | READ |
| **Orders** | READ / CANCEL | READ / CANCEL | READ / UPDATE | CREATE / READ / UPDATE | READ |
| **Order Line Items** | READ | READ | READ / UPDATE | CREATE / READ / UPDATE | READ |
| **Station Routes** | READ | READ | READ | READ | READ |
| **Kitchen Tickets** | READ | READ | READ | READ | READ / UPDATE (STATUS) |
| **Payments** | READ | READ | CREATE / READ | NOT AUTHORIZED | NOT AUTHORIZED |
| **Receipts** | READ | READ | CREATE / READ | NOT AUTHORIZED | NOT AUTHORIZED |
| **Refunds** | CREATE / READ / APPROVE | CREATE / READ / APPROVE | CREATE / READ | NOT AUTHORIZED | NOT AUTHORIZED |
| **Inventory Stock** | CREATE / READ / UPDATE | CREATE / READ / UPDATE | NOT AUTHORIZED | NOT AUTHORIZED | READ |
| **Commissions** | READ / APPROVE | READ / APPROVE | NOT AUTHORIZED | READ (OWN ONLY) | NOT AUTHORIZED |
| **Expenses** | CREATE / READ / UPDATE | CREATE / READ / UPDATE | NOT AUTHORIZED | NOT AUTHORIZED | NOT AUTHORIZED |
| **Finance Reports** | READ | READ | READ (DAILY SHIFT) | NOT AUTHORIZED | NOT AUTHORIZED |
| **Audit Logs** | READ | READ | NOT AUTHORIZED | NOT AUTHORIZED | NOT AUTHORIZED |
