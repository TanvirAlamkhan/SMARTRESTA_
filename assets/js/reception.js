/**
 * SMARTRESTA — Frontend Reception & Cashier Host Desk Controller
 * Module: SmartReception
 */

var SmartReception = window.SmartReception || {
  activeTab: 'bills',
  allBills: [],
  allReservations: [],
  allTables: [],
  allCustomers: [],

  async init() {
    this.bindEvents();
    await this.refreshAll();
  },

  bindEvents() {
    const searchInput = document.getElementById('reception-global-search');
    if (searchInput) {
      searchInput.addEventListener('input', (e) => this.filterGlobal(e.target.value));
    }
  },

  async refreshAll() {
    try {
      await Promise.all([
        this.loadKPIs(),
        this.loadActiveBills(),
        this.loadReservations(),
        this.loadFloorMap(),
        this.loadCustomerDirectory()
      ]);
    } catch (err) {
      console.warn('Reception refresh error:', err);
    }
  },

  switchTab(tabName, btnEl) {
    this.activeTab = tabName;

    document.querySelectorAll('.reception-tab-btn').forEach(b => b.classList.remove('active'));
    if (btnEl) {
      btnEl.classList.add('active');
    } else {
      const targetBtn = document.querySelector(`.reception-tab-btn[data-tab="${tabName}"]`);
      if (targetBtn) targetBtn.classList.add('active');
    }

    document.querySelectorAll('.reception-tab-pane').forEach(p => p.style.display = 'none');
    const pane = document.getElementById(`reception-pane-${tabName}`);
    if (pane) pane.style.display = 'block';

    if (tabName === 'bills') this.loadActiveBills();
    else if (tabName === 'reservations') this.loadReservations();
    else if (tabName === 'floors') this.loadFloorMap();
    else if (tabName === 'customers') this.loadCustomerDirectory();
    else if (tabName === 'ledger' && typeof SmartBilling !== 'undefined') SmartBilling.loadPaymentHistory();
  },

  async loadKPIs() {
    try {
      const [ordRes, resRes, tblRes, payRes] = await Promise.allSettled([
        SmartAPI.get('api/v1/orders/index.php'),
        SmartAPI.get('api/v1/crm/reservations.php'),
        SmartAPI.get('api/v1/tables/index.php'),
        SmartAPI.get('api/v1/payments/index.php')
      ]);

      // Unpaid Bills KPI
      if (ordRes.status === 'fulfilled' && ordRes.value.success && ordRes.value.data) {
        const unpaid = ordRes.value.data.filter(o => o.payment_status !== 'PAID' && o.order_status !== 'CANCELLED');
        let outstandingSum = 0;
        unpaid.forEach(o => {
          const grand = parseFloat(o.grand_total || 0);
          const paid = parseFloat(o.total_paid || 0);
          outstandingSum += Math.max(0, grand - paid);
        });

        const openCountEl = document.getElementById('rec-kpi-open-count');
        if (openCountEl) openCountEl.textContent = unpaid.length;
        const openBalEl = document.getElementById('rec-kpi-open-bal');
        if (openBalEl) openBalEl.textContent = `৳${outstandingSum.toFixed(2)} Outstanding`;
      }

      // Today's Net Collections & Register Cash KPI
      if (payRes.status === 'fulfilled' && payRes.value.success && payRes.value.data) {
        let net = 0;
        let cash = 0;
        payRes.value.data.forEach(p => {
          if ((p.status || '').toUpperCase() === 'COMPLETED') {
            const amt = parseFloat(p.amount || 0);
            net += amt;
            if ((p.payment_method_name || '').toLowerCase().includes('cash')) {
              cash += amt;
            }
          }
        });
        const netEl = document.getElementById('rec-kpi-today-net');
        if (netEl) netEl.textContent = `৳${net.toFixed(2)}`;
        const cashEl = document.getElementById('rec-kpi-cash-total');
        if (cashEl) cashEl.textContent = `৳${cash.toFixed(2)}`;
      }

      // Arriving Reservations KPI
      if (resRes.status === 'fulfilled' && resRes.value.success && resRes.value.data) {
        const pendingOrConfirmed = resRes.value.data.filter(r => ['PENDING', 'CONFIRMED'].includes((r.status || '').toUpperCase()));
        const resCntEl = document.getElementById('rec-kpi-reservations-count');
        if (resCntEl) resCntEl.textContent = pendingOrConfirmed.length;
      }

      // Tables Occupancy KPI
      if (tblRes.status === 'fulfilled' && tblRes.value.success && tblRes.value.data) {
        const total = tblRes.value.data.length;
        const occupied = tblRes.value.data.filter(t => (t.status || '').toUpperCase() === 'OCCUPIED').length;
        const occEl = document.getElementById('rec-kpi-occupancy');
        if (occEl) occEl.textContent = `${occupied} / ${total} Occupied`;
      }

    } catch (err) {
      console.warn('Error loading reception KPIs:', err);
    }
  },

  async loadActiveBills() {
    const container = document.getElementById('reception-active-bills-grid');
    if (!container) return;

    try {
      const res = await SmartAPI.get('api/v1/orders/index.php');
      if (res.success && res.data) {
        this.allBills = res.data.filter(o => o.payment_status !== 'PAID' && o.order_status !== 'CANCELLED');
        this.renderBills(this.allBills);
      } else {
        container.innerHTML = `<div style="grid-column:1/-1; text-align:center; padding:40px; color:var(--text-muted);">No unpaid active bills at reception desk.</div>`;
      }
    } catch (err) {
      container.innerHTML = `<div style="grid-column:1/-1; text-align:center; padding:40px; color:var(--danger);">Failed to load active bills.</div>`;
    }
  },

  renderBills(bills) {
    const container = document.getElementById('reception-active-bills-grid');
    if (!container) return;

    if (!bills || bills.length === 0) {
      container.innerHTML = `<div style="grid-column:1/-1; text-align:center; padding:40px; color:var(--text-muted); background:var(--surface); border:1px solid var(--border); border-radius:12px;">🎉 All dining & takeaway orders are fully settled!</div>`;
      return;
    }

    container.innerHTML = bills.map(b => {
      const grand = parseFloat(b.grand_total || 0);
      const paid = parseFloat(b.total_paid || 0);
      const balance = Math.max(0, grand - paid);
      const isPartial = paid > 0;

      return `
        <div class="card" style="display:flex; flex-direction:column; justify-space-between; gap:12px; border-left:4px solid ${isPartial ? 'var(--warning)' : 'var(--accent)'}; box-shadow:var(--shadow-sm);">
          <div style="display:flex; justify-content:space-between; align-items:flex-start;">
            <div>
              <div style="font-weight:700; font-size:1.1rem; color:var(--text-primary);">Order #${b.order_number || b.id}</div>
              <div class="text-sm" style="color:var(--text-muted);">
                ${b.table_number ? `🪑 Table <strong>${b.table_number}</strong>` : '🥡 Takeaway / Delivery'}
                ${b.waiter_name ? ` • Waiter: <strong>${b.waiter_name}</strong>` : ''}
              </div>
            </div>
            <span class="badge badge-${isPartial ? 'warning' : 'info'}">${b.payment_status || 'UNPAID'}</span>
          </div>

          <div style="background:var(--surface-hover); padding:10px 14px; border-radius:8px; display:grid; grid-template-columns:1fr 1fr 1fr; gap:8px; text-align:center;">
            <div>
              <span style="font-size:0.7rem; color:var(--text-muted); display:block; font-weight:600;">TOTAL</span>
              <strong style="font-size:0.95rem;">৳${grand.toFixed(2)}</strong>
            </div>
            <div>
              <span style="font-size:0.7rem; color:var(--text-muted); display:block; font-weight:600;">PAID</span>
              <strong style="font-size:0.95rem; color:var(--success);">৳${paid.toFixed(2)}</strong>
            </div>
            <div>
              <span style="font-size:0.7rem; color:var(--text-muted); display:block; font-weight:600;">BALANCE</span>
              <strong style="font-size:0.95rem; color:var(--danger);">৳${balance.toFixed(2)}</strong>
            </div>
          </div>

          <div style="display:flex; gap:8px; margin-top:4px;">
            <button class="btn btn-primary btn-sm" style="flex:1;" onclick="SmartBilling.openBillingModal(${b.id})">
              💳 Settle Bill (৳${balance.toFixed(2)})
            </button>
            <button class="btn btn-secondary btn-sm" onclick="SmartBilling.openReceiptModal(0, ${b.id})" title="Print Pre-Bill Receipt">
              🖨️
            </button>
          </div>
        </div>
      `;
    }).join('');
  },

  async loadReservations() {
    const tbody = document.getElementById('reception-reservations-tbody');
    if (!tbody) return;

    try {
      const res = await SmartAPI.get('api/v1/crm/reservations.php');
      if (res.success && res.data) {
        this.allReservations = res.data;
        this.renderReservations(this.allReservations);
      } else {
        tbody.innerHTML = `<tr><td colspan="7" style="text-align:center; padding:30px; color:var(--text-muted);">No table reservations registered for today.</td></tr>`;
      }
    } catch (err) {
      tbody.innerHTML = `<tr><td colspan="7" style="text-align:center; padding:30px; color:var(--danger);">Failed to load table reservations.</td></tr>`;
    }
  },

  renderReservations(reservations) {
    const tbody = document.getElementById('reception-reservations-tbody');
    if (!tbody) return;

    if (!reservations || reservations.length === 0) {
      tbody.innerHTML = `<tr><td colspan="7" style="text-align:center; padding:30px; color:var(--text-muted);">No reservations found.</td></tr>`;
      return;
    }

    tbody.innerHTML = reservations.map(r => {
      const st = (r.status || 'PENDING').toUpperCase();
      let badgeClass = 'warning';
      if (st === 'CONFIRMED') badgeClass = 'info';
      else if (st === 'SEATED') badgeClass = 'success';
      else if (st === 'CANCELLED') badgeClass = 'danger';

      return `
        <tr>
          <td><span class="font-mono">#RES-${r.id}</span></td>
          <td>
            <strong>${r.customer_name || 'Guest'}</strong>
            <div class="text-sm" style="color:var(--text-muted);">${r.customer_phone || ''}</div>
          </td>
          <td>📅 ${r.reservation_date} at <strong>${r.reservation_time}</strong></td>
          <td>👥 <strong>${r.guest_count} Guests</strong></td>
          <td>${r.table_number ? `Table ${r.table_number}` : '<span class="text-muted">Unassigned</span>'}</td>
          <td><span class="badge badge-${badgeClass}">${st}</span></td>
          <td>
            <div style="display:flex; gap:6px;">
              ${st !== 'SEATED' && st !== 'CANCELLED' ? `
                <button class="btn btn-success btn-sm" onclick="SmartReception.seatGuestPrompt(${r.id}, ${r.table_id || 'null'})">🪑 Seat Guest</button>
                <button class="btn btn-secondary btn-sm" onclick="SmartReception.updateReservationStatus(${r.id}, 'CANCELLED')">✕ Cancel</button>
              ` : '<span class="text-sm text-muted">Completed</span>'}
            </div>
          </td>
        </tr>
      `;
    }).join('');
  },

  async seatGuestPrompt(resId, defaultTableId) {
    let tableId = defaultTableId;
    if (!tableId) {
      const input = prompt('Enter Destination Table ID to seat guest:');
      if (!input) return;
      tableId = parseInt(input, 10);
    }
    await this.updateReservationStatus(resId, 'SEATED', tableId);
  },

  async updateReservationStatus(resId, status, tableId = null) {
    try {
      const res = await SmartAPI.post('api/v1/crm/reservations.php', {
        action: 'update_status',
        reservation_id: resId,
        status: status,
        table_id: tableId
      });

      if (res.success) {
        SmartNotifications.show(`Reservation #${resId} marked as ${status}`, 'success');
        this.loadReservations();
        this.loadFloorMap();
        this.loadKPIs();
      }
    } catch (err) {
      SmartNotifications.show(err.message || 'Failed to update reservation status', 'danger');
    }
  },

  async loadFloorMap() {
    const container = document.getElementById('reception-floor-grid');
    if (!container) return;

    try {
      const res = await SmartAPI.get('api/v1/tables/index.php');
      if (res.success && res.data) {
        this.allTables = res.data;
        this.renderFloorGrid(this.allTables);
      } else {
        container.innerHTML = `<div style="grid-column:1/-1; text-align:center; padding:30px; color:var(--text-muted);">No tables defined on floor.</div>`;
      }
    } catch (err) {
      container.innerHTML = `<div style="grid-column:1/-1; text-align:center; padding:30px; color:var(--danger);">Failed to load floor tables.</div>`;
    }
  },

  renderFloorGrid(tables) {
    const container = document.getElementById('reception-floor-grid');
    if (!container) return;

    if (!tables || tables.length === 0) {
      container.innerHTML = `<div style="grid-column:1/-1; text-align:center; padding:30px; color:var(--text-muted);">No floor tables found.</div>`;
      return;
    }

    container.innerHTML = tables.map(t => {
      const st = (t.status || 'AVAILABLE').toUpperCase();
      let borderColor = 'var(--success)';
      let badgeClass = 'success';
      let bgStyle = 'var(--surface)';

      if (st === 'OCCUPIED') {
        borderColor = 'var(--warning)';
        badgeClass = 'warning';
        bgStyle = 'var(--warning-light)';
      } else if (st === 'RESERVED') {
        borderColor = 'var(--info)';
        badgeClass = 'info';
      } else if (st === 'CLEANING') {
        borderColor = 'var(--text-muted)';
        badgeClass = 'neutral';
      }

      return `
        <div class="table-card" style="border-color:${borderColor}; background:${bgStyle}; padding:14px; border-radius:12px; display:flex; flex-direction:column; justify-content:space-between; gap:10px; box-shadow:var(--shadow-sm);">
          <div style="display:flex; justify-content:space-between; align-items:center;">
            <div>
              <div style="font-weight:700; font-size:1.15rem;">Table ${t.table_number}</div>
              <div class="text-sm" style="color:var(--text-muted);">${t.floor_name || 'Main Hall'} • 👥 ${t.seating_capacity} Seats</div>
            </div>
            <span class="badge badge-${badgeClass}">${st}</span>
          </div>

          <div style="display:flex; gap:6px; margin-top:4px;">
            ${st === 'AVAILABLE' ? `
              <button class="btn btn-primary btn-sm" style="width:100%;" onclick="SmartReception.openSessionForTable(${t.id})">
                ➕ Seat Guest
              </button>
            ` : (st === 'OCCUPIED' ? `
              <button class="btn btn-warning btn-sm" style="width:100%;" onclick="SmartReception.settleTableSession(${t.id})">
                💳 Settle Table
              </button>
            ` : `
              <button class="btn btn-secondary btn-sm" style="width:100%;" onclick="SmartReception.openSessionForTable(${t.id})">
                View Status
              </button>
            `)}
          </div>
        </div>
      `;
    }).join('');
  },

  openSessionForTable(tableId) {
    const tableInput = document.getElementById('open-session-table-id');
    if (tableInput) tableInput.value = tableId;
    const titleEl = document.getElementById('session-modal-title');
    if (titleEl) titleEl.textContent = `Open Dining Session — Table #${tableId}`;
    SmartModal.open('open-session-modal');
  },

  async settleTableSession(tableId) {
    try {
      const res = await SmartAPI.get('api/v1/orders/index.php');
      if (res.success && res.data) {
        const order = res.data.find(o => parseInt(o.table_id, 10) === parseInt(tableId, 10) && o.payment_status !== 'PAID');
        if (order) {
          SmartBilling.openBillingModal(order.id);
        } else {
          SmartNotifications.show(`No active unpaid order found for Table #${tableId}`, 'info');
        }
      }
    } catch (e) {
      SmartNotifications.show('Failed to find active order for table', 'danger');
    }
  },

  async loadCustomerDirectory() {
    const tbody = document.getElementById('reception-customers-tbody');
    if (!tbody) return;

    try {
      const res = await SmartAPI.get('api/v1/crm/customers.php');
      if (res.success && res.data) {
        this.allCustomers = Array.isArray(res.data) ? res.data : (res.data.customers || []);
        this.renderCustomers(this.allCustomers);
      } else {
        tbody.innerHTML = `<tr><td colspan="6" style="text-align:center; padding:30px; color:var(--text-muted);">No customer profiles registered.</td></tr>`;
      }
    } catch (err) {
      tbody.innerHTML = `<tr><td colspan="6" style="text-align:center; padding:30px; color:var(--danger);">Failed to load customers.</td></tr>`;
    }
  },

  renderCustomers(customers) {
    const tbody = document.getElementById('reception-customers-tbody');
    if (!tbody) return;

    if (!customers || customers.length === 0) {
      tbody.innerHTML = `<tr><td colspan="6" style="text-align:center; padding:30px; color:var(--text-muted);">No matching customers found.</td></tr>`;
      return;
    }

    tbody.innerHTML = customers.map(c => `
      <tr>
        <td>
          <strong>${c.name}</strong>
          ${c.is_vip ? '<span class="badge badge-warning" style="margin-left:6px;">⭐ VIP</span>' : ''}
        </td>
        <td>📞 <strong>${c.phone || 'N/A'}</strong></td>
        <td>${c.email || '<span class="text-muted">—</span>'}</td>
        <td>🏆 ${c.loyalty_points || 0} Points</td>
        <td>🍽️ ${c.total_visits || 0} Visits</td>
        <td>
          <button class="btn btn-secondary btn-sm" onclick="SmartCRM.openCustomerProfileModal(${c.id})">Profile</button>
        </td>
      </tr>
    `).join('');
  },

  filterGlobal(query) {
    const q = (query || '').toLowerCase().trim();
    if (!q) {
      this.renderBills(this.allBills);
      this.renderReservations(this.allReservations);
      this.renderCustomers(this.allCustomers);
      return;
    }

    // Filter Active Bills
    const filteredBills = (this.allBills || []).filter(b => 
      (b.order_number && b.order_number.toString().toLowerCase().includes(q)) ||
      (b.table_number && b.table_number.toString().toLowerCase().includes(q)) ||
      (b.waiter_name && b.waiter_name.toLowerCase().includes(q))
    );
    this.renderBills(filteredBills);

    // Filter Reservations
    const filteredRes = (this.allReservations || []).filter(r => 
      (r.customer_name && r.customer_name.toLowerCase().includes(q)) ||
      (r.customer_phone && r.customer_phone.toLowerCase().includes(q))
    );
    this.renderReservations(filteredRes);

    // Filter Customers
    const filteredCust = (this.allCustomers || []).filter(c => 
      (c.name && c.name.toLowerCase().includes(q)) ||
      (c.phone && c.phone.toLowerCase().includes(q))
    );
    this.renderCustomers(filteredCust);
  }
};

document.addEventListener('DOMContentLoaded', () => {
  if (document.getElementById('reception')) {
    SmartReception.init();
  }
});

window.SmartReception = SmartReception;
