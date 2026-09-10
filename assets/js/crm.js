/**
 * SMARTRESTA Frontend CRM, Reservations, Loyalty, Coupons & QR Ordering Controller
 * Prompt 14
 */

var SmartCRM = window.SmartCRM || {
  currentTab: 'customers',

  init: function() {
    this.bindEvents();
    this.switchTab(this.currentTab);
  },

  bindEvents: function() {
    // Tab switching inside CRM view
    document.querySelectorAll('.crm-subtab-btn').forEach(btn => {
      btn.addEventListener('click', (e) => {
        const tab = e.target.dataset.crmTab;
        this.switchTab(tab);
      });
    });
  },

  switchTab: function(tabName) {
    this.currentTab = tabName;
    document.querySelectorAll('.crm-subtab-btn').forEach(btn => {
      btn.classList.toggle('active', btn.dataset.crmTab === tabName);
    });
    document.querySelectorAll('.crm-tab-pane').forEach(pane => {
      pane.style.display = pane.id === `crm-pane-${tabName}` ? 'block' : 'none';
    });

    if (tabName === 'customers') this.loadCustomers();
    else if (tabName === 'reservations') this.loadReservations();
    else if (tabName === 'loyalty') this.loadLoyalty();
    else if (tabName === 'coupons') this.loadCoupons();
    else if (tabName === 'qr') this.loadQRTables();
  },

  // ==================== 1. CUSTOMERS ====================
  loadCustomers: function() {
    const search = document.getElementById('crm-customer-search')?.value || '';
    const status = document.getElementById('crm-customer-status-filter')?.value || '';

    const container = document.getElementById('crm-customers-table-tbody');
    if (!container) return;
    container.innerHTML = `<tr><td colspan="8" class="text-center" style="padding:24px;">Loading customer database...</td></tr>`;

    SmartAPI.get(`api/v1/crm/customers.php?search=${encodeURIComponent(search)}&status=${encodeURIComponent(status)}`)
      .then(res => {
        if (!res.success || !res.data || !res.data.customers || res.data.customers.length === 0) {
          container.innerHTML = `<tr><td colspan="8" class="text-center" style="padding:32px; color:#888;">No customers found.</td></tr>`;
          return;
        }

        container.innerHTML = res.data.customers.map(c => `
          <tr>
            <td><strong>${c.customer_code || ('CUST-' + c.id)}</strong></td>
            <td><strong>${c.name}</strong></td>
            <td>${c.phone}</td>
            <td>${c.email || '—'}</td>
            <td><span class="badge ${c.status === 'ACTIVE' ? 'badge-success' : 'badge-danger'}">${c.status}</span></td>
            <td><strong>${parseFloat(c.points_balance || 0).toFixed(2)} pts</strong></td>
            <td><strong>$${parseFloat(c.lifetime_sales || 0).toFixed(2)}</strong></td>
            <td class="text-right">
              <button class="btn btn-sm btn-outline-primary" onclick="SmartCRM.openCustomerProfileModal(${c.id})">Profile</button>
            </td>
          </tr>
        `).join('');
      })
      .catch(err => {
        container.innerHTML = `<tr><td colspan="8" class="text-center text-danger" style="padding:24px;">Unable to load customers. Please try again.</td></tr>`;
      });
  },

  openCreateCustomerModal: function() {
    document.getElementById('modal-create-customer')?.classList.add('active');
  },

  submitCreateCustomer: function() {
    const form = document.getElementById('form-create-customer');
    if (!form) return;

    const data = {
      name: form.name.value,
      phone: form.phone.value,
      email: form.email.value,
      first_name: form.first_name.value,
      last_name: form.last_name.value,
      company_name: form.company_name.value,
      date_of_birth: form.date_of_birth.value,
      gender: form.gender.value,
      notes: form.notes.value
    };

    SmartAPI.post('api/v1/crm/customers.php', data)
    .then(res => {
      if (res.success) {
        alert(res.message);
        document.getElementById('modal-create-customer')?.classList.remove('active');
        form.reset();
        SmartCRM.loadCustomers();
      } else {
        alert("Error: " + res.message);
      }
    }).catch(err => alert("Error: " + err.message));
  },

  openCustomerProfileModal: function(id) {
    const modal = document.getElementById('modal-customer-profile');
    if (!modal) return;
    modal.classList.add('active');

    const content = document.getElementById('customer-profile-content');
    content.innerHTML = `<div class="text-center" style="padding:40px;">Loading customer profile...</div>`;

    SmartAPI.get(`api/v1/crm/profile.php?id=${id}`)
      .then(res => {
        if (!res.success) {
          content.innerHTML = `<div class="alert alert-danger">${res.message}</div>`;
          return;
        }

        const p = res.data;
        const c = p.customer;
        const m = p.metrics;

        content.innerHTML = `
          <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #eee; pb:16px; margin-bottom:20px;">
            <div>
              <h2 style="margin:0; font-size:22px;">${c.name} <small style="color:#666; font-size:14px;">(${c.customer_code || 'CUST-' + c.id})</small></h2>
              <div style="color:#666; font-size:13px; margin-top:4px;">Phone: <strong>${c.phone}</strong> | Email: <strong>${c.email || 'N/A'}</strong> | Registered: ${c.created_at}</div>
            </div>
            <div>
              <span class="badge ${c.status === 'ACTIVE' ? 'badge-success' : 'badge-secondary'}" style="font-size:14px; padding:8px 14px;">${c.status}</span>
            </div>
          </div>

          <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap:12px; margin-bottom:24px;">
            <div class="card" style="padding:16px; text-align:center; background:#f8fafc;">
              <div style="font-size:12px; color:#64748b;">Total Orders</div>
              <div style="font-size:20px; font-weight:700; color:#1e293b;">${m.total_orders}</div>
            </div>
            <div class="card" style="padding:16px; text-align:center; background:#f8fafc;">
              <div style="font-size:12px; color:#64748b;">Lifetime Sales</div>
              <div style="font-size:20px; font-weight:700; color:#059669;">$${parseFloat(m.lifetime_sales).toFixed(2)}</div>
            </div>
            <div class="card" style="padding:16px; text-align:center; background:#f8fafc;">
              <div style="font-size:12px; color:#64748b;">Avg Order Value</div>
              <div style="font-size:20px; font-weight:700; color:#2563eb;">$${parseFloat(m.average_order_value).toFixed(2)}</div>
            </div>
            <div class="card" style="padding:16px; text-align:center; background:#f8fafc;">
              <div style="font-size:12px; color:#64748b;">Loyalty Balance</div>
              <div style="font-size:20px; font-weight:700; color:#d97706;">${parseFloat(m.loyalty_balance).toFixed(2)} pts</div>
            </div>
          </div>

          <div style="margin-bottom:16px; border-bottom:1px solid #e2e8f0; display:flex; gap:16px;">
            <button class="btn btn-sm btn-outline-secondary" onclick="document.querySelectorAll('.prof-section').forEach(s => s.style.display='none'); document.getElementById('prof-orders').style.display='block';">Order History (${p.orders.length})</button>
            <button class="btn btn-sm btn-outline-secondary" onclick="document.querySelectorAll('.prof-section').forEach(s => s.style.display='none'); document.getElementById('prof-visits').style.display='block';">Visits (${p.visits.length})</button>
            <button class="btn btn-sm btn-outline-secondary" onclick="document.querySelectorAll('.prof-section').forEach(s => s.style.display='none'); document.getElementById('prof-loyalty').style.display='block';">Loyalty Ledger (${p.loyalty_ledger.length})</button>
          </div>

          <div id="prof-orders" class="prof-section">
            <h4>Order History</h4>
            <table class="data-table" style="width:100%;">
              <thead>
                <tr><th>Order #</th><th>Date</th><th>Type</th><th>Total</th><th>Status</th></tr>
              </thead>
              <tbody>
                ${p.orders.length === 0 ? '<tr><td colspan="5" class="text-center">No orders found.</td></tr>' : p.orders.map(o => `
                  <tr>
                    <td><strong>${o.order_number}</strong></td>
                    <td>${o.created_at}</td>
                    <td>${o.order_type}</td>
                    <td>$${parseFloat(o.total).toFixed(2)}</td>
                    <td><span class="badge badge-info">${o.order_status}</span></td>
                  </tr>
                `).join('')}
              </tbody>
            </table>
          </div>

          <div id="prof-visits" class="prof-section" style="display:none;">
            <h4>Visit History</h4>
            <table class="data-table" style="width:100%;">
              <thead>
                <tr><th>Session #</th><th>Table</th><th>Guests</th><th>Opened At</th><th>Status</th></tr>
              </thead>
              <tbody>
                ${p.visits.length === 0 ? '<tr><td colspan="5" class="text-center">No visit history found.</td></tr>' : p.visits.map(v => `
                  <tr>
                    <td>#${v.dining_session_id}</td>
                    <td>${v.floor_name} - T${v.table_number}</td>
                    <td>${v.guest_count}</td>
                    <td>${v.opened_at}</td>
                    <td>${v.session_status}</td>
                  </tr>
                `).join('')}
              </tbody>
            </table>
          </div>

          <div id="prof-loyalty" class="prof-section" style="display:none;">
            <h4>Loyalty Points Ledger</h4>
            <table class="data-table" style="width:100%;">
              <thead>
                <tr><th>Date</th><th>Type</th><th>Points</th><th>Before</th><th>After</th><th>Reason</th></tr>
              </thead>
              <tbody>
                ${p.loyalty_ledger.length === 0 ? '<tr><td colspan="6" class="text-center">No loyalty activity found.</td></tr>' : p.loyalty_ledger.map(l => `
                  <tr>
                    <td>${l.created_at}</td>
                    <td><span class="badge ${l.type === 'EARN' ? 'badge-success' : 'badge-warning'}">${l.type}</span></td>
                    <td><strong>${parseFloat(l.points) > 0 ? '+' : ''}${parseFloat(l.points).toFixed(2)}</strong></td>
                    <td>${parseFloat(l.balance_before).toFixed(2)}</td>
                    <td>${parseFloat(l.balance_after).toFixed(2)}</td>
                    <td>${l.reason || '—'}</td>
                  </tr>
                `).join('')}
              </tbody>
            </table>
          </div>
        `;
      });
  },

  // ==================== 2. RESERVATIONS ====================
  loadReservations: function() {
    const container = document.getElementById('crm-reservations-table-tbody');
    if (!container) return;
    container.innerHTML = `<tr><td colspan="8" class="text-center" style="padding:24px;">Loading reservations schedule...</td></tr>`;

    const dateFilter = document.getElementById('crm-res-date-filter')?.value || '';
    const statusFilter = document.getElementById('crm-res-status-filter')?.value || '';

    SmartAPI.get(`api/v1/crm/reservations.php?reservation_date=${encodeURIComponent(dateFilter)}&status=${encodeURIComponent(statusFilter)}`)
      .then(res => {
        if (!res.success || !res.data || !res.data.reservations || res.data.reservations.length === 0) {
          container.innerHTML = `<tr><td colspan="8" class="text-center" style="padding:32px; color:#888;">No reservations found.</td></tr>`;
          return;
        }

        container.innerHTML = res.data.reservations.map(r => `
          <tr>
            <td><strong>${r.reservation_number}</strong></td>
            <td><strong>${r.customer_name}</strong><br><small style="color:#666;">${r.customer_phone}</small></td>
            <td>${r.reservation_date} at <strong>${r.reservation_time ? r.reservation_time.substring(11,16) : '19:00'}</strong></td>
            <td>${r.guest_count} guests (${r.duration_minutes}m)</td>
            <td>${r.floor_name || 'Main'} - <strong>Table ${r.table_number || 'Auto'}</strong></td>
            <td>
              <span class="badge ${r.status === 'CONFIRMED' ? 'badge-primary' : (r.status === 'SEATED' ? 'badge-success' : 'badge-secondary')}">
                ${r.status}
              </span>
            </td>
            <td class="text-right">
              ${r.status === 'CONFIRMED' ? `
                <button class="btn btn-sm btn-success" onclick="SmartCRM.seatReservation(${r.id})">Seat Guest</button>
                <button class="btn btn-sm btn-outline-danger" onclick="SmartCRM.cancelReservation(${r.id})">Cancel</button>
              ` : '—'}
            </td>
          </tr>
        `).join('');
      });
  },

  openCreateReservationModal: function() {
    document.getElementById('modal-create-reservation')?.classList.add('active');
  },

  checkReservationAvailability: function() {
    const date = document.getElementById('res-modal-date').value;
    const time = document.getElementById('res-modal-time').value;
    const guests = document.getElementById('res-modal-guests').value;
    const statusBox = document.getElementById('res-modal-availability-box');

    if (!date || !time) return;

    SmartAPI.get(`api/v1/crm/reservations.php?action=availability&reservation_date=${date}&reservation_time=${time}&guest_count=${guests}`)
      .then(res => {
        if (res.success && res.data.is_available) {
          statusBox.className = 'alert alert-success';
          statusBox.innerHTML = `✓ Table capacity available. (${res.data.available_tables.length} tables eligible).`;
        } else {
          statusBox.className = 'alert alert-danger';
          statusBox.innerHTML = `⚠️ Conflict Detected: Selected time slot is fully booked or occupied.`;
        }
      });
  },

  submitCreateReservation: function() {
    const data = {
      action: 'create',
      customer_name: document.getElementById('res-modal-name').value,
      phone: document.getElementById('res-modal-phone').value,
      reservation_date: document.getElementById('res-modal-date').value,
      reservation_time: document.getElementById('res-modal-time').value,
      guest_count: document.getElementById('res-modal-guests').value,
      duration_minutes: document.getElementById('res-modal-duration').value,
      notes: document.getElementById('res-modal-notes').value
    };

    SmartAPI.post('api/v1/crm/reservations.php', data)
    .then(res => {
      if (res.success) {
        alert(res.message);
        document.getElementById('modal-create-reservation')?.classList.remove('active');
        SmartCRM.loadReservations();
      } else {
        alert("Reservation Error (Conflict): " + res.message);
      }
    });
  },

  seatReservation: function(id) {
    if (!confirm("Seat this guest and convert reservation into active dining session?")) return;

    SmartAPI.post('api/v1/crm/reservations.php', { action: 'seat', reservation_id: id })
    .then(res => {
      if (res.success) {
        alert("Guest seated! Dining session #" + res.data.dining_session_id + " opened on Table.");
        SmartCRM.loadReservations();
      } else {
        alert("Error: " + res.message);
      }
    });
  },

  cancelReservation: function(id) {
    const reason = prompt("Enter cancellation reason:");
    if (reason === null) return;

    SmartAPI.post('api/v1/crm/reservations.php', { action: 'cancel', reservation_id: id, reason: reason })
    .then(res => {
      if (res.success) {
        alert("Reservation cancelled.");
        SmartCRM.loadReservations();
      } else {
        alert("Error: " + res.message);
      }
    });
  },

  // ==================== 3. LOYALTY ====================
  loadLoyalty: function() {
    SmartCRM.loadCustomers(); // Uses customer points balance table
  },

  openAdjustPointsModal: function(customerId) {
    const pts = prompt("Enter points adjustment (+ amount to credit, - amount to deduct):");
    if (!pts) return;
    const reason = prompt("Enter mandatory audit reason for adjustment:");
    if (!reason) return;

    SmartAPI.post('api/v1/crm/loyalty.php', { action: 'adjust', customer_id: customerId, points: parseFloat(pts), reason: reason })
    .then(res => {
      if (res.success) {
        alert("Points adjusted! New balance: " + res.data.balance_after + " pts");
        SmartCRM.loadCustomers();
      } else {
        alert("Error: " + res.message);
      }
    });
  },

  // ==================== 4. COUPONS ====================
  loadCoupons: function() {
    const container = document.getElementById('crm-coupons-table-tbody');
    if (!container) return;
    container.innerHTML = `<tr><td colspan="7" class="text-center" style="padding:24px;">Loading coupons...</td></tr>`;

    SmartAPI.get('api/v1/crm/coupons.php')
      .then(res => {
        if (!res.success || !res.data || !res.data.coupons || res.data.coupons.length === 0) {
          container.innerHTML = `<tr><td colspan="7" class="text-center" style="padding:32px; color:#888;">No coupons available.</td></tr>`;
          return;
        }

        container.innerHTML = res.data.coupons.map(c => `
          <tr>
            <td><strong style="font-size:15px; color:#2563eb;">${c.code}</strong></td>
            <td>${c.name || '—'}</td>
            <td><strong>${c.discount_type === 'PERCENTAGE' ? c.discount_amount + '%' : '$' + parseFloat(c.discount_amount).toFixed(2)}</strong></td>
            <td>Min Spend: $${parseFloat(c.min_order_amount || 0).toFixed(2)}</td>
            <td>Redemptions: <strong>${c.total_redemptions}</strong> / ${c.usage_limit > 0 ? c.usage_limit : '∞'}</td>
            <td><span class="badge ${c.is_active == 1 ? 'badge-success' : 'badge-secondary'}">${c.is_active == 1 ? 'ACTIVE' : 'INACTIVE'}</span></td>
            <td><small style="color:#666;">Until ${c.valid_until ? c.valid_until.substring(0,10) : 'Permanent'}</small></td>
          </tr>
        `).join('');
      });
  },

  openCreateCouponModal: function() {
    document.getElementById('modal-create-coupon')?.classList.add('active');
  },

  submitCreateCoupon: function() {
    const data = {
      action: 'create',
      code: document.getElementById('cpn-modal-code').value,
      name: document.getElementById('cpn-modal-name').value,
      discount_type: document.getElementById('cpn-modal-type').value,
      discount_amount: parseFloat(document.getElementById('cpn-modal-amount').value),
      min_order_amount: parseFloat(document.getElementById('cpn-modal-minspend').value || 0),
      max_discount: parseFloat(document.getElementById('cpn-modal-maxdisc').value || 0),
      usage_limit: parseInt(document.getElementById('cpn-modal-limit').value || 0),
      valid_until: document.getElementById('cpn-modal-until').value
    };

    SmartAPI.post('api/v1/crm/coupons.php', data)
    .then(res => {
      if (res.success) {
        alert("Coupon code created successfully.");
        document.getElementById('modal-create-coupon')?.classList.remove('active');
        SmartCRM.loadCoupons();
      } else {
        alert("Error: " + res.message);
      }
    });
  },

  // ==================== 5. QR TABLE TOKENS ====================
  loadQRTables: function() {
    const container = document.getElementById('crm-qr-table-tbody');
    if (!container) return;
    container.innerHTML = `<tr><td colspan="6" class="text-center" style="padding:24px;">Loading QR ordering tables...</td></tr>`;

    SmartAPI.get('api/v1/crm/qr.php')
      .then(res => {
        if (!res.success || !res.data || !res.data.tables || res.data.tables.length === 0) {
          container.innerHTML = `<tr><td colspan="6" class="text-center" style="padding:32px; color:#888;">No QR tables configured.</td></tr>`;
          return;
        }

        container.innerHTML = res.data.tables.map(t => {
          const hasToken = t.token && t.token_status === 'ACTIVE';
          const qrUrl = window.location.origin + `/public/qr.html?token=${t.token || ''}`;

          return `
            <tr>
              <td><strong>${t.floor_name || 'Main Zone'}</strong></td>
              <td><strong>Table ${t.table_number}</strong> (${t.capacity} seats)</td>
              <td>
                <span class="badge ${hasToken ? 'badge-success' : 'badge-secondary'}">
                  ${hasToken ? 'ACTIVE TOKEN' : 'NO TOKEN'}
                </span>
              </td>
              <td><small style="color:#666;">${hasToken ? t.updated_at || t.created_at : 'Never'}</small></td>
              <td>
                ${hasToken ? `
                  <a href="${qrUrl}" target="_blank" class="btn btn-sm btn-outline-primary" style="text-decoration:none;">Open Public QR Page</a>
                  <button class="btn btn-sm btn-outline-secondary" onclick="navigator.clipboard.writeText('${qrUrl}'); alert('Public QR Link copied to clipboard!');">Copy Link</button>
                ` : '—'}
              </td>
              <td class="text-right">
                ${hasToken ? `
                  <button class="btn btn-sm btn-warning" onclick="SmartCRM.regenerateQRToken(${t.table_id})">Regenerate</button>
                  <button class="btn btn-sm btn-danger" onclick="SmartCRM.revokeQRToken(${t.token_id})">Revoke</button>
                ` : `
                  <button class="btn btn-sm btn-success" onclick="SmartCRM.generateQRToken(${t.table_id})">Generate Secure Token</button>
                `}
              </td>
            </tr>
          `;
        }).join('');
      });
  },

  generateQRToken: function(tableId) {
    SmartAPI.post('api/v1/crm/qr.php', { action: 'generate', table_id: tableId })
    .then(res => {
      if (res.success) {
        SmartCRM.loadQRTables();
      } else {
        alert("Error: " + res.message);
      }
    });
  },

  regenerateQRToken: function(tableId) {
    if (!confirm("Regenerating this QR token will revoke the old code. Proceed?")) return;
    SmartAPI.post('api/v1/crm/qr.php', { action: 'regenerate', table_id: tableId })
    .then(res => {
      if (res.success) {
        SmartCRM.loadQRTables();
      } else {
        alert("Error: " + res.message);
      }
    });
  },

  revokeQRToken: function(tokenId) {
    if (!confirm("Revoke public access for this table QR code?")) return;
    SmartAPI.post('api/v1/crm/qr.php', { action: 'revoke', token_id: tokenId })
    .then(res => {
      if (res.success) {
        SmartCRM.loadQRTables();
      } else {
        alert("Error: " + res.message);
      }
    });
  }
};

document.addEventListener('DOMContentLoaded', () => {
  if (document.getElementById('crm') || document.getElementById('crm-view')) {
    SmartCRM.init();
  }
});

window.SmartCRM = SmartCRM;
