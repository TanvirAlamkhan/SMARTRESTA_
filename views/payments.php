<!-- VIEW: RECEPTION / CASHIER BILLING & PAYMENT SETTLEMENT PORTAL -->
<section id="payments" class="role-view" style="display:none;">

  <!-- Reception Cashier KPI Summary Grid (Real DB Metrics) -->
  <div class="grid-kpi" id="reception-kpi-container" style="margin-bottom:20px;">
    <div class="kpi-card" style="border-left: 4px solid var(--warning, #f59e0b);">
      <span class="kpi-label">Open Bills (Unpaid)</span>
      <div class="kpi-value" id="kpi-rec-open-count" style="color:var(--warning, #f59e0b);">0</div>
      <span class="kpi-trend negative" id="kpi-rec-open-balance">৳0.00 Outstanding</span>
    </div>

    <div class="kpi-card" style="border-left: 4px solid var(--success, #10b981);">
      <span class="kpi-label">Today Net Collections</span>
      <div class="kpi-value" id="kpi-rec-today-net" style="color:var(--success, #10b981);">৳0.00</div>
      <span class="kpi-trend positive">Settled Payments</span>
    </div>

    <div class="kpi-card" style="border-left: 4px solid var(--info, #3b82f6);">
      <span class="kpi-label">Cash Register</span>
      <div class="kpi-value" id="kpi-rec-cash-total">৳0.00</div>
      <span class="kpi-trend positive">Physical Cash In Drawer</span>
    </div>

    <div class="kpi-card" style="border-left: 4px solid #8b5cf6;">
      <span class="kpi-label">Digital MFS & Card</span>
      <div class="kpi-value" id="kpi-rec-digital-total">৳0.00</div>
      <span class="kpi-trend positive">bKash / Nagad / Card</span>
    </div>

    <div class="kpi-card" style="border-left: 4px solid var(--danger, #ef4444);">
      <span class="kpi-label">Refunds Issued</span>
      <div class="kpi-value" id="kpi-rec-refunds-total" style="color:var(--danger, #ef4444);">৳0.00</div>
      <span class="kpi-trend negative" id="kpi-rec-refunds-count">0 Reversals</span>
    </div>
  </div>

  <div class="card">
    <div class="card-header" style="flex-wrap:wrap; gap:12px;">
      <div>
        <h3>Reception Cashier — Billing, Settlement & Receipts</h3>
        <p class="text-sm">Real-time order settlements, bKash/Nagad digital QR payments, thermal receipts, and audit-logged refunds</p>
      </div>
      <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
        <button class="btn btn-secondary btn-sm" onclick="SmartBilling.loadPaymentHistory()">🔄 Refresh Payments</button>
        <button class="btn btn-primary btn-sm" onclick="SmartBilling.openOrderSelectionModal()">💳 + Process New Settlement</button>
      </div>
    </div>

    <!-- Operational Filters Bar -->
    <div style="padding: 12px var(--space-4); display:flex; gap:12px; align-items:center; flex-wrap:wrap; border-bottom: 1px solid var(--border);">
      <input type="text" id="payments-search-input" class="form-control" style="max-width:260px;" placeholder="Search Order #, TRX ID, Cashier..." oninput="SmartBilling.filterPayments()">
      <select id="payments-method-filter" class="form-control" style="max-width:180px;" onchange="SmartBilling.filterPayments()">
        <option value="">All Payment Methods</option>
        <option value="Cash">Cash</option>
        <option value="bKash">bKash</option>
        <option value="Nagad">Nagad</option>
        <option value="Card">Card / POS Terminal</option>
      </select>
      <select id="payments-status-filter" class="form-control" style="max-width:160px;" onchange="SmartBilling.filterPayments()">
        <option value="">All Statuses</option>
        <option value="COMPLETED">COMPLETED</option>
        <option value="REFUNDED">REFUNDED</option>
        <option value="PARTIALLY_REFUNDED">PARTIALLY_REFUNDED</option>
        <option value="VOIDED">VOIDED</option>
      </select>
    </div>

    <div class="table-container">
      <table class="data-table">
        <thead>
          <tr>
            <th>PAYMENT ID</th>
            <th>ORDER / SESSION</th>
            <th>ORDER TYPE / TABLE</th>
            <th>METHOD</th>
            <th>AMOUNT</th>
            <th>TRANSACTION REF</th>
            <th>CASHIER / USER</th>
            <th>STATUS</th>
            <th>ACTIONS</th>
          </tr>
        </thead>
        <tbody id="payments-table-tbody">
          <tr><td colspan="9" style="text-align:center; padding: 40px; color: var(--text-muted);">Loading payment transaction history...</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</section>

