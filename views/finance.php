<!-- VIEW: FINANCE, SHIFTS & DAY CLOSING (Prompt 13) -->
<section id="finance-view" class="role-view" style="display:none;">

  <!-- Active Shift Banner -->
  <div id="active-shift-banner" style="margin-bottom:20px;"></div>

  <!-- Finance Navigation Tabs -->
  <div class="card" style="margin-bottom:20px; padding:12px 20px;">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
      <div style="display:flex; gap:6px; flex-wrap:wrap;">
        <button class="btn btn-sm btn-primary finance-tab-btn active" data-tab="overview">📊 Finance Overview</button>
        <button class="btn btn-sm btn-secondary finance-tab-btn" data-tab="expenses">💸 Operating Expenses</button>
        <button class="btn btn-sm btn-secondary finance-tab-btn" data-tab="shifts">🔑 Shifts & Cash Drawers</button>
        <button class="btn btn-sm btn-secondary finance-tab-btn" data-tab="dayclosing">📅 Day Closing Audit</button>
      </div>
      <div>
        <span class="text-sm text-muted">Day Status: </span>
        <span class="badge badge-success" id="fin-day-status-badge">OPEN</span>
      </div>
    </div>
  </div>

  <!-- TAB: Overview -->
  <div id="tab-fin-overview-content" class="finance-tab-pane">
    <div class="grid-kpi" style="margin-bottom:20px;">
      <div class="kpi-card"><span class="kpi-label">Gross Sales</span><div class="kpi-value" id="fin-kpi-gross">৳0.00</div></div>
      <div class="kpi-card"><span class="kpi-label">Net Revenue</span><div class="kpi-value" id="fin-kpi-net">৳0.00</div></div>
      <div class="kpi-card"><span class="kpi-label">Cash Collected</span><div class="kpi-value" id="fin-kpi-cash" style="color:var(--success)">৳0.00</div></div>
      <div class="kpi-card"><span class="kpi-label">Non-Cash (MFS/Card)</span><div class="kpi-value" id="fin-kpi-noncash" style="color:var(--info)">৳0.00</div></div>
      <div class="kpi-card"><span class="kpi-label">Operating Expenses</span><div class="kpi-value" id="fin-kpi-exp" style="color:var(--danger)">৳0.00</div></div>
      <div class="kpi-card"><span class="kpi-label">Active Shifts</span><div class="kpi-value" id="fin-kpi-shifts">0 Open</div></div>
    </div>
  </div>

  <!-- TAB: Expenses -->
  <div id="tab-fin-expenses-content" class="finance-tab-pane" style="display:none;">
    <div class="card" style="margin-bottom:16px; padding:12px 20px; display:flex; justify-content:space-between; align-items:center;">
      <h3>💸 Operating Expenses Ledger</h3>
      <button class="btn btn-sm btn-primary" onclick="SmartFinance.openCreateExpenseModal()">+ Record Expense</button>
    </div>
    <div class="card">
      <div class="table-container"><table class="data-table">
        <thead><tr><th>ID</th><th>Title / Reference</th><th>Category</th><th>Amount</th><th>Method</th><th>Status</th><th>Recorded By</th><th>Date</th></tr></thead>
        <tbody id="table-expenses-body"><tr><td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted)">Loading...</td></tr></tbody>
      </table></div>
    </div>
  </div>

  <!-- TAB: Shifts -->
  <div id="tab-fin-shifts-content" class="finance-tab-pane" style="display:none;">
    <div class="card">
      <div class="card-header"><h3>🔑 Cashier Shift History</h3></div>
      <div class="table-container"><table class="data-table">
        <thead><tr><th>Shift #</th><th>Cashier</th><th>Drawer</th><th>Opened</th><th>Opening Cash</th><th>Expected Cash</th><th>Actual Cash</th><th>Difference</th><th>Status</th></tr></thead>
        <tbody id="table-shifts-body"><tr><td colspan="9" style="text-align:center;padding:40px;color:var(--text-muted)">Shift history logged automatically.</td></tr></tbody>
      </table></div>
    </div>
  </div>

  <!-- TAB: Day Closing -->
  <div id="tab-fin-dayclosing-content" class="finance-tab-pane" style="display:none;">
    <div id="dayclosing-review-box">
      <!-- Populated dynamically by SmartFinance.loadDayClosingReview -->
    </div>
  </div>

</section>
