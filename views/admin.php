<!-- VIEW 1: ADMIN & SYSTEM MANAGEMENT DASHBOARD -->
<section id="admin" class="role-view">

  <!-- Executive Control Toolbar & Real-Time Date Filter -->
  <div class="card" style="margin-bottom:20px; padding:16px 20px;">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
      <div>
        <h2 style="font-size:1.25rem; font-weight:700; margin:0;">System Administration & Executive Intelligence</h2>
        <p class="text-sm" style="margin:2px 0 0 0; color:var(--text-muted);">Real-time database metrics, operational alerts, waiter commissions, and multi-counter order routing</p>
      </div>

      <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
        <div style="display:flex; align-items:center; gap:6px;">
          <label style="font-size:0.875rem; font-weight:600;">Date Range:</label>
          <select id="report-date-preset" class="form-control" style="width:auto; padding:6px 12px;" onchange="SmartReports.currentPreset=this.value; SmartReports.loadOverviewDashboard();">
            <option value="today" selected>Today</option>
            <option value="yesterday">Yesterday</option>
            <option value="this_week">This Week</option>
            <option value="this_month">This Month</option>
            <option value="custom">Custom Range</option>
          </select>
        </div>

        <div id="report-custom-date-box" style="display:none; gap:6px; align-items:center;">
          <input type="date" id="report-date-from" class="form-control" style="width:auto; padding:4px 8px;">
          <span>to</span>
          <input type="date" id="report-date-to" class="form-control" style="width:auto; padding:4px 8px;">
          <button class="btn btn-secondary btn-sm" id="btn-apply-custom-date">Apply</button>
        </div>

        <button class="btn btn-primary btn-sm" onclick="SmartReports.loadOverviewDashboard(); loadWaiterMatrix(); loadActiveOrders();">
          🔄 Refresh Metrics
        </button>
      </div>
    </div>
  </div>

  <!-- Operational Real-Time Alert Banners -->
  <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:16px; margin-bottom:24px;">
    <div class="card" style="padding:14px 18px; border-left: 4px solid var(--danger, #ef4444);">
      <span style="font-size:0.8rem; font-weight:600; text-transform:uppercase; color:var(--text-muted);">Low Stock Alerts</span>
      <div style="font-size:1.4rem; font-weight:700; color:var(--danger, #ef4444);" id="alert-low-stock">0 Items</div>
      <span class="text-sm">Ingredients reorder level</span>
    </div>

    <div class="card" style="padding:14px 18px; border-left: 4px solid var(--warning, #f59e0b);">
      <span style="font-size:0.8rem; font-weight:600; text-transform:uppercase; color:var(--text-muted);">Unpaid Orders</span>
      <div style="font-size:1.4rem; font-weight:700; color:var(--warning, #f59e0b);" id="alert-unpaid-orders">0 Orders</div>
      <span class="text-sm">Outstanding balances</span>
    </div>

    <div class="card" style="padding:14px 18px; border-left: 4px solid var(--info, #3b82f6);">
      <span style="font-size:0.8rem; font-weight:600; text-transform:uppercase; color:var(--text-muted);">Delayed KDS Tickets</span>
      <div style="font-size:1.4rem; font-weight:700; color:var(--info, #3b82f6);" id="alert-delayed-kds">0 Tickets</div>
      <span class="text-sm">Over 20 mins in prep</span>
    </div>

    <div class="card" style="padding:14px 18px; border-left: 4px solid var(--success, #10b981);">
      <span style="font-size:0.8rem; font-weight:600; text-transform:uppercase; color:var(--text-muted);">Pending Commissions</span>
      <div style="font-size:1.4rem; font-weight:700; color:var(--success, #10b981);" id="alert-pending-comm">0 Pending</div>
      <span class="text-sm">Waiter payout review</span>
    </div>
  </div>

  <!-- Primary Executive KPI Cards Grid (Real DB Metrics) -->
  <div class="grid-kpi" id="kpi-container">
    <div class="kpi-card">
      <span class="kpi-label">Today Net Sales</span>
      <div class="kpi-value" id="kpi-net-sales">৳0.00</div>
      <span class="kpi-trend positive">Live Sales Total</span>
    </div>

    <div class="kpi-card">
      <span class="kpi-label">Gross Revenue</span>
      <div class="kpi-value" id="kpi-gross-sales">৳0.00</div>
      <span class="kpi-trend positive">Before Discounts</span>
    </div>

    <div class="kpi-card">
      <span class="kpi-label">Orders Processed</span>
      <div class="kpi-value" id="kpi-total-orders">0</div>
      <span class="kpi-trend positive">Completed + Active</span>
    </div>

    <div class="kpi-card">
      <span class="kpi-label">Avg Order Value (AOV)</span>
      <div class="kpi-value" id="kpi-aov">৳0.00</div>
      <span class="kpi-trend positive">Per Ticket Average</span>
    </div>

    <div class="kpi-card">
      <span class="kpi-label">Paid Collections</span>
      <div class="kpi-value" id="kpi-paid-amount">৳0.00</div>
      <span class="kpi-trend positive">Settled Cash / bKash</span>
    </div>

    <div class="kpi-card">
      <span class="kpi-label">Pending Collection</span>
      <div class="kpi-value" id="kpi-pending-amount">৳0.00</div>
      <span class="kpi-trend negative">Unsettled Balance</span>
    </div>

    <div class="kpi-card">
      <span class="kpi-label">Active Tables</span>
      <div class="kpi-value" id="kpi-tables">0 / 0</div>
      <span class="kpi-trend positive">Floor Occupancy</span>
    </div>

    <div class="kpi-card">
      <span class="kpi-label">Waiter Commission Owed</span>
      <div class="kpi-value" id="kpi-commission">৳0.00</div>
      <span class="kpi-trend negative">Base Rate Calculation</span>
    </div>
  </div>

  <!-- Table 1: Waiter Performance & Commission Matrix -->
  <div class="card" style="margin-top: 24px;">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
      <div>
        <h3>Waiter Performance & Commission Matrix</h3>
        <p class="text-sm">Real-time order throughput, sales volume, and commission breakdown from MySQL</p>
      </div>
      <button class="btn btn-secondary btn-sm" onclick="loadWaiterMatrix()">🔄 Refresh Matrix</button>
    </div>

    <div class="table-container">
      <table class="data-table">
        <thead>
          <tr>
            <th>WAITER</th>
            <th>ORDERS</th>
            <th>TABLES SERVED</th>
            <th>TOTAL SALES</th>
            <th>PAID</th>
            <th>PENDING</th>
            <th>COMMISSION</th>
            <th>STATUS</th>
          </tr>
        </thead>
        <tbody id="waiter-matrix-tbody">
          <tr><td colspan="8" style="text-align:center; padding: 40px; color: var(--text-muted);">Loading waiter performance data...</td></tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Table 2: Live Orders & Multi-Counter Station Routing -->
  <div class="card" style="margin-top: 24px;">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
      <div>
        <h3>Live Orders & Multi-Counter Station Routing</h3>
        <p class="text-sm">Real-time order status, payment state, station dispatches, and drill-down inspection</p>
      </div>
      <button class="btn btn-secondary btn-sm" onclick="loadActiveOrders()">🔄 Refresh Orders</button>
    </div>

    <div class="table-container">
      <table class="data-table">
        <thead>
          <tr>
            <th>ORDER ID</th>
            <th>TABLE</th>
            <th>WAITER</th>
            <th>ITEMS</th>
            <th>TOTAL</th>
            <th>PAYMENT</th>
            <th>STATUS</th>
            <th>ACTION</th>
          </tr>
        </thead>
        <tbody id="orders-tbody">
          <tr><td colspan="8" style="text-align:center; padding: 40px; color: var(--text-muted);">Loading active orders from database...</td></tr>
        </tbody>
      </table>
    </div>
  </div>

</section>
