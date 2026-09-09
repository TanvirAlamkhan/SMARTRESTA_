<!-- VIEW: DEDICATED WAITER DESK & TABLE ORDERING PORTAL -->
<section id="waiter" class="role-view" style="display:none;">

  <!-- Waiter KPI Summary Grid -->
  <div class="grid-kpi" id="waiter-kpi-bar" style="margin-bottom:24px;">
    <div class="kpi-card" style="border-left: 4px solid var(--accent, #6366f1);">
      <span class="kpi-label">My Active Tables</span>
      <div class="kpi-value" id="wtr-kpi-tables-count" style="color:var(--accent, #6366f1);">0</div>
      <span class="kpi-trend positive">Floor Seating</span>
    </div>

    <div class="kpi-card" style="border-left: 4px solid var(--warning, #f59e0b);">
      <span class="kpi-label">My Open Sessions</span>
      <div class="kpi-value" id="wtr-kpi-sessions-count" style="color:var(--warning, #f59e0b);">0</div>
      <span class="kpi-trend negative">Active Dining</span>
    </div>

    <div class="kpi-card" style="border-left: 4px solid var(--info, #3b82f6);">
      <span class="kpi-label">My Active Orders</span>
      <div class="kpi-value" id="wtr-kpi-orders-count" style="color:var(--info, #3b82f6);">0</div>
      <span class="kpi-trend positive">In Progress</span>
    </div>

    <div class="kpi-card" style="border-left: 4px solid #8b5cf6;">
      <span class="kpi-label">Kitchen Status</span>
      <div class="kpi-value" id="wtr-kpi-kitchen-status" style="font-size:1.25rem; color:#8b5cf6;">0 Prep / 0 Ready</div>
      <span class="kpi-trend positive">KDS Ticket SLA</span>
    </div>

    <div class="kpi-card" style="border-left: 4px solid var(--success, #10b981);">
      <span class="kpi-label">Today's Sales & Commission</span>
      <div class="kpi-value" id="wtr-kpi-today-sales" style="color:var(--success, #10b981);">৳0.00</div>
      <span class="kpi-trend positive" id="wtr-kpi-today-comm">৳0.00 Commission</span>
    </div>
  </div>

  <!-- Waiter Control Header -->
  <div class="card" style="margin-bottom:24px;">
    <div class="card-header" style="flex-wrap:wrap; gap:16px;">
      <div>
        <h2 style="font-size:1.35rem; font-weight:700; margin:0 0 4px 0;">Waiter Station & Floor Operations</h2>
        <p class="text-sm" style="margin:0; color:var(--text-muted);">
          Table seating, POS order creation, variants & extra modifiers, kitchen dispatch, and real-time SLA ready order tracking
        </p>
      </div>

      <!-- Quick Action Buttons -->
      <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
        <button class="btn btn-secondary btn-sm" onclick="SmartWaiter.refreshAll()">🔄 Refresh Desk</button>
        <button class="btn btn-secondary btn-sm" onclick="document.getElementById('modal-create-customer').classList.add('active')">👤 + Add Customer</button>
        <button class="btn btn-secondary btn-sm" onclick="document.getElementById('open-session-modal').classList.add('active')">🪑 + Seat Table Session</button>
        <button class="btn btn-primary btn-sm" onclick="SmartWaiter.switchTab('pos')">🛒 + New POS Order</button>
      </div>
    </div>

    <!-- Quick Search Bar -->
    <div style="padding:0 var(--space-4) 16px var(--space-4);">
      <input type="text" id="waiter-global-search" class="form-control" placeholder="🔍 Quick Search by Table #, Order #, Guest Name, Product..." style="font-size:0.95rem; padding:12px 16px; border-radius:10px;">
    </div>
  </div>

  <!-- Tab Navigation Controls -->
  <div style="display:flex; gap:8px; border-bottom: 2px solid var(--border); margin-bottom: 20px; overflow-x:auto; padding-bottom:2px;">
    <button class="btn btn-secondary waiter-tab-btn active" data-tab="pos" onclick="SmartWaiter.switchTab('pos', this)">
      🛒 POS Order Builder
    </button>
    <button class="btn btn-secondary waiter-tab-btn" data-tab="floors" onclick="SmartWaiter.switchTab('floors', this)">
      🪑 Interactive Floor Map
    </button>
    <button class="btn btn-secondary waiter-tab-btn" data-tab="sessions" onclick="SmartWaiter.switchTab('sessions', this)">
      🍷 Dining Sessions
    </button>
    <button class="btn btn-secondary waiter-tab-btn" data-tab="kitchen" onclick="SmartWaiter.switchTab('kitchen', this)">
      🍳 Kitchen Feed & Ready Orders
    </button>
    <button class="btn btn-secondary waiter-tab-btn" data-tab="orders" onclick="SmartWaiter.switchTab('orders', this)">
      📑 My Orders & History
    </button>
    <button class="btn btn-secondary waiter-tab-btn" data-tab="performance" onclick="SmartWaiter.switchTab('performance', this)">
      📈 Sales & Commission
    </button>
  </div>

  <!-- Tab Pane 1: POS Order Builder -->
  <div id="waiter-pane-pos" class="waiter-tab-pane">
    <div class="pos-layout">
      
      <!-- Product Catalog Column -->
      <div>
        <!-- Table & Customer Selection Bar -->
        <div style="background:var(--surface); border:1px solid var(--border); border-radius:12px; padding:16px; margin-bottom:16px; display:flex; flex-direction:column; gap:12px;">
          <div style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
            <div style="flex:1; min-width:200px;">
              <label style="font-weight:600; font-size:0.8rem; display:block; margin-bottom:4px;">Select Dining Table *</label>
              <select id="wtr-pos-table-selector" class="form-control" onchange="SmartWaiter.onTableSelect(this)">
                <option value="">— Choose Table —</option>
              </select>
            </div>

            <div style="flex:1; min-width:200px;">
              <label style="font-weight:600; font-size:0.8rem; display:block; margin-bottom:4px;">Customer Profile (Optional)</label>
              <select id="wtr-pos-customer-select" class="form-control">
                <option value="">— Walk-in / Guest —</option>
              </select>
            </div>

            <div style="padding-top:20px; display:flex; gap:8px;">
              <button type="button" class="btn btn-secondary btn-sm" onclick="SmartWaiter.setTakeawayMode()">🛍️ Takeaway</button>
              <span id="wtr-pos-table-heading" class="badge badge-info" style="font-size:0.85rem; padding:8px 12px;">No Table Selected</span>
            </div>
          </div>
        </div>

        <!-- Dynamic Category Pills -->
        <div class="category-slider" id="wtr-pos-category-pills" style="margin-bottom:16px;">
          <button class="category-pill active" data-cat="" onclick="SmartWaiter.filterCategory('', this)">All Menu Items</button>
        </div>

        <!-- Dynamic Product Grid -->
        <div class="product-grid" id="wtr-pos-product-grid">
          <div style="grid-column:1/-1; text-align:center; padding:40px; color:var(--text-muted);">
            <div style="font-size:2rem; margin-bottom:8px;">🍽️</div>
            <p>Loading menu product catalog...</p>
          </div>
        </div>
      </div>

      <!-- POS Order Cart Column -->
      <div class="pos-cart">
        <div class="cart-header">
          <h3 id="wtr-cart-heading">Current Order</h3>
          <span class="badge badge-info">Waiter POS</span>
        </div>

        <div id="wtr-pos-cart-items" class="cart-items-list">
          <div style="text-align:center; padding: 40px var(--space-4); color: var(--text-muted);">
            <div style="font-size: 2rem; margin-bottom: 8px;">🛒</div>
            <p style="font-size:0.875rem;">No items added to current order</p>
          </div>
        </div>

        <div class="cart-footer">
          <div class="cart-summary-row">
            <span>Item Subtotal</span>
            <span id="wtr-cart-subtotal">৳0.00</span>
          </div>
          <div class="cart-summary-row">
            <span>VAT (5%)</span>
            <span id="wtr-cart-tax">৳0.00</span>
          </div>
          <div class="cart-summary-row total">
            <span>Grand Total</span>
            <span id="wtr-cart-total" class="price-tag">৳0.00</span>
          </div>

          <div class="form-group" style="margin-top:12px; margin-bottom:12px;">
            <label class="form-label" style="font-size:0.8rem;">Special Order Notes / Allergies</label>
            <input type="text" id="wtr-pos-order-notes" class="form-control" placeholder="e.g. Extra spicy, No onions, High chair">
          </div>

          <!-- Dual Dispatch Action Buttons -->
          <div style="display:flex; flex-direction:column; gap:8px; margin-top:12px;">
            <button class="btn btn-primary btn-lg" style="width:100%; font-weight:700;" onclick="SmartWaiter.submitOrder('KITCHEN')">
              🚀 Send Order to Kitchen (Direct)
            </button>
            <button class="btn btn-secondary btn-lg" style="width:100%; border-color:var(--accent); color:var(--accent);" onclick="SmartWaiter.submitOrder('RECEPTION')">
              💳 Send to Reception (Pay-First)
            </button>
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- Tab Pane 2: Interactive Floor Map -->
  <div id="waiter-pane-floors" class="waiter-tab-pane" style="display:none;">
    <div class="card" style="margin-bottom:16px;">
      <div class="card-header">
        <h3>Restaurant Floor Map & Dining Status</h3>
        <span class="text-sm" style="color:var(--text-muted);">Real-time table statuses, capacity, active dining session, and 1-click order creation</span>
      </div>
    </div>
    <div id="wtr-floor-grid" style="display:grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap:16px;">
      <div style="grid-column:1/-1; text-align:center; padding:40px; color:var(--text-muted);">Loading floor tables...</div>
    </div>
  </div>

  <!-- Tab Pane 3: Active Dining Sessions -->
  <div id="waiter-pane-sessions" class="waiter-tab-pane" style="display:none;">
    <div class="card">
      <div class="card-header">
        <h3>Active Floor Dining Sessions</h3>
        <button class="btn btn-primary btn-sm" onclick="document.getElementById('open-session-modal').classList.add('active')">+ Open New Session</button>
      </div>
      <div class="table-container">
        <table class="data-table">
          <thead>
            <tr>
              <th>SESSION ID</th>
              <th>TABLE #</th>
              <th>GUEST COUNT</th>
              <th>SEATING TIME</th>
              <th>ACTIVE ORDER</th>
              <th>ORDER TOTAL</th>
              <th>STATUS</th>
              <th>ACTIONS</th>
            </tr>
          </thead>
          <tbody id="wtr-sessions-tbody">
            <tr><td colspan="8" style="text-align:center; padding:30px; color:var(--text-muted);">Loading active dining sessions...</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Tab Pane 4: Kitchen Status & Ready Orders -->
  <div id="waiter-pane-kitchen" class="waiter-tab-pane" style="display:none;">
    
    <!-- READY Orders Highlight Banner -->
    <div id="wtr-ready-orders-banner" style="margin-bottom:20px; display:none;">
      <!-- Populated dynamically when orders become READY in kitchen -->
    </div>

    <div class="card">
      <div class="card-header">
        <h3>Sent Orders — Live Kitchen Display Status</h3>
        <button class="btn btn-secondary btn-sm" onclick="SmartWaiter.loadKitchenFeed()">🔄 Refresh Kitchen Tickets</button>
      </div>
      <div id="wtr-kitchen-tickets-grid" style="display:grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap:16px; padding:16px;">
        <div style="grid-column:1/-1; text-align:center; padding:40px; color:var(--text-muted);">Loading kitchen order tickets...</div>
      </div>
    </div>
  </div>

  <!-- Tab Pane 5: My Orders & Order History -->
  <div id="waiter-pane-orders" class="waiter-tab-pane" style="display:none;">
    <div class="card">
      <div class="card-header" style="flex-wrap:wrap; gap:12px;">
        <h3>My Orders & Historical Orders Log</h3>
        <div style="display:flex; gap:8px;">
          <select id="wtr-orders-status-filter" class="form-control" style="max-width:160px;" onchange="SmartWaiter.filterOrders()">
            <option value="">All Statuses</option>
            <option value="DRAFT">DRAFT</option>
            <option value="SUBMITTED">SUBMITTED</option>
            <option value="PREPARING">PREPARING</option>
            <option value="READY">READY</option>
            <option value="SERVED">SERVED</option>
            <option value="COMPLETED">COMPLETED</option>
            <option value="CANCELLED">CANCELLED</option>
          </select>
          <button class="btn btn-secondary btn-sm" onclick="SmartWaiter.loadMyOrders()">🔄 Refresh Orders</button>
        </div>
      </div>
      <div class="table-container">
        <table class="data-table">
          <thead>
            <tr>
              <th>ORDER #</th>
              <th>TABLE</th>
              <th>ORDER TYPE</th>
              <th>ITEMS</th>
              <th>TOTAL</th>
              <th>PAYMENT STATUS</th>
              <th>ORDER STATUS</th>
              <th>CREATED AT</th>
              <th>ACTION</th>
            </tr>
          </thead>
          <tbody id="wtr-orders-tbody">
            <tr><td colspan="9" style="text-align:center; padding:40px; color:var(--text-muted);">Loading order history...</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Tab Pane 6: Sales & Commission Performance -->
  <div id="waiter-pane-performance" class="waiter-tab-pane" style="display:none;">
    
    <!-- Waiter Sales Metrics Cards -->
    <div class="grid-kpi" style="margin-bottom:20px;">
      <div class="kpi-card" style="border-left:4px solid var(--accent);">
        <span class="kpi-label">Total Sales Volume</span>
        <div class="kpi-value" id="wtr-matrix-total-sales">৳0.00</div>
        <span class="kpi-trend positive" id="wtr-matrix-paid-sales">৳0.00 Paid</span>
      </div>
      <div class="kpi-card" style="border-left:4px solid var(--info);">
        <span class="kpi-label">Orders Completed</span>
        <div class="kpi-value" id="wtr-matrix-total-orders">0</div>
        <span class="kpi-trend positive" id="wtr-matrix-avg-order">৳0.00 Avg Order</span>
      </div>
      <div class="kpi-card" style="border-left:4px solid var(--warning);">
        <span class="kpi-label">Commission Rate</span>
        <div class="kpi-value" style="color:var(--warning);">5.0%</div>
        <span class="kpi-trend positive">Standard Rate</span>
      </div>
      <div class="kpi-card" style="border-left:4px solid var(--success);">
        <span class="kpi-label">Accrued Commission</span>
        <div class="kpi-value" id="wtr-matrix-total-comm" style="color:var(--success);">৳0.00</div>
        <span class="kpi-trend positive" id="wtr-matrix-approved-comm">৳0.00 Approved</span>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <h3>My Commission Transaction Ledger</h3>
        <button class="btn btn-secondary btn-sm" onclick="SmartWaiter.loadMyCommission()">🔄 Refresh Ledger</button>
      </div>
      <div class="table-container">
        <table class="data-table">
          <thead>
            <tr>
              <th>COMMISSION ID</th>
              <th>ORDER #</th>
              <th>COMMISSION RULE</th>
              <th>ORDER TOTAL</th>
              <th>COMMISSION AMOUNT</th>
              <th>STATUS</th>
              <th>DATE</th>
            </tr>
          </thead>
          <tbody id="wtr-commission-tbody">
            <tr><td colspan="7" style="text-align:center; padding:40px; color:var(--text-muted);">Loading commission ledger...</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</section>
