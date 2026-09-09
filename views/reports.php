<!-- VIEW: REPORTS & ANALYTICS DASHBOARD (Prompt 12) -->
<section id="reports" class="role-view" style="display:none;">

  <!-- Global Date/Time Range Filter Toolbar -->
  <div class="card" style="margin-bottom:20px; padding:16px 20px;">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
      <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
        <label class="form-label" style="margin:0; font-weight:600;">📅 Period:</label>
        <select id="report-date-preset" class="form-control" style="max-width:180px;">
          <option value="today" selected>Today</option>
          <option value="yesterday">Yesterday</option>
          <option value="this_week">This Week</option>
          <option value="last_week">Last Week</option>
          <option value="this_month">This Month</option>
          <option value="last_month">Last Month</option>
          <option value="this_year">This Year</option>
          <option value="custom">Custom Range</option>
        </select>
        <div id="report-custom-date-box" style="display:none; gap:8px; align-items:center;">
          <input type="date" id="report-date-from" class="form-control" style="max-width:160px;">
          <span>to</span>
          <input type="date" id="report-date-to" class="form-control" style="max-width:160px;">
          <button id="btn-apply-custom-date" class="btn btn-sm btn-primary">Apply</button>
        </div>
      </div>
      <div style="display:flex; gap:8px;">
        <button class="btn btn-sm btn-secondary" onclick="SmartReports.loadCurrentTab()">🔄 Refresh</button>
        <button class="btn btn-sm btn-success" onclick="SmartReports.exportReport(SmartReports.currentTab)">📥 Export CSV</button>
      </div>
    </div>
  </div>

  <!-- Report Tab Navigation -->
  <div class="card" style="margin-bottom:20px; padding:12px 20px;">
    <div style="display:flex; gap:6px; flex-wrap:wrap;">
      <button class="btn btn-sm btn-primary report-tab-btn active" data-tab="overview">📊 Overview</button>
      <button class="btn btn-sm btn-secondary report-tab-btn" data-tab="sales">💰 Sales</button>
      <button class="btn btn-sm btn-secondary report-tab-btn" data-tab="orders">📋 Orders</button>
      <button class="btn btn-sm btn-secondary report-tab-btn" data-tab="waiters">👤 Waiters</button>
      <button class="btn btn-sm btn-secondary report-tab-btn" data-tab="tables">🪑 Tables</button>
      <button class="btn btn-sm btn-secondary report-tab-btn" data-tab="products">🍔 Products</button>
      <button class="btn btn-sm btn-secondary report-tab-btn" data-tab="payments">💳 Payments</button>
      <button class="btn btn-sm btn-secondary report-tab-btn" data-tab="kds">🍳 KDS Stations</button>
      <button class="btn btn-sm btn-secondary report-tab-btn" data-tab="inventory">📦 Inventory</button>
    </div>
  </div>

  <!-- TAB: Overview Dashboard -->
  <div id="tab-overview-content" class="report-tab-pane">
    <!-- KPI Cards -->
    <div class="grid-kpi" style="margin-bottom:20px;">
      <div class="kpi-card"><span class="kpi-label">Gross Sales</span><div class="kpi-value" id="kpi-gross-sales">৳0.00</div></div>
      <div class="kpi-card"><span class="kpi-label">Net Sales</span><div class="kpi-value" id="kpi-net-sales">৳0.00</div></div>
      <div class="kpi-card"><span class="kpi-label">Total Orders</span><div class="kpi-value" id="kpi-total-orders">0</div></div>
      <div class="kpi-card"><span class="kpi-label">Avg Order Value</span><div class="kpi-value" id="kpi-aov">৳0.00</div></div>
      <div class="kpi-card"><span class="kpi-label">Paid Amount</span><div class="kpi-value" id="kpi-paid-amount">৳0.00</div></div>
      <div class="kpi-card"><span class="kpi-label">Pending Amount</span><div class="kpi-value" id="kpi-pending-amount">৳0.00</div></div>
    </div>
    <!-- Operational Alerts -->
    <div class="card">
      <div class="card-header"><h3>⚠️ Operational Alerts</h3></div>
      <div style="padding:16px; display:flex; gap:16px; flex-wrap:wrap;">
        <div class="kpi-card" style="border-left:4px solid var(--danger); cursor:pointer;" onclick="document.querySelector('[data-tab=inventory]').click()">
          <span class="kpi-label">Low Stock</span><div class="kpi-value" id="alert-low-stock" style="color:var(--danger)">0 Items</div>
        </div>
        <div class="kpi-card" style="border-left:4px solid var(--warning); cursor:pointer;" onclick="document.querySelector('[data-tab=orders]').click()">
          <span class="kpi-label">Unpaid Orders</span><div class="kpi-value" id="alert-unpaid-orders" style="color:var(--warning)">0 Orders</div>
        </div>
        <div class="kpi-card" style="border-left:4px solid var(--danger); cursor:pointer;" onclick="document.querySelector('[data-tab=kds]').click()">
          <span class="kpi-label">Delayed KDS</span><div class="kpi-value" id="alert-delayed-kds" style="color:var(--danger)">0 Tickets</div>
        </div>
        <div class="kpi-card" style="border-left:4px solid var(--info); cursor:pointer;">
          <span class="kpi-label">Pending Commissions</span><div class="kpi-value" id="alert-pending-comm" style="color:var(--info)">0 Pending</div>
        </div>
      </div>
    </div>
  </div>

  <!-- TAB: Sales Analytics -->
  <div id="tab-sales-content" class="report-tab-pane" style="display:none;">
    <div class="card">
      <div class="card-header"><h3>💰 Sales Analytics Breakdown</h3></div>
      <div class="table-container"><table class="data-table">
        <thead><tr><th>Period</th><th>Orders</th><th>Gross Sales</th><th>Discounts</th><th>Tax</th><th>Net Sales</th></tr></thead>
        <tbody id="table-sales-body"><tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted)">Select a date range and click Refresh.</td></tr></tbody>
      </table></div>
    </div>
  </div>

  <!-- TAB: Orders Report -->
  <div id="tab-orders-content" class="report-tab-pane" style="display:none;">
    <div class="card" style="margin-bottom:16px; padding:12px 20px;">
      <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
        <select id="report-order-status-filter" class="form-control" style="max-width:180px;">
          <option value="">All Statuses</option>
          <option value="DRAFT">Draft</option>
          <option value="SUBMITTED">Submitted</option>
          <option value="PREPARING">Preparing</option>
          <option value="READY">Ready</option>
          <option value="SERVED">Served</option>
          <option value="COMPLETED">Completed</option>
          <option value="CANCELLED">Cancelled</option>
          <option value="REFUNDED">Refunded</option>
        </select>
        <input type="text" id="report-order-search" class="form-control" style="max-width:220px;" placeholder="Search order #, waiter, table...">
      </div>
    </div>
    <div class="card">
      <div class="card-header"><h3>📋 Orders Performance Report</h3></div>
      <div class="table-container"><table class="data-table">
        <thead><tr><th>Order #</th><th>Date</th><th>Type</th><th>Table</th><th>Waiter</th><th>Total</th><th>Payment</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody id="table-orders-body"><tr><td colspan="9" style="text-align:center;padding:40px;color:var(--text-muted)">Loading...</td></tr></tbody>
      </table></div>
      <div id="orders-pagination-controls" style="padding:12px 20px;"></div>
    </div>
  </div>

  <!-- TAB: Waiter Performance -->
  <div id="tab-waiters-content" class="report-tab-pane" style="display:none;">
    <div class="card">
      <div class="card-header"><h3>👤 Waiter Performance & Commission Report</h3></div>
      <div class="table-container"><table class="data-table">
        <thead><tr><th>Waiter</th><th>Orders</th><th>Tables</th><th>Total Sales</th><th>AOV</th><th>Pending Comm.</th><th>Approved Comm.</th><th>Paid Comm.</th></tr></thead>
        <tbody id="table-waiters-body"><tr><td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted)">Loading...</td></tr></tbody>
      </table></div>
    </div>
  </div>

  <!-- TAB: Table Performance -->
  <div id="tab-tables-content" class="report-tab-pane" style="display:none;">
    <div class="card">
      <div class="card-header"><h3>🪑 Table & Dining Session Report</h3></div>
      <div class="table-container"><table class="data-table">
        <thead><tr><th>Table (Floor)</th><th>Capacity</th><th>Sessions</th><th>Guests</th><th>Total Sales</th><th>Avg Spend/Session</th><th>Avg Duration</th></tr></thead>
        <tbody id="table-tables-body"><tr><td colspan="7" style="text-align:center;padding:40px;color:var(--text-muted)">Loading...</td></tr></tbody>
      </table></div>
    </div>
  </div>

  <!-- TAB: Product Performance -->
  <div id="tab-products-content" class="report-tab-pane" style="display:none;">
    <div class="card">
      <div class="card-header"><h3>🍔 Product Sales & Recipe Margin Analysis</h3></div>
      <div class="table-container"><table class="data-table">
        <thead><tr><th>Product</th><th>Category</th><th>Units Sold</th><th>Gross Sales</th><th>Recipe Cost/Unit</th><th>Total Cost</th><th>Gross Profit</th><th>Margin %</th></tr></thead>
        <tbody id="table-products-body"><tr><td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted)">Loading...</td></tr></tbody>
      </table></div>
    </div>
  </div>

  <!-- TAB: Payment Reconciliation -->
  <div id="tab-payments-content" class="report-tab-pane" style="display:none;">
    <div class="card" style="margin-bottom:20px;">
      <div class="card-header"><h3>💳 Payment Method Reconciliation</h3></div>
      <div class="table-container"><table class="data-table">
        <thead><tr><th>Payment Method</th><th>Transactions</th><th>Collected</th><th>Refunded</th><th>Net Collected</th></tr></thead>
        <tbody id="table-payment-methods-body"><tr><td colspan="5" style="text-align:center;padding:40px;color:var(--text-muted)">Loading...</td></tr></tbody>
      </table></div>
    </div>
    <div class="card">
      <div class="card-header"><h3>⚠️ Outstanding Unpaid Orders</h3></div>
      <div class="table-container"><table class="data-table">
        <thead><tr><th>Order #</th><th>Table</th><th>Total</th><th>Paid</th><th>Outstanding</th><th>Status</th></tr></thead>
        <tbody id="table-unpaid-orders-body"><tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted)">Loading...</td></tr></tbody>
      </table></div>
    </div>
  </div>

  <!-- TAB: KDS Station Report -->
  <div id="tab-kds-content" class="report-tab-pane" style="display:none;">
    <div class="card">
      <div class="card-header"><h3>🍳 KDS Station Production & SLA Report</h3></div>
      <div class="table-container"><table class="data-table">
        <thead><tr><th>Station</th><th>Total Tickets</th><th>Completed</th><th>Cancelled</th><th>Avg Prep Time</th><th>SLA Status</th></tr></thead>
        <tbody id="table-kds-body"><tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted)">Loading...</td></tr></tbody>
      </table></div>
    </div>
  </div>

  <!-- TAB: Inventory Report -->
  <div id="tab-inventory-content" class="report-tab-pane" style="display:none;">
    <div style="display:flex; gap:16px; margin-bottom:20px; flex-wrap:wrap;">
      <div class="kpi-card"><span class="kpi-label">Total Ingredients</span><div class="kpi-value" id="report-inv-val-count">0</div></div>
      <div class="kpi-card"><span class="kpi-label">Total Stock Value</span><div class="kpi-value" id="report-inv-val-total">৳0.00</div></div>
    </div>
    <div class="card" style="margin-bottom:20px;">
      <div class="card-header"><h3>🗑️ Wastage Breakdown by Reason</h3></div>
      <div class="table-container"><table class="data-table">
        <thead><tr><th>Reason</th><th>Records</th><th>Total Cost</th></tr></thead>
        <tbody id="table-inv-wastage-body"><tr><td colspan="3" style="text-align:center;padding:40px;color:var(--text-muted)">Loading...</td></tr></tbody>
      </table></div>
    </div>
    <div class="card">
      <div class="card-header"><h3>📦 Ingredient Consumption Summary</h3></div>
      <div class="table-container"><table class="data-table">
        <thead><tr><th>Ingredient</th><th>Consumed Qty</th><th>Estimated Cost</th></tr></thead>
        <tbody id="table-inv-cons-body"><tr><td colspan="3" style="text-align:center;padding:40px;color:var(--text-muted)">Loading...</td></tr></tbody>
      </table></div>
    </div>
  </div>

</section>
