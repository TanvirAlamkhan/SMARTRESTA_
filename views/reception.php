<!-- VIEW: DEDICATED RECEPTION & CASHIER FRONT DESK PORTAL -->
<section id="reception" class="role-view" style="display:none;">

  <!-- Reception KPI Summary Grid -->
  <div class="grid-kpi" id="reception-kpi-bar" style="margin-bottom:24px;">
    <div class="kpi-card" style="border-left: 4px solid var(--warning, #f59e0b);">
      <span class="kpi-label">Open Unpaid Bills</span>
      <div class="kpi-value" id="rec-kpi-open-count" style="color:var(--warning, #f59e0b);">0</div>
      <span class="kpi-trend negative" id="rec-kpi-open-bal">৳0.00 Outstanding</span>
    </div>

    <div class="kpi-card" style="border-left: 4px solid var(--success, #10b981);">
      <span class="kpi-label">Today Net Collections</span>
      <div class="kpi-value" id="rec-kpi-today-net" style="color:var(--success, #10b981);">৳0.00</div>
      <span class="kpi-trend positive">Settled Payments</span>
    </div>

    <div class="kpi-card" style="border-left: 4px solid var(--info, #3b82f6);">
      <span class="kpi-label">Cash Register Balance</span>
      <div class="kpi-value" id="rec-kpi-cash-total">৳0.00</div>
      <span class="kpi-trend positive">Physical Cash In Drawer</span>
    </div>

    <div class="kpi-card" style="border-left: 4px solid #8b5cf6;">
      <span class="kpi-label">Today's Reservations</span>
      <div class="kpi-value" id="rec-kpi-reservations-count" style="color:#8b5cf6;">0</div>
      <span class="kpi-trend positive">Arriving Guests</span>
    </div>

    <div class="kpi-card" style="border-left: 4px solid var(--accent, #6366f1);">
      <span class="kpi-label">Floor Table Occupancy</span>
      <div class="kpi-value" id="rec-kpi-occupancy" style="font-size:1.3rem;">0 / 0 Occupied</div>
      <span class="kpi-trend positive">Real-Time Floor Plan</span>
    </div>
  </div>

  <!-- Front Desk Control Header -->
  <div class="card" style="margin-bottom:24px;">
    <div class="card-header" style="flex-wrap:wrap; gap:16px;">
      <div>
        <h2 style="font-size:1.35rem; font-weight:700; margin:0 0 4px 0;">Reception Front Desk & Host Operations</h2>
        <p class="text-sm" style="margin:0; color:var(--text-muted);">
          Real-time table seating, instant guest check-in, active bill settlement, bKash QR payments, and thermal receipt printing
        </p>
      </div>

      <!-- Quick Action Buttons -->
      <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
        <button class="btn btn-secondary btn-sm" onclick="SmartReception.refreshAll()">🔄 Refresh Desk</button>
        <button class="btn btn-secondary btn-sm" onclick="document.getElementById('modal-create-customer').classList.add('active')">👤 + Register Customer</button>
        <button class="btn btn-secondary btn-sm" onclick="document.getElementById('modal-create-reservation').classList.add('active')">📅 + Quick Reservation</button>
        <button class="btn btn-primary btn-sm" onclick="SmartBilling.openOrderSelectionModal()">💳 + Settle Active Order</button>
      </div>
    </div>

    <!-- Quick Search Bar -->
    <div style="padding:0 var(--space-4) 16px var(--space-4);">
      <input type="text" id="reception-global-search" class="form-control" placeholder="🔍 Quick Search by Guest Name, Phone #, Order #, or Table #..." style="font-size:0.95rem; padding:12px 16px; border-radius:10px;">
    </div>
  </div>

  <!-- Tab Navigation Controls -->
  <div style="display:flex; gap:8px; border-bottom: 2px solid var(--border); margin-bottom: 20px; overflow-x:auto; padding-bottom:2px;">
    <button class="btn btn-secondary reception-tab-btn active" data-tab="bills" onclick="SmartReception.switchTab('bills', this)">
      💳 Active Unpaid Bills
    </button>
    <button class="btn btn-secondary reception-tab-btn" data-tab="reservations" onclick="SmartReception.switchTab('reservations', this)">
      📅 Table Reservations & Seating
    </button>
    <button class="btn btn-secondary reception-tab-btn" data-tab="floors" onclick="SmartReception.switchTab('floors', this)">
      🪑 Live Floor Plan Map
    </button>
    <button class="btn btn-secondary reception-tab-btn" data-tab="customers" onclick="SmartReception.switchTab('customers', this)">
      👥 Customer CRM & Directory
    </button>
    <button class="btn btn-secondary reception-tab-btn" data-tab="ledger" onclick="SmartReception.switchTab('ledger', this)">
      📑 Settlement Ledger History
    </button>
  </div>

  <!-- Tab Pane 1: Active Unpaid Bills -->
  <div id="reception-pane-bills" class="reception-tab-pane">
    <div id="reception-active-bills-grid" style="display:grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap:16px;">
      <div style="grid-column:1/-1; text-align:center; padding:40px; color:var(--text-muted);">Loading active unpaid bills...</div>
    </div>
  </div>

  <!-- Tab Pane 2: Table Reservations -->
  <div id="reception-pane-reservations" class="reception-tab-pane" style="display:none;">
    <div class="card">
      <div class="card-header">
        <h3>Today's Reservations & Host Desk Check-in</h3>
        <button class="btn btn-primary btn-sm" onclick="document.getElementById('modal-create-reservation').classList.add('active')">+ Create Reservation</button>
      </div>
      <div class="table-container">
        <table class="data-table">
          <thead>
            <tr>
              <th>RES ID</th>
              <th>GUEST NAME / PHONE</th>
              <th>DATE & TIME</th>
              <th>GUESTS</th>
              <th>ASSIGNED TABLE</th>
              <th>STATUS</th>
              <th>ACTIONS</th>
            </tr>
          </thead>
          <tbody id="reception-reservations-tbody">
            <tr><td colspan="7" style="text-align:center; padding:30px; color:var(--text-muted);">Loading reservations...</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Tab Pane 3: Live Floor Plan Map -->
  <div id="reception-pane-floors" class="reception-tab-pane" style="display:none;">
    <div class="card" style="margin-bottom:16px;">
      <div class="card-header">
        <h3>Interactive Floor Plan & Dining Status</h3>
        <span class="text-sm" style="color:var(--text-muted);">Click any available table to seat guest or occupied table to settle bill</span>
      </div>
    </div>
    <div id="reception-floor-grid" style="display:grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap:16px;">
      <div style="grid-column:1/-1; text-align:center; padding:40px; color:var(--text-muted);">Loading floor plan tables...</div>
    </div>
  </div>

  <!-- Tab Pane 4: Customer Directory -->
  <div id="reception-pane-customers" class="reception-tab-pane" style="display:none;">
    <div class="card">
      <div class="card-header">
        <h3>Customer Profiles & Loyalty CRM</h3>
        <button class="btn btn-primary btn-sm" onclick="document.getElementById('modal-create-customer').classList.add('active')">+ Add New Customer</button>
      </div>
      <div class="table-container">
        <table class="data-table">
          <thead>
            <tr>
              <th>CUSTOMER NAME</th>
              <th>PHONE</th>
              <th>EMAIL</th>
              <th>LOYALTY POINTS</th>
              <th>VISITS</th>
              <th>ACTION</th>
            </tr>
          </thead>
          <tbody id="reception-customers-tbody">
            <tr><td colspan="6" style="text-align:center; padding:30px; color:var(--text-muted);">Loading customer directory...</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Tab Pane 5: Payment Ledger -->
  <div id="reception-pane-ledger" class="reception-tab-pane" style="display:none;">
    <div class="card">
      <div class="card-header">
        <h3>Payment Settlements & Ledger</h3>
        <button class="btn btn-secondary btn-sm" onclick="SmartBilling.loadPaymentHistory()">🔄 Refresh Ledger</button>
      </div>
      <div class="table-container">
        <table class="data-table">
          <thead>
            <tr>
              <th>PAYMENT ID</th>
              <th>ORDER #</th>
              <th>TABLE / TYPE</th>
              <th>METHOD</th>
              <th>AMOUNT</th>
              <th>TRX REF</th>
              <th>CASHIER</th>
              <th>STATUS</th>
              <th>ACTIONS</th>
            </tr>
          </thead>
          <tbody id="payments-table-tbody">
            <tr><td colspan="9" style="text-align:center; padding:40px; color:var(--text-muted);">Loading payment transactions...</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</section>
