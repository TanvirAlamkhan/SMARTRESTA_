/**
 * SMARTRESTA Frontend Routing & Ticket Dispatch Controller
 * Prompt 07: Smart Order Routing Engine, Multi-Station Dispatch & Item-Level Routing
 */

var SmartRouting = window.SmartRouting || {
  stations: [],

  async init() {
    await this.loadStations();
  },

  async loadStations() {
    try {
      const res = await SmartAPI.get('api/v1/stations/index.php');
      if (res.success && res.data) {
        this.stations = res.data;
        this.renderStationFilters();
      }
    } catch (err) {
      console.warn('Failed to load stations:', err.message);
    }
  },

  renderStationFilters() {
    const container = document.getElementById('station-filter-tabs');
    if (!container) return;

    let html = `<button class="btn btn-sm btn-secondary active-filter" onclick="SmartRouting.filterStationQueue(0, this)">All Stations</button>`;
    this.stations.forEach(s => {
      html += `<button class="btn btn-sm btn-secondary" onclick="SmartRouting.filterStationQueue(${s.id}, this)">${s.name} (${s.type})</button>`;
    });
    container.innerHTML = html;
  },

  async dispatchOrder(orderId) {
    try {
      const res = await SmartAPI.post('api/v1/routing/route.php', { order_id: orderId });
      if (res.success) {
        SmartNotifications.show('Order routed to station tickets successfully', 'success');
        if (typeof loadActiveOrders === 'function') loadActiveOrders();
        this.openOrderRoutingModal(orderId);
      }
    } catch (err) {
      SmartNotifications.show(err.message || 'Routing failed', 'danger');
    }
  },

  async openOrderRoutingModal(orderId) {
    try {
      const res = await SmartAPI.get(`api/v1/routing/index.php?order_id=${orderId}`);
      if (!res.success || !res.data) {
        SmartNotifications.show('Failed to load order routing details', 'danger');
        return;
      }

      const summary = res.data;
      const o = summary.order;
      const routes = summary.routes || [];
      const tickets = summary.tickets || [];
      const items = summary.items || [];

      document.getElementById('routing-modal-title').textContent = `Station Routing — Order #${o.order_number}`;
      document.getElementById('routing-modal-subtitle').textContent = `Table: ${o.table_number || 'N/A'} | Status: ${o.order_status} | Waiter: ${o.waiter_name || 'Staff'}`;

      const body = document.getElementById('routing-modal-body');
      
      let itemsHtml = `
        <div style="margin-bottom:20px;">
          <h4 style="margin-bottom:10px;">Item-Level Routing Breakdown</h4>
          <table class="data-table">
            <thead>
              <tr>
                <th>ITEM</th>
                <th>QTY</th>
                <th>ASSIGNED STATION</th>
                <th>ROUTING STATUS</th>
                <th>ACTION</th>
              </tr>
            </thead>
            <tbody>
              ${items.map(i => `
                <tr>
                  <td><strong>${i.item_name}</strong> ${i.variant_name ? `<small>(${i.variant_name})</small>` : ''}</td>
                  <td>${i.quantity}</td>
                  <td><span class="badge badge-info">${i.station_name || 'Unassigned'}</span></td>
                  <td><span class="badge ${i.routing_status === 'ROUTED' ? 'badge-success' : 'badge-warning'}">${i.routing_status || 'UNROUTED'}</span></td>
                  <td>
                    <button class="btn btn-secondary btn-sm" onclick="SmartRouting.promptReroute(${i.id}, '${i.item_name.replace(/'/g, "\\'")}')">Reroute</button>
                  </td>
                </tr>
              `).join('')}
            </tbody>
          </table>
        </div>
      `;

      let ticketsHtml = `
        <div>
          <h4 style="margin-bottom:10px;">Generated Station Tickets</h4>
          ${tickets.length > 0 ? `
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap:16px;">
              ${tickets.map(t => `
                <div style="background:var(--surface); border:1px solid var(--border); border-radius:8px; padding:16px;">
                  <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                    <div>
                      <strong style="font-size:1.05rem;">${t.ticket_number}</strong>
                      <div><small class="badge badge-neutral">${t.station_name}</small></div>
                    </div>
                    <span class="badge ${t.status === 'NEW' ? 'badge-warning' : (t.status === 'READY' ? 'badge-success' : 'badge-info')}">${t.status}</span>
                  </div>
                  <div style="border-top:1px dashed var(--border); padding-top:8px; margin-top:8px;">
                    ${(t.items || []).map(ti => `
                      <div style="display:flex; justify-content:space-between; margin-bottom:4px; font-size:0.9rem;">
                        <span>${ti.quantity}× ${ti.product_name} ${ti.variant_name ? `(${ti.variant_name})` : ''}</span>
                        ${ti.modifiers_snapshot ? `<small class="text-muted">${ti.modifiers_snapshot}</small>` : ''}
                      </div>
                    `).join('')}
                  </div>
                </div>
              `).join('')}
            </div>
          ` : `<div class="text-muted" style="padding:16px; text-align:center; background:var(--surface); border-radius:8px;">No station tickets generated yet. Click Dispatch to route items.</div>`}
        </div>
      `;

      body.innerHTML = itemsHtml + ticketsHtml;

      const footer = document.getElementById('routing-modal-footer');
      let footerBtns = `<button class="btn btn-secondary" onclick="SmartModal.close('order-routing-modal')">Close</button>`;

      if (['SUBMITTED', 'CONFIRMED'].includes(o.order_status) || items.some(i => i.routing_status === 'UNROUTED')) {
        footerBtns += `<button class="btn btn-primary" onclick="SmartRouting.dispatchOrder(${o.id})">Dispatch / Re-Route Order</button>`;
      }

      footer.innerHTML = footerBtns;
      SmartModal.open('order-routing-modal');

    } catch (err) {
      SmartNotifications.show(err.message || 'Error opening order routing details', 'danger');
    }
  },

  async promptReroute(orderItemId, itemName) {
    if (this.stations.length === 0) await this.loadStations();

    const options = this.stations.map(s => `${s.id}: ${s.name} (${s.type})`).join('\n');
    const input = prompt(`Select target station ID for "${itemName}":\n\n${options}`);
    if (!input) return;

    const newStationId = parseInt(input.trim(), 10);
    if (isNaN(newStationId)) {
      SmartNotifications.show('Invalid station ID entered', 'danger');
      return;
    }

    const reason = prompt('Optional reason for rerouting:');

    try {
      const res = await SmartAPI.post('api/v1/routing/reroute.php', {
        order_item_id: orderItemId,
        new_station_id: newStationId,
        reason: reason || 'Manual override'
      });
      if (res.success) {
        SmartNotifications.show(`Item "${itemName}" rerouted successfully`, 'success');
        if (typeof loadActiveOrders === 'function') loadActiveOrders();
        SmartModal.close('order-routing-modal');
      }
    } catch (err) {
      SmartNotifications.show(err.message || 'Failed to reroute item', 'danger');
    }
  },

  async filterStationQueue(stationId, btnEl) {
    if (btnEl) {
      document.querySelectorAll('#station-filter-tabs .btn').forEach(b => b.classList.remove('active-filter', 'btn-primary'));
      btnEl.classList.add('active-filter', 'btn-primary');
    }
    await this.loadStationQueue(stationId);
  },

  async loadStationQueue(stationId = 0) {
    const container = document.getElementById('station-tickets-grid');
    if (!container) return;

    try {
      let url = 'api/v1/routing/tickets.php';
      if (stationId > 0) url += `?station_id=${stationId}`;

      const res = await SmartAPI.get(url);
      if (res.success && res.data && res.data.length > 0) {
        container.innerHTML = res.data.map(t => `
          <div style="background:var(--surface); border:1px solid var(--border); border-radius:8px; padding:16px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
              <div>
                <span class="font-mono" style="font-weight:700;">${t.ticket_number}</span>
                <span class="badge badge-neutral" style="margin-left:6px;">${t.station_name}</span>
              </div>
              <span class="badge ${t.status === 'NEW' ? 'badge-warning' : (t.status === 'READY' ? 'badge-success' : 'badge-info')}">${t.status}</span>
            </div>
            <div class="text-sm text-muted" style="margin-bottom:8px;">
              Order #${t.order_number} | Table: ${t.table_number || 'Takeaway'} | Created: ${t.created_at}
            </div>
            <div style="border-top:1px dashed var(--border); border-bottom:1px dashed var(--border); padding:10px 0; margin-bottom:12px;">
              ${(t.items || []).map(ti => `
                <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                  <strong>${ti.quantity}× ${ti.product_name} ${ti.variant_name ? `<small>(${ti.variant_name})</small>` : ''}</strong>
                  ${ti.modifiers_snapshot ? `<small class="badge badge-neutral">+ ${ti.modifiers_snapshot}</small>` : ''}
                </div>
                ${ti.special_instructions ? `<div class="text-sm text-warning" style="margin-bottom:4px;">Note: ${ti.special_instructions}</div>` : ''}
              `).join('')}
            </div>
            <div style="display:flex; justify-content:flex-end; gap:8px;">
              ${t.status === 'NEW' ? `<button class="btn btn-primary btn-sm" onclick="SmartRouting.updateTicketStatus(${t.id}, 'PREPARING')">Start Preparing</button>` : ''}
              ${t.status === 'PREPARING' ? `<button class="btn btn-success btn-sm" onclick="SmartRouting.updateTicketStatus(${t.id}, 'READY')">Mark Ready</button>` : ''}
              ${t.status === 'READY' ? `<button class="btn btn-neutral btn-sm" onclick="SmartRouting.updateTicketStatus(${t.id}, 'SERVED')">Mark Served</button>` : ''}
            </div>
          </div>
        `).join('');
      } else {
        container.innerHTML = `<div class="text-muted" style="grid-column: 1 / -1; padding:40px; text-align:center;">No active station tickets in queue.</div>`;
      }
    } catch (err) {
      container.innerHTML = `<div class="text-danger" style="grid-column: 1 / -1; padding:40px; text-align:center;">Failed to load station tickets queue.</div>`;
    }
  }
};

document.addEventListener('DOMContentLoaded', () => {
  SmartRouting.init();
});

window.SmartRouting = SmartRouting;
