/**
 * SMARTRESTA — Frontend Waiter Desk & Table Ordering Controller
 * Module: SmartWaiter
 */

const SmartWaiter = {
  activeTab: 'pos',
  allTables: [],
  allSessions: [],
  allOrders: [],
  kitchenTickets: [],

  async init() {
    this.bindEvents();
    await this.refreshAll();
  },

  bindEvents() {
    const searchInput = document.getElementById('waiter-global-search');
    if (searchInput) {
      searchInput.addEventListener('input', (e) => this.filterGlobal(e.target.value));
    }
  },

  async refreshAll() {
    try {
      await Promise.all([
        this.loadWaiterKPIs(),
        this.loadPOSTableSelector(),
        this.loadPOSCustomers(),
        this.loadFloorMap(),
        this.loadDiningSessions(),
        this.loadKitchenFeed(),
        this.loadMyOrders(),
        this.loadMyCommission()
      ]);
    } catch (err) {
      console.warn('SmartWaiter refresh error:', err);
    }
  },

  switchTab(tabName, btnEl) {
    this.activeTab = tabName;

    document.querySelectorAll('.waiter-tab-btn').forEach(b => b.classList.remove('active'));
    if (btnEl) {
      btnEl.classList.add('active');
    } else {
      const targetBtn = document.querySelector(`.waiter-tab-btn[data-tab="${tabName}"]`);
      if (targetBtn) targetBtn.classList.add('active');
    }

    document.querySelectorAll('.waiter-tab-pane').forEach(p => p.style.display = 'none');
    const pane = document.getElementById(`waiter-pane-${tabName}`);
    if (pane) pane.style.display = 'block';

    if (tabName === 'pos') {
      if (typeof loadPOSProducts === 'function') loadPOSProducts();
    } else if (tabName === 'floors') {
      this.loadFloorMap();
    } else if (tabName === 'sessions') {
      this.loadDiningSessions();
    } else if (tabName === 'kitchen') {
      this.loadKitchenFeed();
    } else if (tabName === 'orders') {
      this.loadMyOrders();
    } else if (tabName === 'performance') {
      this.loadMyCommission();
    }
  },

  async loadWaiterKPIs() {
    try {
      const [tblRes, ordRes, kdsRes] = await Promise.allSettled([
        SmartAPI.get('api/v1/tables/index.php'),
        SmartAPI.get('api/v1/orders/index.php'),
        SmartAPI.get('api/v1/kds/index.php')
      ]);

      // Active Tables & Sessions
      if (tblRes.status === 'fulfilled' && tblRes.value.success && tblRes.value.data) {
        const tables = tblRes.value.data;
        const total = tables.length;
        const occupied = tables.filter(t => (t.status || '').toUpperCase() === 'OCCUPIED').length;
        
        const tblCountEl = document.getElementById('wtr-kpi-tables-count');
        if (tblCountEl) tblCountEl.textContent = `${occupied} / ${total}`;

        const sessCountEl = document.getElementById('wtr-kpi-sessions-count');
        if (sessCountEl) sessCountEl.textContent = occupied;
      }

      // Active Orders & Sales Today
      if (ordRes.status === 'fulfilled' && ordRes.value.success && ordRes.value.data) {
        const orders = ordRes.value.data;
        const active = orders.filter(o => !['COMPLETED', 'CANCELLED', 'REFUNDED'].includes((o.order_status || '').toUpperCase()));
        
        const ordCountEl = document.getElementById('wtr-kpi-orders-count');
        if (ordCountEl) ordCountEl.textContent = active.length;

        let todaySales = 0;
        orders.forEach(o => {
          if ((o.payment_status || '').toUpperCase() === 'PAID') {
            todaySales += parseFloat(o.total || o.grand_total || 0);
          }
        });

        const salesEl = document.getElementById('wtr-kpi-today-sales');
        if (salesEl) salesEl.textContent = `৳${todaySales.toFixed(2)}`;

        const commEst = todaySales * 0.05; // 5% standard rate
        const commEl = document.getElementById('wtr-kpi-today-comm');
        if (commEl) commEl.textContent = `৳${commEst.toFixed(2)} Commission`;
      }

      // Kitchen Ticket Status
      if (kdsRes.status === 'fulfilled' && kdsRes.value.success && kdsRes.value.data) {
        const tickets = kdsRes.value.data;
        const prep = tickets.filter(t => (t.status || '').toUpperCase() === 'PREPARING').length;
        const ready = tickets.filter(t => (t.status || '').toUpperCase() === 'READY').length;

        const kdsStatusEl = document.getElementById('wtr-kpi-kitchen-status');
        if (kdsStatusEl) kdsStatusEl.textContent = `${prep} Prep / ${ready} Ready`;
      }

    } catch (err) {
      console.warn('Error loading waiter KPIs:', err);
    }
  },

  async loadPOSTableSelector() {
    const selector = document.getElementById('wtr-pos-table-selector');
    if (!selector) return;

    try {
      const res = await SmartAPI.get('api/v1/tables/index.php');
      if (res.success && res.data) {
        this.allTables = res.data;
        selector.innerHTML = `<option value="">— Choose Table —</option>` + res.data.map(t => `
          <option value="${t.id}" data-number="${t.table_number}" data-status="${t.status}">
            Table ${t.table_number} (${t.floor_name || 'Main'} - ${t.status})
          </option>
        `).join('');
      }
    } catch (e) {
      console.warn('Failed to load table selector:', e);
    }
  },

  async loadPOSCustomers() {
    const selector = document.getElementById('wtr-pos-customer-select');
    if (!selector) return;

    try {
      const res = await SmartAPI.get('api/v1/crm/index.php');
      if (res.success && res.data) {
        selector.innerHTML = `<option value="">— Walk-in / Guest —</option>` + res.data.map(c => `
          <option value="${c.id}">${c.name} (${c.phone || 'No Phone'}) ${c.is_vip ? '⭐ VIP' : ''}</option>
        `).join('');
      }
    } catch (e) {
      console.warn('Failed to load customer dropdown:', e);
    }
  },

  onTableSelect(selectEl) {
    if (!selectEl) return;
    const tableId = selectEl.value;
    if (!tableId) {
      this.setTakeawayMode();
      return;
    }
    const option = selectEl.options[selectEl.selectedIndex];
    const tableNum = option ? option.getAttribute('data-number') : tableId;

    if (typeof SmartPOS !== 'undefined') {
      SmartPOS.setTableAndSession(tableId, tableNum, null);
    }

    const heading = document.getElementById('wtr-pos-table-heading');
    if (heading) {
      heading.textContent = `Table #${tableNum} Selected`;
      heading.className = 'badge badge-success';
    }

    const cartHeading = document.getElementById('wtr-cart-heading');
    if (cartHeading) {
      cartHeading.textContent = `Order — Table #${tableNum}`;
    }
  },

  setTakeawayMode() {
    const selector = document.getElementById('wtr-pos-table-selector');
    if (selector) selector.value = '';
    if (typeof SmartPOS !== 'undefined') {
      SmartPOS.selectTakeaway();
    }
    const heading = document.getElementById('wtr-pos-table-heading');
    if (heading) {
      heading.textContent = 'Takeaway / Walk-in Mode';
      heading.className = 'badge badge-info';
    }
    const cartHeading = document.getElementById('wtr-cart-heading');
    if (cartHeading) {
      cartHeading.textContent = 'Takeaway Order';
    }
  },

  filterCategory(catId, btnEl) {
    if (typeof filterPOSCategory === 'function') {
      filterPOSCategory(catId, btnEl);
    }
  },

  async submitOrder(routingDestination = 'KITCHEN') {
    if (typeof SmartPOS === 'undefined' || SmartPOS.cart.length === 0) {
      SmartNotifications.show('Please add items to cart before submitting order', 'warning');
      return;
    }

    const tableId = document.getElementById('wtr-pos-table-selector')?.value || null;
    const customerId = document.getElementById('wtr-pos-customer-select')?.value || null;
    const notes = document.getElementById('wtr-pos-order-notes')?.value || '';
    const orderType = tableId ? 'DINE_IN' : 'TAKEAWAY';

    try {
      // 1. Create Draft Order via OrderEngine
      const draftRes = await SmartAPI.post('api/v1/orders/index.php', {
        order_type: orderType,
        table_id: tableId,
        customer_id: customerId,
        notes: notes
      });

      if (!draftRes.success || !draftRes.data || !draftRes.data.order_id) {
        throw new Error(draftRes.message || 'Failed to initialize draft order');
      }

      const orderId = draftRes.data.order_id;

      // 2. Add Line Items with Variants & Modifiers
      for (const item of SmartPOS.cart) {
        await SmartAPI.post('api/v1/orders/items.php', {
          order_id: orderId,
          product_id: item.product_id,
          variant_id: item.variant_id || null,
          modifier_ids: item.modifiers ? item.modifiers.map(m => m.id) : [],
          quantity: item.qty
        });
      }

      // 3. Dispatch according to routing choice (Kitchen Direct vs Reception Pay-First)
      const submitRes = await SmartAPI.post('api/v1/orders/submit.php', { order_id: orderId });

      if (!submitRes.success) {
        throw new Error(submitRes.message || 'Failed to submit order');
      }

      const orderNum = submitRes.data ? submitRes.data.order_number : orderId;

      if (routingDestination === 'RECEPTION') {
        // Pay-First Flow -> Update status to WAITING_PAYMENT
        await SmartAPI.post('api/v1/orders/status.php', {
          order_id: orderId,
          status: 'WAITING_PAYMENT',
          notes: 'Pay-First Routing requested by Waiter'
        });

        SmartNotifications.show(`Order #${orderNum} created & sent to Reception for Pay-First settlement!`, 'info');
        
        // Trigger Billing settlement modal
        if (typeof SmartBilling !== 'undefined') {
          setTimeout(() => SmartBilling.openBillingModal(orderId), 300);
        }
      } else {
        // Direct Kitchen Flow -> Route directly to Kitchen Stations
        SmartNotifications.show(`Order #${orderNum} successfully placed & routed directly to Kitchen!`, 'success');
      }

      // Clear POS cart and reset notes
      SmartPOS.clearCart();
      const notesInput = document.getElementById('wtr-pos-order-notes');
      if (notesInput) notesInput.value = '';

      this.refreshAll();

    } catch (err) {
      SmartNotifications.show(err.message || 'Failed to submit waiter order', 'danger');
    }
  },

  async loadFloorMap() {
    const container = document.getElementById('wtr-floor-grid');
    if (!container) return;

    try {
      const res = await SmartAPI.get('api/v1/tables/index.php');
      if (res.success && res.data) {
        this.allTables = res.data;
        this.renderFloorGrid(this.allTables);
      } else {
        container.innerHTML = `<div style="grid-column:1/-1; text-align:center; padding:40px; color:var(--text-muted);">No tables defined on floor map.</div>`;
      }
    } catch (err) {
      container.innerHTML = `<div style="grid-column:1/-1; text-align:center; padding:40px; color:var(--danger);">Failed to load floor tables.</div>`;
    }
  },

  renderFloorGrid(tables) {
    const container = document.getElementById('wtr-floor-grid');
    if (!container) return;

    if (!tables || tables.length === 0) {
      container.innerHTML = `<div style="grid-column:1/-1; text-align:center; padding:40px; color:var(--text-muted);">No floor tables found.</div>`;
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
              <button class="btn btn-primary btn-sm" style="width:100%;" onclick="SmartWaiter.openSessionForTable(${t.id})">
                ➕ Seat & Open Session
              </button>
            ` : (st === 'OCCUPIED' ? `
              <button class="btn btn-warning btn-sm" style="flex:1;" onclick="SmartWaiter.selectPOSTable(${t.id}, '${t.table_number}')">
                🛒 Take Order
              </button>
              <button class="btn btn-secondary btn-sm" onclick="SmartWaiter.settleTable(${t.id})" title="Settle Payment">
                💳
              </button>
            ` : `
              <button class="btn btn-secondary btn-sm" style="width:100%;" onclick="SmartWaiter.openSessionForTable(${t.id})">
                Open Session
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

  selectPOSTable(tableId, tableNum) {
    const selector = document.getElementById('wtr-pos-table-selector');
    if (selector) {
      selector.value = tableId;
      this.onTableSelect(selector);
    }
    this.switchTab('pos');
  },

  async settleTable(tableId) {
    try {
      const res = await SmartAPI.get('api/v1/orders/index.php');
      if (res.success && res.data) {
        const order = res.data.find(o => parseInt(o.table_id, 10) === parseInt(tableId, 10) && o.payment_status !== 'PAID');
        if (order && typeof SmartBilling !== 'undefined') {
          SmartBilling.openBillingModal(order.id);
        } else {
          SmartNotifications.show(`No active unpaid order found for Table #${tableId}`, 'info');
        }
      }
    } catch (e) {
      SmartNotifications.show('Failed to find active order for table', 'danger');
    }
  },

  async loadDiningSessions() {
    const tbody = document.getElementById('wtr-sessions-tbody');
    if (!tbody) return;

    try {
      const [sessRes, ordRes] = await Promise.allSettled([
        SmartAPI.get('api/v1/tables/index.php'),
        SmartAPI.get('api/v1/orders/index.php')
      ]);

      if (sessRes.status === 'fulfilled' && sessRes.value.success && sessRes.value.data) {
        const tables = sessRes.value.data.filter(t => (t.status || '').toUpperCase() === 'OCCUPIED');
        const orders = (ordRes.status === 'fulfilled' && ordRes.value.success) ? ordRes.value.data : [];

        if (tables.length === 0) {
          tbody.innerHTML = `<tr><td colspan="8" style="text-align:center; padding:30px; color:var(--text-muted);">No open dining sessions on floor.</td></tr>`;
          return;
        }

        tbody.innerHTML = tables.map(t => {
          const tableOrder = orders.find(o => parseInt(o.table_id, 10) === parseInt(t.id, 10) && o.payment_status !== 'PAID');
          const grandTotal = tableOrder ? parseFloat(tableOrder.total || tableOrder.grand_total || 0) : 0;

          return `
            <tr>
              <td><span class="font-mono">#DS-TBL-${t.id}</span></td>
              <td><strong>Table ${t.table_number}</strong> <small>(${t.floor_name || 'Main'})</small></td>
              <td>👥 ${t.seating_capacity} Guests</td>
              <td>${t.updated_at || 'Recently Opened'}</td>
              <td>${tableOrder ? `<span class="font-mono">#${tableOrder.order_number}</span>` : '<span class="text-muted">No Order Yet</span>'}</td>
              <td class="price-tag">৳${grandTotal.toFixed(2)}</td>
              <td><span class="badge badge-warning">OPEN</span></td>
              <td>
                <div style="display:flex; gap:6px;">
                  <button class="btn btn-primary btn-sm" onclick="SmartWaiter.selectPOSTable(${t.id}, '${t.table_number}')">🛒 Add Items</button>
                  ${tableOrder ? `<button class="btn btn-secondary btn-sm" onclick="SmartBilling.openBillingModal(${tableOrder.id})">💳 Settle</button>` : ''}
                </div>
              </td>
            </tr>
          `;
        }).join('');

      } else {
        tbody.innerHTML = `<tr><td colspan="8" style="text-align:center; padding:30px; color:var(--text-muted);">No active sessions found.</td></tr>`;
      }
    } catch (err) {
      tbody.innerHTML = `<tr><td colspan="8" style="text-align:center; padding:30px; color:var(--danger);">Failed to load dining sessions.</td></tr>`;
    }
  },

  async loadKitchenFeed() {
    const grid = document.getElementById('wtr-kitchen-tickets-grid');
    const banner = document.getElementById('wtr-ready-orders-banner');
    if (!grid) return;

    try {
      const res = await SmartAPI.get('api/v1/kds/index.php');
      if (res.success && res.data) {
        this.kitchenTickets = res.data;

        // Highlight READY Tickets Banner
        const readyTickets = res.data.filter(t => (t.status || '').toUpperCase() === 'READY');
        if (banner) {
          if (readyTickets.length > 0) {
            banner.style.display = 'block';
            banner.innerHTML = `
              <div style="background:#ecfdf5; border:2px solid #10b981; border-radius:12px; padding:16px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; box-shadow:0 4px 12px rgba(16, 185, 129, 0.15);">
                <div>
                  <h4 style="margin:0 0 4px 0; color:#065f46; font-size:1.1rem;">🔔 ${readyTickets.length} ORDER(S) READY FOR PICKUP & SERVING!</h4>
                  <p class="text-sm" style="margin:0; color:#047857;">Kitchen has completed preparation. Please deliver to table immediately.</p>
                </div>
                <div style="display:flex; gap:8px;">
                  ${readyTickets.map(t => `
                    <button class="btn btn-success btn-sm" onclick="SmartWaiter.markOrderServed(${t.order_id})">
                      ✅ Serve Order #${t.order_number || t.order_id} (Table ${t.table_number || 'N/A'})
                    </button>
                  `).join('')}
                </div>
              </div>
            `;
          } else {
            banner.style.display = 'none';
          }
        }

        // Render Kitchen Tickets Grid
        if (res.data.length === 0) {
          grid.innerHTML = `<div style="grid-column:1/-1; text-align:center; padding:40px; color:var(--text-muted);">No active tickets in kitchen display queue.</div>`;
          return;
        }

        grid.innerHTML = res.data.map(t => {
          const st = (t.status || 'PENDING').toUpperCase();
          let badgeClass = 'warning';
          let borderLeft = '4px solid var(--warning)';

          if (st === 'PREPARING') {
            badgeClass = 'info';
            borderLeft = '4px solid var(--info)';
          } else if (st === 'READY') {
            badgeClass = 'success';
            borderLeft = '4px solid var(--success)';
          } else if (st === 'SERVED') {
            badgeClass = 'neutral';
            borderLeft = '4px solid var(--text-muted)';
          }

          return `
            <div class="card" style="border-left:${borderLeft}; display:flex; flex-direction:column; justify-content:space-between; gap:10px;">
              <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                <div>
                  <div style="font-weight:700; font-size:1.05rem;">Order #${t.order_number || t.order_id}</div>
                  <div class="text-sm" style="color:var(--text-muted);">
                    ${t.table_number ? `🪑 Table <strong>${t.table_number}</strong>` : '🥡 Takeaway'}
                    • Station: <strong>${t.station_name || 'Kitchen'}</strong>
                  </div>
                </div>
                <span class="badge badge-${badgeClass}">${st}</span>
              </div>

              <div style="background:var(--surface-hover); padding:10px; border-radius:8px; font-size:0.9rem;">
                <div>Item: <strong>${t.item_name}</strong> ${t.variant_name ? `<small>(${t.variant_name})</small>` : ''}</div>
                <div style="color:var(--text-muted); font-size:0.8rem;">Qty: ${t.quantity} ${t.notes ? `• Note: ${t.notes}` : ''}</div>
              </div>

              <div style="display:flex; justify-content:space-between; align-items:center;">
                <span class="text-sm" style="color:var(--text-muted);">⏱️ ${t.elapsed_time || 'Just now'}</span>
                ${st === 'READY' ? `
                  <button class="btn btn-success btn-sm" onclick="SmartWaiter.markOrderServed(${t.order_id})">
                    ✅ Mark Served
                  </button>
                ` : ''}
              </div>
            </div>
          `;
        }).join('');

      } else {
        grid.innerHTML = `<div style="grid-column:1/-1; text-align:center; padding:40px; color:var(--text-muted);">No active tickets.</div>`;
      }
    } catch (err) {
      grid.innerHTML = `<div style="grid-column:1/-1; text-align:center; padding:40px; color:var(--danger);">Failed to load kitchen status.</div>`;
    }
  },

  async markOrderServed(orderId) {
    try {
      const res = await SmartAPI.post('api/v1/orders/status.php', {
        order_id: orderId,
        status: 'SERVED',
        notes: 'Order marked served by Waiter'
      });

      if (res.success) {
        SmartNotifications.show(`Order #${orderId} marked as SERVED!`, 'success');
        this.loadKitchenFeed();
        this.loadWaiterKPIs();
        this.loadMyOrders();
      }
    } catch (err) {
      SmartNotifications.show(err.message || 'Failed to update order status', 'danger');
    }
  },

  async loadMyOrders() {
    const tbody = document.getElementById('wtr-orders-tbody');
    if (!tbody) return;

    try {
      const res = await SmartAPI.get('api/v1/orders/index.php');
      if (res.success && res.data) {
        this.allOrders = res.data;
        this.renderMyOrders(this.allOrders);
      } else {
        tbody.innerHTML = `<tr><td colspan="9" style="text-align:center; padding:40px; color:var(--text-muted);">No orders recorded.</td></tr>`;
      }
    } catch (err) {
      tbody.innerHTML = `<tr><td colspan="9" style="text-align:center; padding:40px; color:var(--danger);">Failed to load orders history.</td></tr>`;
    }
  },

  renderMyOrders(orders) {
    const tbody = document.getElementById('wtr-orders-tbody');
    if (!tbody) return;

    if (!orders || orders.length === 0) {
      tbody.innerHTML = `<tr><td colspan="9" style="text-align:center; padding:40px; color:var(--text-muted);">No matching orders found.</td></tr>`;
      return;
    }

    tbody.innerHTML = orders.map(o => `
      <tr>
        <td><span class="font-mono">#${o.order_number || o.id}</span></td>
        <td>${o.table_number ? `Table ${o.table_number}` : 'Takeaway'}</td>
        <td><span class="badge badge-neutral">${o.order_type || 'DINE_IN'}</span></td>
        <td>${o.item_count || 1} Items</td>
        <td class="price-tag">৳${parseFloat(o.total || o.grand_total || 0).toFixed(2)}</td>
        <td><span class="badge badge-${o.payment_status === 'PAID' ? 'success' : 'warning'}">${o.payment_status || 'UNPAID'}</span></td>
        <td><span class="badge badge-info">${o.order_status || 'DRAFT'}</span></td>
        <td><small class="text-muted">${o.created_at || 'Today'}</small></td>
        <td>
          <button class="btn btn-secondary btn-sm" onclick="SmartBilling.openBillingModal(${o.id})">Details / Bill</button>
        </td>
      </tr>
    `).join('');
  },

  filterOrders() {
    const statusVal = (document.getElementById('wtr-orders-status-filter')?.value || '').toUpperCase().trim();
    if (!statusVal) {
      this.renderMyOrders(this.allOrders);
      return;
    }
    const filtered = (this.allOrders || []).filter(o => (o.order_status || '').toUpperCase() === statusVal);
    this.renderMyOrders(filtered);
  },

  async loadMyCommission() {
    try {
      const res = await SmartAPI.get('api/v1/waiters/index.php');
      if (res.success && res.data) {
        this.allCommissionData = res.data;
        
        // Calculate totals for matrix cards
        let totalSales = 0;
        let paidSales = 0;
        let totalOrders = 0;
        let totalComm = 0;

        res.data.forEach(w => {
          totalSales += parseFloat(w.total_sales || 0);
          paidSales += parseFloat(w.paid_sales || 0);
          totalOrders += parseInt(w.total_orders || 0, 10);
          totalComm += parseFloat(w.total_commission || 0);
        });

        const salesEl = document.getElementById('wtr-matrix-total-sales');
        if (salesEl) salesEl.textContent = `৳${totalSales.toFixed(2)}`;

        const paidSalesEl = document.getElementById('wtr-matrix-paid-sales');
        if (paidSalesEl) paidSalesEl.textContent = `৳${paidSales.toFixed(2)} Paid`;

        const ordsEl = document.getElementById('wtr-matrix-total-orders');
        if (ordsEl) ordsEl.textContent = totalOrders;

        const avgVal = totalOrders > 0 ? (totalSales / totalOrders) : 0;
        const avgEl = document.getElementById('wtr-matrix-avg-order');
        if (avgEl) avgEl.textContent = `৳${avgVal.toFixed(2)} Avg Order`;

        const commEl = document.getElementById('wtr-matrix-total-comm');
        if (commEl) commEl.textContent = `৳${totalComm.toFixed(2)}`;

        const commApprovedEl = document.getElementById('wtr-matrix-approved-comm');
        if (commApprovedEl) commApprovedEl.textContent = `৳${(totalComm * 0.8).toFixed(2)} Approved`;

        // Render Commission Ledger Table
        const tbody = document.getElementById('wtr-commission-tbody');
        if (tbody) {
          tbody.innerHTML = res.data.map((w, idx) => `
            <tr>
              <td><span class="font-mono">#COMM-${w.profile_id || (idx + 1)}</span></td>
              <td><span class="font-mono">Waitstaff ${w.waiter_name}</span></td>
              <td><span class="badge badge-neutral">Standard 5% Rule</span></td>
              <td>৳${parseFloat(w.total_sales || 0).toFixed(2)}</td>
              <td class="price-tag" style="color:var(--success);">৳${parseFloat(w.total_commission || 0).toFixed(2)}</td>
              <td><span class="badge badge-success">APPROVED</span></td>
              <td><small class="text-muted">Today</small></td>
            </tr>
          `).join('');
        }

      }
    } catch (err) {
      console.warn('Error loading commission matrix:', err);
    }
  },

  filterGlobal(query) {
    const q = (query || '').toLowerCase().trim();
    if (!q) {
      this.renderFloorGrid(this.allTables);
      this.renderMyOrders(this.allOrders);
      return;
    }

    const filteredTables = (this.allTables || []).filter(t => 
      t.table_number.toString().toLowerCase().includes(q) ||
      (t.floor_name && t.floor_name.toLowerCase().includes(q))
    );
    this.renderFloorGrid(filteredTables);

    const filteredOrders = (this.allOrders || []).filter(o => 
      (o.order_number && o.order_number.toString().toLowerCase().includes(q)) ||
      (o.table_number && o.table_number.toString().toLowerCase().includes(q))
    );
    this.renderMyOrders(filteredOrders);
  }
};

document.addEventListener('DOMContentLoaded', () => {
  if (document.getElementById('waiter')) {
    SmartWaiter.init();
  }
});

window.SmartWaiter = SmartWaiter;
