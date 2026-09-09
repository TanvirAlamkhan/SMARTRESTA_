<!-- VIEW: DEDICATED KITCHEN DISPLAY SYSTEM (KDS) & STATION WORKSPACE -->
<section id="kitchen" class="role-view" style="display:none;">

  <div id="ktc-connection-banner" class="alert alert-danger" style="display:none; margin-bottom:16px; font-weight:600;"></div>

  <!-- Kitchen KPI Summary Grid -->
  <div class="grid-kpi" id="kitchen-kpi-bar" style="margin-bottom:24px;">
    <div class="kpi-card" style="border-left: 4px solid var(--warning, #f59e0b);">
      <span class="kpi-label">New Incoming Tickets</span>
      <div class="kpi-value" id="ktc-kpi-new-count" style="color:var(--warning, #f59e0b);">0</div>
      <span class="kpi-trend negative">Pending Acceptance</span>
    </div>

    <div class="kpi-card" style="border-left: 4px solid var(--info, #3b82f6);">
      <span class="kpi-label">Preparing Queue</span>
      <div class="kpi-value" id="ktc-kpi-prep-count" style="color:var(--info, #3b82f6);">0</div>
      <span class="kpi-trend positive">Cooking in Progress</span>
    </div>

    <div class="kpi-card" style="border-left: 4px solid var(--success, #10b981);">
      <span class="kpi-label">Ready for Pickup</span>
      <div class="kpi-value" id="ktc-kpi-ready-count" style="color:var(--success, #10b981);">0</div>
      <span class="kpi-trend positive">Awaiting Server Pass</span>
    </div>

    <div class="kpi-card" style="border-left: 4px solid var(--accent, #6366f1);">
      <span class="kpi-label">Completed Today</span>
      <div class="kpi-value" id="ktc-kpi-served-count" style="color:var(--accent, #6366f1);">0</div>
      <span class="kpi-trend positive">Served Tickets</span>
    </div>

    <div class="kpi-card" style="border-left: 4px solid var(--danger, #ef4444);">
      <span class="kpi-label">Delayed SLA Tickets</span>
      <div class="kpi-value" id="ktc-kpi-delayed-count" style="color:var(--danger, #ef4444);">0</div>
      <span class="kpi-trend negative" id="ktc-kpi-avg-prep">Avg Prep: 0m</span>
    </div>
  </div>

  <!-- Kitchen Station Control Header -->
  <div class="card" style="margin-bottom:24px;">
    <div class="card-header" style="flex-wrap:wrap; gap:16px;">
      <div>
        <h2 style="font-size:1.35rem; font-weight:700; margin:0 0 4px 0;">Multi-Station Kitchen Display System (KDS)</h2>
        <p class="text-sm" style="margin:0; color:var(--text-muted);">
          Real-time order ticket routing, preparation timers, SLA monitoring, re-fire dispatch, and station workload isolation
        </p>
      </div>

      <!-- Action Buttons -->
      <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
        <button class="btn btn-secondary btn-sm" onclick="SmartKitchen.promptPauseStation()">⏸️ Pause / Resume Station</button>
        <button class="btn btn-secondary btn-sm" onclick="SmartKitchen.refreshAll()">🔄 Refresh Queue</button>
        <button class="btn btn-primary btn-sm" onclick="SmartModal.open('manage-station-modal')">+ Create Station</button>
      </div>
    </div>

    <!-- Station Filter Tabs -->
    <div id="ktc-station-tabs" style="padding: 12px var(--space-4); display:flex; gap:8px; align-items:center; flex-wrap:wrap; border-top: 1px solid var(--border);">
      <!-- Populated dynamically by SmartKitchen.renderStationSelector -->
    </div>

    <!-- Quick Search Bar -->
    <div style="padding:12px var(--space-4);">
      <input type="text" id="kitchen-global-search" class="form-control" placeholder="🔍 Search Ticket #, Order #, Table #, Item Name, or Waiter..." style="font-size:0.95rem; padding:12px 16px; border-radius:10px;">
    </div>
  </div>

  <!-- Tab Navigation Controls -->
  <div style="display:flex; gap:8px; border-bottom: 2px solid var(--border); margin-bottom: 20px; overflow-x:auto; padding-bottom:2px;">
    <button class="btn btn-secondary kitchen-tab-btn active" data-tab="board" onclick="SmartKitchen.switchTab('board', this)">
      🍳 Live KDS Kanban Board
    </button>
    <button class="btn btn-secondary kitchen-tab-btn" data-tab="sla" onclick="SmartKitchen.switchTab('sla', this)">
      ⏱️ Delayed Orders & SLA Monitor
    </button>
    <button class="btn btn-secondary kitchen-tab-btn" data-tab="stations" onclick="SmartKitchen.switchTab('stations', this)">
      🎛️ Station Queues & Load
    </button>
    <button class="btn btn-secondary kitchen-tab-btn" data-tab="history" onclick="SmartKitchen.switchTab('history', this)">
      📜 Ticket Audit History
    </button>
    <button class="btn btn-secondary kitchen-tab-btn" data-tab="performance" onclick="SmartKitchen.switchTab('performance', this)">
      📈 Kitchen SLA Performance
    </button>
  </div>

  <!-- Tab Pane 1: Visual KDS Kanban Board -->
  <div id="kitchen-pane-board" class="kitchen-tab-pane">
    <div class="kds-kanban">
      
      <!-- NEW TICKETS Column -->
      <div class="kds-column">
        <div class="kds-column-header new">
          <h3>NEW TICKETS</h3>
          <span class="badge badge-warning" id="ktc-col-badge-new">0 Received</span>
        </div>
        <div class="kds-tickets-container" id="ktc-col-new">
          <div style="text-align:center; padding: 40px; color:var(--text-muted);">Loading tickets...</div>
        </div>
      </div>

      <!-- PREPARING Column -->
      <div class="kds-column">
        <div class="kds-column-header preparing">
          <h3>PREPARING</h3>
          <span class="badge badge-info" id="ktc-col-badge-prep">0 Cooking</span>
        </div>
        <div class="kds-tickets-container" id="ktc-col-preparing">
          <div style="text-align:center; padding: 40px; color:var(--text-muted);">Loading tickets...</div>
        </div>
      </div>

      <!-- READY FOR SERVING Column -->
      <div class="kds-column">
        <div class="kds-column-header ready">
          <h3>READY FOR SERVING</h3>
          <span class="badge badge-success" id="ktc-col-badge-ready">0 Pickup</span>
        </div>
        <div class="kds-tickets-container" id="ktc-col-ready">
          <div style="text-align:center; padding: 40px; color:var(--text-muted);">Loading tickets...</div>
        </div>
      </div>

      <!-- SERVED Column -->
      <div class="kds-column">
        <div class="kds-column-header served">
          <h3>SERVED / COMPLETED</h3>
          <span class="badge badge-neutral" id="ktc-col-badge-served">0 Completed</span>
        </div>
        <div class="kds-tickets-container" id="ktc-col-served">
          <div style="text-align:center; padding: 40px; color:var(--text-muted);">Loading tickets...</div>
        </div>
      </div>

    </div>
  </div>

  <!-- Tab Pane 2: Delayed Orders & SLA Monitor -->
  <div id="kitchen-pane-sla" class="kitchen-tab-pane" style="display:none;">
    <div class="card">
      <div class="card-header">
        <h3>Delayed Orders & Preparation SLA Monitor</h3>
        <span class="text-sm" style="color:var(--text-muted);">Orders exceeding preparation SLA target (15 minutes)</span>
      </div>
      <div id="ktc-sla-tickets-container" style="display:grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap:16px; padding:16px;">
        <div style="grid-column:1/-1; text-align:center; padding:40px; color:var(--text-muted);">Loading delayed tickets...</div>
      </div>
    </div>
  </div>

  <!-- Tab Pane 3: Station Board & Queue Breakdown -->
  <div id="kitchen-pane-stations" class="kitchen-tab-pane" style="display:none;">
    <div class="card" style="margin-bottom:16px;">
      <div class="card-header">
        <h3>Operational Station Queues & Workload Distribution</h3>
        <button class="btn btn-primary btn-sm" onclick="SmartModal.open('manage-station-modal')">+ Add Station</button>
      </div>
    </div>
    <div id="ktc-stations-grid" style="display:grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap:16px;">
      <div style="grid-column:1/-1; text-align:center; padding:40px; color:var(--text-muted);">Loading station queues...</div>
    </div>
  </div>

  <!-- Tab Pane 4: Ticket Audit History & Search -->
  <div id="kitchen-pane-history" class="kitchen-tab-pane" style="display:none;">
    <div class="card">
      <div class="card-header" style="flex-wrap:wrap; gap:12px;">
        <h3>Ticket Lifecycle Audit History</h3>
        <button class="btn btn-secondary btn-sm" onclick="SmartKitchen.loadTicketHistory()">🔄 Refresh Audit Trail</button>
      </div>
      <div class="table-container">
        <table class="data-table">
          <thead>
            <tr>
              <th>TICKET #</th>
              <th>ORDER #</th>
              <th>STATION</th>
              <th>STATUS</th>
              <th>CREATED AT</th>
              <th>STARTED AT</th>
              <th>READY AT</th>
              <th>PREP TIME</th>
              <th>ACTION</th>
            </tr>
          </thead>
          <tbody id="ktc-history-tbody">
            <tr><td colspan="9" style="text-align:center; padding:40px; color:var(--text-muted);">Loading ticket audit log...</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Tab Pane 5: Kitchen SLA Performance Analytics -->
  <div id="kitchen-pane-performance" class="kitchen-tab-pane" style="display:none;">
    
    <!-- Kitchen Performance Metrics -->
    <div class="grid-kpi" style="margin-bottom:20px;">
      <div class="kpi-card" style="border-left:4px solid var(--accent);">
        <span class="kpi-label">Tickets Processed</span>
        <div class="kpi-value" id="ktc-perf-total">0</div>
        <span class="kpi-trend positive">Today Total</span>
      </div>
      <div class="kpi-card" style="border-left:4px solid var(--success);">
        <span class="kpi-label">On-Time SLA Rate</span>
        <div class="kpi-value" id="ktc-perf-ontime-rate" style="color:var(--success);">100%</div>
        <span class="kpi-trend positive">Under 15 Min SLA</span>
      </div>
      <div class="kpi-card" style="border-left:4px solid var(--info);">
        <span class="kpi-label">Avg Queue Time</span>
        <div class="kpi-value" id="ktc-perf-avg-queue" style="color:var(--info);">0.0 Min</div>
        <span class="kpi-trend positive">Wait Time</span>
      </div>
      <div class="kpi-card" style="border-left:4px solid var(--warning);">
        <span class="kpi-label">Avg Cooking Time</span>
        <div class="kpi-value" id="ktc-perf-avg-cook" style="color:var(--warning);">0.0 Min</div>
        <span class="kpi-trend positive">Preparation Time</span>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <h3>Station SLA Efficiency Ledger</h3>
        <button class="btn btn-secondary btn-sm" onclick="SmartKitchen.loadPerformanceData()">🔄 Refresh SLA Metrics</button>
      </div>
      <div class="table-container">
        <table class="data-table">
          <thead>
            <tr>
              <th>STATION NAME</th>
              <th>TICKETS PROCESSED</th>
              <th>AVG QUEUE TIME</th>
              <th>AVG PREP TIME</th>
              <th>ON-TIME RATE</th>
              <th>STATUS</th>
            </tr>
          </thead>
          <tbody id="ktc-performance-tbody">
            <tr><td colspan="6" style="text-align:center; padding:40px; color:var(--text-muted);">Loading SLA efficiency breakdown...</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</section>
