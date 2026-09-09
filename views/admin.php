<!-- VIEW 1: ADMIN / MANAGER DASHBOARD -->
<section id="admin" class="role-view">
  <div class="grid-kpi" id="kpi-container">
    <div class="kpi-card">
      <span class="kpi-label">Today Net Sales</span>
      <div class="kpi-value" id="kpi-sales">৳0.00</div>
      <span class="kpi-trend positive">Live Database Data</span>
    </div>

    <div class="kpi-card">
      <span class="kpi-label">Active Tables</span>
      <div class="kpi-value" id="kpi-tables">0 / 0</div>
      <span class="kpi-trend positive">Floor Status</span>
    </div>

    <div class="kpi-card">
      <span class="kpi-label">Orders Processed</span>
      <div class="kpi-value" id="kpi-orders">0</div>
      <span class="kpi-trend positive">Shift Total</span>
    </div>

    <div class="kpi-card">
      <span class="kpi-label">Waiter Commission Owed</span>
      <div class="kpi-value" id="kpi-commission">৳0.00</div>
      <span class="kpi-trend negative">5.0% Base Rate</span>
    </div>
  </div>

  <div class="card" style="margin-top: 24px;">
    <div class="card-header">
      <div>
        <h3>Waiter Performance & Commission Matrix</h3>
        <p class="text-sm">Real-time order throughput, sales volume, and commission breakdown from MySQL</p>
      </div>
      <button class="btn btn-secondary btn-sm" onclick="loadWaiterMatrix()">Refresh Data</button>
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
            <th>COMMISSION (5%)</th>
            <th>STATUS</th>
          </tr>
        </thead>
        <tbody id="waiter-matrix-tbody">
          <tr><td colspan="8" style="text-align:center; padding: 40px; color: var(--text-muted);">Loading waiter performance data...</td></tr>
        </tbody>
      </table>
    </div>
  </div>

  <div class="card" style="margin-top: 24px;">
    <div class="card-header">
      <h3>Live Orders & Multi-Counter Station Routing</h3>
      <button class="btn btn-secondary btn-sm" onclick="loadActiveOrders()">Refresh Orders</button>
    </div>

    <div class="table-container">
      <table class="data-table">
        <thead>
          <tr>
            <th>ORDER ID</th>
            <th>TABLE</th>
            <th>WAITER</th>
            <th>ROUTED STATIONS</th>
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
