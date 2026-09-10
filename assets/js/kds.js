/**
 * SMARTRESTA Kitchen Display System (KDS) & Multi-Station Production Controller
 * Prompt 10: Kitchen Display System, Counter Operations & Multi-Station Production Workflow
 */

var SmartKDS = window.SmartKDS || {
  stations: [],
  currentStationId: 0,
  pollingTimer: null,
  isFetchingQueue: false,
  seenTicketIds: new Set(),
  tickerTimer: null,

  async init() {
    await this.loadStations();
    await this.loadKDSGrid();
    this.startPolling();
    this.startElapsedTicker();
  },

  async loadStations() {
    try {
      const res = await SmartAPI.get('api/v1/kds/stations.php');
      if (res.success && res.data) {
        this.stations = res.data;
        this.renderStationSelector();
      }
    } catch (err) {
      console.warn('Failed to load operational stations:', err.message);
    }
  },

  renderStationSelector() {
    const container = document.getElementById('kds-station-tabs');
    if (!container) return;

    let html = `
      <button class="btn btn-sm ${this.currentStationId === 0 ? 'btn-primary active-filter' : 'btn-secondary'}" onclick="SmartKDS.switchStation(0, this)">
        All Stations
      </button>
    `;

    this.stations.forEach(s => {
      const activeCount = (s.counters && s.counters.total_active) ? s.counters.total_active : 0;
      const isPaused = s.status === 'PAUSED' || s.is_paused == 1;
      const badgeClass = isPaused ? 'badge-danger' : (activeCount > 0 ? 'badge-warning' : 'badge-neutral');
      const isSelected = this.currentStationId === parseInt(s.id);

      html += `
        <button class="btn btn-sm ${isSelected ? 'btn-primary active-filter' : 'btn-secondary'}" onclick="SmartKDS.switchStation(${s.id}, this)">
          ${s.name} <span class="badge ${badgeClass}">${isPaused ? 'PAUSED' : activeCount}</span>
        </button>
      `;
    });

    container.innerHTML = html;
  },

  async switchStation(stationId, btnEl) {
    this.currentStationId = parseInt(stationId, 10);
    if (btnEl) {
      document.querySelectorAll('#kds-station-tabs .btn').forEach(b => b.classList.remove('active-filter', 'btn-primary'));
      btnEl.classList.add('active-filter', 'btn-primary');
    }
    await this.loadKDSGrid();
  },

  async loadKDSGrid() {
    if (this.isFetchingQueue) return;
    this.isFetchingQueue = true;

    const connBanner = document.getElementById('kds-connection-banner');

    try {
      let url = `api/v1/kds/tickets.php`;
      if (this.currentStationId > 0) {
        url += `?station_id=${this.currentStationId}`;
      }

      const res = await SmartAPI.get(url);
      if (connBanner) connBanner.style.display = 'none';

      if (res.success && res.data) {
        const tickets = res.data.tickets || [];
        const counters = res.data.counters || {};

        this.checkNewIncomingTickets(tickets);
        this.renderHeaderCounters(counters);
        this.renderKanbanColumns(tickets);
      }
    } catch (err) {
      if (connBanner) {
        connBanner.style.display = 'block';
        connBanner.textContent = '⚡ Connection Lost. Reconnecting KDS live queue...';
      }
    } finally {
      this.isFetchingQueue = false;
    }
  },

  checkNewIncomingTickets(tickets) {
    let hasNew = false;
    tickets.forEach(t => {
      if (t.status === 'NEW' && !this.seenTicketIds.has(t.id)) {
        hasNew = true;
      }
      this.seenTicketIds.add(t.id);
    });

    if (hasNew && this.seenTicketIds.size > tickets.length) {
      this.playChimeAlert();
    }
  },

  playChimeAlert() {
    try {
      const audio = new Audio('https://actions.google.com/sounds/v1/alarms/beep_short.ogg');
      audio.play().catch(() => {});
    } catch (e) {}
  },

  renderHeaderCounters(c) {
    const elActive = document.getElementById('kds-count-active');
    const elNew = document.getElementById('kds-count-new');
    const elPrep = document.getElementById('kds-count-preparing');
    const elReady = document.getElementById('kds-count-ready');
    const elServed = document.getElementById('kds-count-served');

    if (elActive) elActive.textContent = `${c.total_active || 0} Active`;
    if (elNew) elNew.textContent = `${c.count_new || 0} New`;
    if (elPrep) elPrep.textContent = `${c.count_preparing || 0} Prep`;
    if (elReady) elReady.textContent = `${c.count_ready || 0} Ready`;
    if (elServed) elServed.textContent = `${c.count_served || 0} Served`;
  },

  renderKanbanColumns(tickets) {
    const colNew = document.getElementById('kds-col-new');
    const colPrep = document.getElementById('kds-col-preparing');
    const colReady = document.getElementById('kds-col-ready');
    const colServed = document.getElementById('kds-col-served');

    const newTickets = tickets.filter(t => t.status === 'NEW');
    const prepTickets = tickets.filter(t => t.status === 'PREPARING');
    const readyTickets = tickets.filter(t => t.status === 'READY');
    const servedTickets = tickets.filter(t => t.status === 'SERVED');

    if (colNew) colNew.innerHTML = this.buildTicketListHtml(newTickets, 'NEW');
    if (colPrep) colPrep.innerHTML = this.buildTicketListHtml(prepTickets, 'PREPARING');
    if (colReady) colReady.innerHTML = this.buildTicketListHtml(readyTickets, 'READY');
    if (colServed) colServed.innerHTML = this.buildTicketListHtml(servedTickets, 'SERVED');
  },

  buildTicketListHtml(tickets, columnStatus) {
    if (tickets.length === 0) {
      const messages = {
        'NEW': 'No new tickets in queue.',
        'PREPARING': 'No tickets currently preparing.',
        'READY': 'No ready tickets waiting for pickup.',
        'SERVED': 'Served tickets clear automatically.'
      };
      return `<div style="text-align:center; padding:36px var(--space-4); color:var(--text-muted); font-size:0.875rem;">${messages[columnStatus]}</div>`;
    }

    return tickets.map(t => {
      let priorityClass = 'badge-neutral';
      if (t.priority === 'HIGH') priorityClass = 'badge-warning';
      if (t.priority === 'URGENT') priorityClass = 'badge-danger';

      const orderTypeBadge = (t.order_type === 'DINE_IN') ? 'DINE-IN' : (t.order_type === 'TAKEAWAY' ? 'TAKEAWAY' : 'DELIVERY');
      const tableInfo = (t.order_type === 'DINE_IN' && t.table_number) ? `Table ${t.table_number}` : orderTypeBadge;

      return `
        <div class="kds-ticket-card ${t.priority === 'URGENT' ? 'urgent-flash' : ''}" data-created="${t.created_at}" data-started="${t.started_at || ''}">
          <!-- Header -->
          <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:8px;">
            <div>
              <div style="font-weight:700; font-size:1.1rem; color:var(--text-primary);" class="font-mono">${t.ticket_number}</div>
              <div class="text-sm" style="color:var(--text-secondary); margin-top:2px;">
                Order #${t.order_number} | <strong>${tableInfo}</strong>
              </div>
            </div>
            <div style="display:flex; flex-direction:column; align-items:flex-end; gap:4px;">
              <span class="badge ${priorityClass}">${t.priority}</span>
              <span class="badge badge-info">${t.station_badge || t.station_name}</span>
            </div>
          </div>

          <!-- Waiter & Timestamp Metadata -->
          <div style="display:flex; justify-content:space-between; font-size:0.8rem; color:var(--text-muted); margin-bottom:10px; padding-bottom:6px; border-bottom:1px dashed var(--border);">
            <span>Staff: ${t.waiter_name || 'Counter'}</span>
            <span class="elapsed-time-tag" id="elapsed-${t.id}">Calculating...</span>
          </div>

          <!-- Ticket Line Items -->
          <div style="margin-bottom:12px; display:flex; flex-direction:column; gap:6px;">
            ${(t.items || []).map(item => `
              <div style="background:var(--surface-hover); padding:6px 10px; border-radius:6px; border-left:3px solid var(--primary);">
                <div style="display:flex; justify-content:space-between; font-weight:600; font-size:0.95rem;">
                  <span>${item.quantity}× ${item.product_name} ${item.variant_name ? `<small>(${item.variant_name})</small>` : ''}</span>
                </div>
                ${item.modifiers_snapshot ? `<div style="font-size:0.8rem; color:var(--accent-dark); margin-top:2px;">+ ${item.modifiers_snapshot}</div>` : ''}
                ${(item.special_instructions || item.notes) ? `<div style="font-size:0.85rem; color:#F59E0B; font-weight:700; margin-top:2px;">📝 Instruction: "${item.special_instructions || item.notes}"</div>` : ''}
              </div>
            `).join('')}
          </div>

          ${t.order_notes ? `<div style="font-size:0.8rem; color:var(--info); margin-bottom:10px; padding:4px 8px; background:var(--info-light); border-radius:4px;">Order Note: ${t.order_notes}</div>` : ''}

          <!-- Action Buttons -->
          <div style="display:flex; gap:6px; flex-wrap:wrap; margin-top: auto;">
            ${t.status === 'NEW' ? `
              <button class="btn btn-primary btn-sm" style="flex:1; min-height:40px; font-weight:600;" onclick="SmartKDS.updateTicketStatus(${t.id}, 'PREPARING')">
                ▶ Start Prep
              </button>
              <button class="btn btn-secondary btn-sm" onclick="SmartKDS.openCancelModal(${t.id}, '${t.ticket_number}')">✕</button>
            ` : ''}

            ${t.status === 'PREPARING' ? `
              <button class="btn btn-success btn-sm" style="flex:1; min-height:40px; font-weight:600;" onclick="SmartKDS.updateTicketStatus(${t.id}, 'READY')">
                ✓ Mark Ready
              </button>
              <button class="btn btn-warning btn-sm" onclick="SmartKDS.openRefireModal(${t.id}, '${t.ticket_number}')">🔥 Re-fire</button>
              <button class="btn btn-secondary btn-sm" onclick="SmartKDS.openCancelModal(${t.id}, '${t.ticket_number}')">✕</button>
            ` : ''}

            ${t.status === 'READY' ? `
              <button class="btn btn-primary btn-sm" style="flex:1; min-height:40px; font-weight:600;" onclick="SmartKDS.updateTicketStatus(${t.id}, 'SERVED')">
                🛎️ Mark Served
              </button>
              <button class="btn btn-secondary btn-sm" onclick="SmartKDS.recallTicket(${t.id})">↺ Recall</button>
              <button class="btn btn-warning btn-sm" onclick="SmartKDS.openRefireModal(${t.id}, '${t.ticket_number}')">🔥</button>
            ` : ''}

            ${t.status === 'SERVED' ? `
              <button class="btn btn-secondary btn-sm" style="flex:1;" onclick="SmartKDS.recallTicket(${t.id})">↺ Recall to Ready</button>
              <button class="btn btn-secondary btn-sm" onclick="SmartKDS.openHistoryModal(${t.id})">📜 Audit</button>
            ` : ''}
          </div>
        </div>
      `;
    }).join('');
  },

  startElapsedTicker() {
    if (this.tickerTimer) clearInterval(this.tickerTimer);
    this.tickerTimer = setInterval(() => {
      document.querySelectorAll('.kds-ticket-card').forEach(card => {
        const created = card.getAttribute('data-created');
        const started = card.getAttribute('data-started');
        const tag = card.querySelector('.elapsed-time-tag');

        if (!tag || !created) return;

        const nowSec = Math.floor(Date.now() / 1000);
        const createdSec = Math.floor(new Date(created).getTime() / 1000);

        if (isNaN(createdSec)) return;

        const diffSec = Math.max(0, nowSec - createdSec);
        const mins = Math.floor(diffSec / 60);
        const secs = diffSec % 60;
        const timeStr = `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;

        if (started) {
          tag.textContent = `Prep Time: ${timeStr}`;
          tag.className = 'elapsed-time-tag preparing-ticker';
        } else {
          tag.textContent = `Waiting: ${timeStr}`;
          tag.className = 'elapsed-time-tag waiting-ticker';
        }
      });
    }, 1000);
  },

  async updateTicketStatus(ticketId, newStatus) {
    try {
      const res = await SmartAPI.post('api/v1/kds/tickets.php', { ticket_id: ticketId, status: newStatus });
      if (res.success) {
        SmartNotifications.show(res.message || `Ticket marked ${newStatus}`, 'success');
        this.loadKDSGrid();
        if (typeof loadActiveOrders === 'function') loadActiveOrders();
      }
    } catch (err) {
      SmartNotifications.show(err.message || 'Failed to update ticket status', 'danger');
    }
  },

  async recallTicket(ticketId) {
    const reason = prompt('Optional reason for ticket recall:');
    try {
      const res = await SmartAPI.post('api/v1/kds/recall.php', { ticket_id: ticketId, reason: reason || 'KDS recall' });
      if (res.success) {
        SmartNotifications.show(`Ticket recalled back to ${res.data.status}`, 'info');
        this.loadKDSGrid();
        if (typeof loadActiveOrders === 'function') loadActiveOrders();
      }
    } catch (err) {
      SmartNotifications.show(err.message || 'Failed to recall ticket', 'danger');
    }
  },

  openRefireModal(ticketId, ticketNum) {
    document.getElementById('refire-ticket-id').value = ticketId;
    document.getElementById('refire-modal-title').textContent = `Re-Fire Ticket #${ticketNum}`;
    SmartModal.open('refire-ticket-modal');
  },

  async submitRefireTicket() {
    const ticketId = document.getElementById('refire-ticket-id').value;
    const reason = document.getElementById('refire-reason').value.trim();

    if (!reason) {
      SmartNotifications.show('Please specify a re-fire reason (e.g. Burned, Cold, Quality issue)', 'warning');
      return;
    }

    try {
      const res = await SmartAPI.post('api/v1/kds/refire.php', { ticket_id: ticketId, reason });
      if (res.success) {
        SmartNotifications.show(`Re-fire ticket #${res.data.refire_ticket_number} dispatched with URGENT priority!`, 'success');
        SmartModal.close('refire-ticket-modal');
        document.getElementById('refire-reason').value = '';
        this.loadKDSGrid();
      }
    } catch (err) {
      SmartNotifications.show(err.message || 'Failed to re-fire ticket', 'danger');
    }
  },

  openCancelModal(ticketId, ticketNum) {
    document.getElementById('cancel-ticket-id').value = ticketId;
    document.getElementById('cancel-modal-title').textContent = `Cancel Ticket #${ticketNum}`;
    SmartModal.open('cancel-ticket-modal');
  },

  async submitCancelTicket() {
    const ticketId = document.getElementById('cancel-ticket-id').value;
    const reason = document.getElementById('cancel-reason').value.trim();

    if (!reason) {
      SmartNotifications.show('Please state cancellation reason', 'warning');
      return;
    }

    try {
      const res = await SmartAPI.post('api/v1/kds/cancel.php', { ticket_id: ticketId, reason });
      if (res.success) {
        SmartNotifications.show('Ticket cancelled successfully', 'info');
        SmartModal.close('cancel-ticket-modal');
        document.getElementById('cancel-reason').value = '';
        this.loadKDSGrid();
      }
    } catch (err) {
      SmartNotifications.show(err.message || 'Failed to cancel ticket', 'danger');
    }
  },

  async openHistoryModal(ticketId) {
    try {
      const res = await SmartAPI.get(`api/v1/kds/history.php?ticket_id=${ticketId}`);
      if (res.success && res.data) {
        const ticket = res.data.ticket;
        const history = res.data.history || [];

        document.getElementById('history-modal-title').textContent = `Audit History — Ticket #${ticket.ticket_number}`;
        const container = document.getElementById('history-timeline-container');

        if (history.length > 0) {
          container.innerHTML = history.map(h => `
            <div style="padding:10px 14px; background:var(--surface); border:1px solid var(--border); border-radius:6px; margin-bottom:8px;">
              <div style="display:flex; justify-content:space-between; align-items:center; font-weight:600;">
                <span>${h.from_status || 'CREATED'} ➔ <span class="badge badge-info">${h.to_status}</span></span>
                <small class="text-muted">${h.created_at}</small>
              </div>
              <div class="text-sm text-muted" style="margin-top:4px;">
                User: <strong>${h.user_name || 'System'}</strong> ${h.reason ? `| Reason: ${h.reason}` : ''}
              </div>
            </div>
          `).join('');
        } else {
          container.innerHTML = `<div style="text-align:center; padding:20px; color:var(--text-muted);">No historical transitions logged.</div>`;
        }

        SmartModal.open('ticket-history-modal');
      }
    } catch (err) {
      SmartNotifications.show(err.message || 'Failed to load ticket audit history', 'danger');
    }
  },

  async promptPauseStation() {
    if (this.currentStationId === 0) {
      SmartNotifications.show('Please select a specific station tab to pause or resume.', 'info');
      return;
    }

    const st = this.stations.find(s => parseInt(s.id) === this.currentStationId);
    if (!st) return;

    const isCurrentlyPaused = st.status === 'PAUSED' || st.is_paused == 1;
    const action = isCurrentlyPaused ? 'resume' : 'pause';

    let reason = '';
    if (action === 'pause') {
      reason = prompt(`Enter pause reason for ${st.name}:`, 'Station cleaning / Rush break');
      if (reason === null) return;
    }

    try {
      const res = await SmartAPI.post('api/v1/kds/stations.php', { action, station_id: this.currentStationId, reason });
      if (res.success) {
        SmartNotifications.show(isCurrentlyPaused ? `Station ${st.name} resumed!` : `Station ${st.name} paused!`, 'info');
        await this.loadStations();
        await this.loadKDSGrid();
      }
    } catch (err) {
      SmartNotifications.show(err.message || 'Failed to update station state', 'danger');
    }
  },

  startPolling() {
    if (this.pollingTimer) clearInterval(this.pollingTimer);
    this.pollingTimer = setInterval(() => {
      this.loadKDSGrid();
    }, 5000);
  },

  stopPolling() {
    if (this.pollingTimer) clearInterval(this.pollingTimer);
  }
};

document.addEventListener('DOMContentLoaded', () => {
  SmartKDS.init();
});

window.SmartKDS = SmartKDS;
