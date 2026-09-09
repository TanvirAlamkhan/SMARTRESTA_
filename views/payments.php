<!-- VIEW: BILLING & PAYMENT SETTLEMENT HISTORY -->
<section id="payments" class="role-view" style="display:none;">
  <div class="card">
    <div class="card-header" style="flex-wrap:wrap; gap:12px;">
      <div>
        <h3>Billing & Settlement History</h3>
        <p class="text-sm">Real-time payment transactions, receipts, invoices, split payment allocations, and refund processing</p>
      </div>
      <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
        <button class="btn btn-secondary btn-sm" onclick="SmartBilling.loadPaymentHistory()">Refresh Payments</button>
        <button class="btn btn-primary btn-sm" onclick="SmartBilling.openOrderSelectionModal()">💳 + Process New Settlement</button>
      </div>
    </div>

    <!-- Filters Bar -->
    <div style="padding: 12px var(--space-4); display:flex; gap:12px; align-items:center; flex-wrap:wrap; border-bottom: 1px solid var(--border);">
      <input type="text" id="payments-search-input" class="form-control" style="max-width:240px;" placeholder="Search Order # or Payment Ref..." oninput="SmartBilling.filterPayments()">
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
            <th>RECEIVED BY</th>
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
