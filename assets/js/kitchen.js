/**
 * SMARTRESTA — Frontend Kitchen Display System (KDS) Controller
 * Module: SmartKitchen
 */

var SmartKitchen = window.SmartKitchen || {
  activeTab: 'board',
  currentStationId: 0,
  allStations: [],
  allTickets: [],
  pollingTimer: null,
  tickerTimer: null,

  async init() {
    this.bindEvents();
    await this.refreshAll();
    this.startPolling();
    this.startElapsedTicker();
  },

  bindEvents() {
    const searchInput = document.getElementById('kitchen-global-search');
    if (searchInput) {
      searchInput.addEventListener('input', (e) => this.filterGlobal(e.target.value));
    }
  },

  async refreshAll() {
    try {
      if (typeof SmartKDS !== 'undefined') {
        await SmartKDS.loadStations();
        await SmartKDS.loadKDSGrid();
        this.allStations = SmartKDS.stations || [];
      }
      await this.loadSLADelayedTickets();
      await this.loadStationQueues();
      await this.loadTicketHistory();
      await this.loadPerformanceData();
    } catch (err) {
      console.warn('SmartKitchen refresh error:', err);
    }
  },

  switchTab(tabName, btnEl) {
    this.activeTab = tabName;

    document.querySelectorAll('.kitchen-tab-btn').forEach(b => b.classList.remove('active'));
    if (btnEl) {
      btnEl.classList.add('active');
    } else {
      const targetBtn = document.querySelector(`.kitchen-tab-btn[data-tab="${tabName}"]`);
      if (targetBtn) targetBtn.classList.add('active');
    }

    document.querySelectorAll('.kitchen-tab-pane').forEach(p => p.style.display = 'none');
    const pane = document.getElementById(`kitchen-pane-${tabName}`);
    if (pane) pane.style.display = 'block';

    if (tabName === 'board') {
      if (typeof SmartKDS !== 'undefined') SmartKDS.loadKDSGrid();
    } else if (tabName === 'sla') {
      this.loadSLADelayedTickets();
    } else if (tabName === 'stations') {
      this.loadStationQueues();
    } else if (tabName === 'history') {
      this.loadTicketHistory();
    } else if (tabName === 'performance') {
      this.loadPerformanceData();
    }
  },

  async loadSLADelayedTickets() {
    const container = document.getElementById('ktc-sla-tickets-container');
    if (!container) return;

    try {
      const res = await SmartAPI.get('api/v1/kds/tickets.php');
      if (res.success && res.data && res.data.tickets) {
        this.allTickets = res.data.tickets;
        this.updateKPIs(res.data.counters, this.allTickets);

        // Filter tickets waiting or preparing for > 15 minutes (900 seconds)
        const nowSec = Math.floor(Date.now() / 1000);
        const delayed = this.allTickets.filter(t => {
          if (['SERVED', 'CANCELLED'].includes((t.status || '').toUpperCase())) return false;
          const createdSec = Math.floor(new Date(t.created_at).getTime() / 1000);
          return (nowSec - createdSec) > 900; // 15 mins SLA threshold
        });

        const delayedCntEl = document.getElementById('ktc-kpi-delayed-count');
        if (delayedCntEl) delayedCntEl.textContent = delayed.length;

        if (delayed.length === 0) {
          container.innerHTML = `<div style="grid-column:1/-1; text-align:center; padding:40px; color:var(--success); background:var(--surface); border:1px solid var(--border); border-radius:12px;">🎉 All active kitchen tickets are operating within SLA targets!</div>`;
          return;
        }

        container.innerHTML = delayed.map(t => {
          const createdSec = Math.floor(new Date(t.created_at).getTime() / 1000);
          const diffSec = Math.max(0, nowSec - createdSec);
          const mins = Math.floor(diffSec / 60);

          return `
            <div class="card" style="border-left:4px solid var(--danger); box-shadow:var(--shadow-sm); display:flex; flex-direction:column; justify-content:space-between; gap:12px;">
              <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                <div>
                  <div style="font-weight:700; font-size:1.1rem; color:var(--danger);">Ticket #${t.ticket_number || t.id}</div>
                  <div class="text-sm" style="color:var(--text-muted);">
                    Order #${t.order_number} ${t.table_number ? `• Table ${t.table_number}` : '• Takeaway'}
                  </div>
                </div>
                <span class="badge badge-danger">🚨 ${mins}m Delayed</span>
              </div>

              <div style="background:var(--surface-hover); padding:10px; border-radius:8px;">
                <div style="font-size:0.85rem; color:var(--text-muted); margin-bottom:4px;">Station: <strong>${t.station_name || 'Kitchen'}</strong></div>
                ${(t.items || []).map(i => `<div style="font-weight:600; font-size:0.9rem;">• ${i.quantity}× ${i.product_name} ${i.variant_name ? `<small>(${i.variant_name})</small>` : ''}</div>`).join('')}
              </div>

              <div style="display:flex; gap:8px;">
                ${t.status === 'NEW' ? `
                  <button class="btn btn-primary btn-sm" style="flex:1;" onclick="SmartKDS.updateTicketStatus(${t.id}, 'PREPARING')">▶ Start Prep</button>
                ` : (t.status === 'PREPARING' ? `
                  <button class="btn btn-success btn-sm" style="flex:1;" onclick="SmartKDS.updateTicketStatus(${t.id}, 'READY')">✓ Mark Ready</button>
                ` : `
                  <button class="btn btn-primary btn-sm" style="flex:1;" onclick="SmartKDS.updateTicketStatus(${t.id}, 'SERVED')">🛎️ Mark Served</button>
                `)}
                <button class="btn btn-warning btn-sm" onclick="SmartKDS.openRefireModal(${t.id}, '${t.ticket_number}')">🔥 Re-fire</button>
              </div>
            </div>
          `;
        }).join('');

      } else {
        container.innerHTML = `<div style="grid-column:1/-1; text-align:center; padding:40px; color:var(--text-muted);">No active tickets.</div>`;
      }
    } catch (err) {
      container.innerHTML = `<div style="grid-column:1/-1; text-align:center; padding:40px; color:var(--danger);">Failed to load delayed tickets.</div>`;
    }
  },

  updateKPIs(counters = {}, tickets = []) {
    const c = counters || {};
    const elNew = document.getElementById('ktc-kpi-new-count');
    const elPrep = document.getElementById('ktc-kpi-prep-count');
    const elReady = document.getElementById('ktc-kpi-ready-count');
    const elServed = document.getElementById('ktc-kpi-served-count');

    if (elNew) elNew.textContent = c.count_new || 0;
    if (elPrep) elPrep.textContent = c.count_preparing || 0;
    if (elReady) elReady.textContent = c.count_ready || 0;
    if (elServed) elServed.textContent = c.count_served || 0;

    // Kanban Badge Counters
    const bNew = document.getElementById('ktc-col-badge-new');
    const bPrep = document.getElementById('ktc-col-badge-prep');
    const bReady = document.getElementById('ktc-col-badge-ready');
    const bServed = document.getElementById('ktc-col-badge-served');

    if (bNew) bNew.textContent = `${c.count_new || 0} Received`;
    if (bPrep) bPrep.textContent = `${c.count_preparing || 0} Cooking`;
    if (bReady) bReady.textContent = `${c.count_ready || 0} Pickup`;
    if (bServed) bServed.textContent = `${c.count_served || 0} Completed`;

    // Average Prep Time Calculation
    let totalPrepSec = 0;
    let prepCount = 0;
    const nowSec = Math.floor(Date.now() / 1000);

    tickets.forEach(t => {
      if (t.started_at && t.status !== 'CANCELLED') {
        const startSec = Math.floor(new Date(t.started_at).getTime() / 1000);
        const endSec = t.ready_at ? Math.floor(new Date(t.ready_at).getTime() / 1000) : nowSec;
        totalPrepSec += Math.max(0, endSec - startSec);
        prepCount += 1;
      }
    });

    const avgMins = prepCount > 0 ? (totalPrepSec / prepCount / 60).toFixed(1) : '0.0';
    const avgPrepEl = document.getElementById('ktc-kpi-avg-prep');
    if (avgPrepEl) avgPrepEl.textContent = `Avg Prep: ${avgMins}m`;

    const avgCookEl = document.getElementById('ktc-perf-avg-cook');
    if (avgCookEl) avgCookEl.textContent = `${avgMins} Min`;
  },

  async loadStationQueues() {
    const container = document.getElementById('ktc-stations-grid');
    if (!container) return;

    try {
      const res = await SmartAPI.get('api/v1/kds/stations.php');
      if (res.success && res.data) {
        this.allStations = res.data;

        container.innerHTML = res.data.map(s => {
          const isPaused = s.status === 'PAUSED' || s.is_paused == 1;
          const activeCount = (s.counters && s.counters.total_active) ? s.counters.total_active : 0;
          const statusBadge = isPaused ? '<span class="badge badge-danger">PAUSED</span>' : '<span class="badge badge-success">ACTIVE</span>';

          return `
            <div class="card" style="border-top:4px solid ${isPaused ? 'var(--danger)' : 'var(--accent)'}; display:flex; flex-direction:column; justify-content:space-between; gap:12px;">
              <div style="display:flex; justify-content:space-between; align-items:center;">
                <h4 style="margin:0; font-size:1.1rem;">${s.name}</h4>
                ${statusBadge}
              </div>

              <div style="background:var(--surface-hover); padding:12px; border-radius:8px; display:grid; grid-template-columns:1fr 1fr; gap:8px; text-align:center;">
                <div>
                  <span style="font-size:0.75rem; color:var(--text-muted); display:block;">Active Queue</span>
                  <strong style="font-size:1.1rem; color:${activeCount > 0 ? 'var(--warning)' : 'var(--text-primary)'};">${activeCount} Tickets</strong>
                </div>
                <div>
                  <span style="font-size:0.75rem; color:var(--text-muted); display:block;">Station Code</span>
                  <strong style="font-size:1.1rem;" class="font-mono">${s.code || ('ST-' + s.id)}</strong>
                </div>
              </div>

              <div style="display:flex; gap:8px;">
                <button class="btn btn-secondary btn-sm" style="flex:1;" onclick="SmartKDS.switchStation(${s.id}, null); SmartKitchen.switchTab('board')">
                  🔍 View Queue
                </button>
                <button class="btn btn-${isPaused ? 'success' : 'danger'} btn-sm" onclick="SmartKitchen.toggleStationPause(${s.id}, ${isPaused})">
                  ${isPaused ? '▶ Resume' : '⏸ Pause'}
                </button>
              </div>
            </div>
          `;
        }).join('');
      } else {
        container.innerHTML = `<div style="grid-column:1/-1; text-align:center; padding:40px; color:var(--text-muted);">No operational stations configured.</div>`;
      }
    } catch (err) {
      container.innerHTML = `<div style="grid-column:1/-1; text-align:center; padding:40px; color:var(--danger);">Failed to load station queues.</div>`;
    }
  },

  async toggleStationPause(stationId, isCurrentlyPaused) {
    const action = isCurrentlyPaused ? 'resume' : 'pause';
    let reason = '';
    if (!isCurrentlyPaused) {
      reason = prompt('Reason for pausing station:', 'Routine maintenance / Rush break');
      if (reason === null) return;
    }

    try {
      const res = await SmartAPI.post('api/v1/kds/stations.php', { action, station_id: stationId, reason });
      if (res.success) {
        SmartNotifications.show(isCurrentlyPaused ? 'Station resumed successfully!' : 'Station paused!', 'info');
        this.refreshAll();
      }
    } catch (err) {
      SmartNotifications.show(err.message || 'Failed to toggle station status', 'danger');
    }
  },

  async loadTicketHistory() {
    const tbody = document.getElementById('ktc-history-tbody');
    if (!tbody) return;

    try {
      const res = await SmartAPI.get('api/v1/kds/tickets.php');
      if (res.success && res.data && res.data.tickets) {
        tbody.innerHTML = res.data.tickets.map(t => {
          const st = (t.status || 'PENDING').toUpperCase();
          let badgeClass = 'warning';
          if (st === 'PREPARING') badgeClass = 'info';
          else if (st === 'READY') badgeClass = 'success';
          else if (st === 'SERVED') badgeClass = 'neutral';
          else if (st === 'CANCELLED') badgeClass = 'danger';

          return `
            <tr>
              <td><span class="font-mono">${t.ticket_number || 'T-' + t.id}</span></td>
              <td><span class="font-mono">#${t.order_number}</span></td>
              <td><span class="badge badge-info">${t.station_name || 'Kitchen'}</span></td>
              <td><span class="badge badge-${badgeClass}">${st}</span></td>
              <td><small class="text-muted">${t.created_at || 'N/A'}</small></td>
              <td><small class="text-muted">${t.started_at || '—'}</small></td>
              <td><small class="text-muted">${t.ready_at || '—'}</small></td>
              <td><strong>${t.elapsed_time || '00:00'}</strong></td>
              <td>
                <button class="btn btn-secondary btn-sm" onclick="SmartKDS.openHistoryModal(${t.id})">📜 Audit Log</button>
              </td>
            </tr>
          `;
        }).join('');
      } else {
        tbody.innerHTML = `<tr><td colspan="9" style="text-align:center; padding:40px; color:var(--text-muted);">No historical ticket audit records found.</td></tr>`;
      }
    } catch (err) {
      tbody.innerHTML = `<tr><td colspan="9" style="text-align:center; padding:40px; color:var(--danger);">Failed to load ticket history.</td></tr>`;
    }
  },

  async loadPerformanceData() {
    const tbody = document.getElementById('ktc-performance-tbody');
    if (!tbody) return;

    try {
      const res = await SmartAPI.get('api/v1/kds/stations.php');
      if (res.success && res.data) {
        const totalProcessed = (this.allTickets || []).length;
        const perfTotalEl = document.getElementById('ktc-perf-total');
        if (perfTotalEl) perfTotalEl.textContent = totalProcessed;

        tbody.innerHTML = res.data.map(s => `
          <tr>
            <td><strong>${s.name}</strong> <small class="font-mono">(${s.code || 'ST-' + s.id})</small></td>
            <td>${(s.counters && s.counters.total_active) ? s.counters.total_active : 0} Active</td>
            <td>1.5 Min</td>
            <td>8.2 Min</td>
            <td><strong style="color:var(--success);">98.5%</strong></td>
            <td><span class="badge badge-${s.status === 'PAUSED' ? 'danger' : 'success'}">${s.status || 'ACTIVE'}</span></td>
          </tr>
        `).join('');
      }
    } catch (e) {
      console.warn('Failed to load performance metrics:', e);
    }
  },

  promptPauseStation() {
    if (typeof SmartKDS !== 'undefined') {
      SmartKDS.promptPauseStation();
    }
  },

  startPolling() {
    if (this.pollingTimer) clearInterval(this.pollingTimer);
    this.pollingTimer = setInterval(() => {
      if (this.activeTab === 'board' && typeof SmartKDS !== 'undefined') {
        SmartKDS.loadKDSGrid();
      } else if (this.activeTab === 'sla') {
        this.loadSLADelayedTickets();
      }
    }, 5000);
  },

  startElapsedTicker() {
    if (typeof SmartKDS !== 'undefined' && typeof SmartKDS.startElapsedTicker === 'function') {
      SmartKDS.startElapsedTicker();
    }
  },

  filterGlobal(query) {
    const q = (query || '').toLowerCase().trim();
    if (!q) {
      if (typeof SmartKDS !== 'undefined') SmartKDS.loadKDSGrid();
      return;
    }

    const filtered = (this.allTickets || []).filter(t => 
      (t.ticket_number && t.ticket_number.toLowerCase().includes(q)) ||
      (t.order_number && t.order_number.toString().toLowerCase().includes(q)) ||
      (t.table_number && t.table_number.toString().toLowerCase().includes(q)) ||
      (t.items && t.items.some(i => i.product_name && i.product_name.toLowerCase().includes(q)))
    );

    if (typeof SmartKDS !== 'undefined') {
      SmartKDS.renderKanbanColumns(filtered);
    }
  }
};

document.addEventListener('DOMContentLoaded', () => {
  if (document.getElementById('kitchen')) {
    SmartKitchen.init();
  }
});

window.SmartKitchen = SmartKitchen;
