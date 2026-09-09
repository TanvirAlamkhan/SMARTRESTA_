<!-- VIEW 4: KITCHEN DISPLAY SYSTEM (KDS) -->
<section id="kds" class="role-view" style="display:none;">
  <div id="kds-connection-banner" class="alert alert-danger" style="display:none; margin-bottom:16px; font-weight:600;"></div>

  <div class="card" style="margin-bottom:20px;">
    <div class="card-header" style="flex-wrap:wrap; gap:12px;">
      <div>
        <h3>Multi-Station Kitchen Display System (KDS)</h3>
        <p class="text-sm">Real-time database tickets, status workflow (New ➔ Prep ➔ Ready ➔ Served), timers, and re-fire dispatch</p>
      </div>
      <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
        <span class="badge badge-warning" id="kds-count-active">0 Active</span>
        <span class="badge badge-info" id="kds-count-new">0 New</span>
        <span class="badge badge-info" id="kds-count-preparing">0 Prep</span>
        <span class="badge badge-success" id="kds-count-ready">0 Ready</span>
        <span class="badge badge-neutral" id="kds-count-served">0 Served</span>
        <button class="btn btn-secondary btn-sm" onclick="SmartKDS.promptPauseStation()">⏸️ Pause / Resume Station</button>
        <button class="btn btn-primary btn-sm" onclick="SmartModal.open('manage-station-modal')">+ Create Station</button>
      </div>
    </div>

    <!-- Station Filter Tabs -->
    <div id="kds-station-tabs" style="padding: 12px var(--space-4); display:flex; gap:8px; align-items:center; flex-wrap:wrap; border-top: 1px solid var(--border);">
      <!-- Populated dynamically by SmartKDS.renderStationSelector -->
    </div>
  </div>

  <div class="kds-kanban">
    <div class="kds-column">
      <div class="kds-column-header new">
        <h3>NEW TICKETS</h3>
        <span class="badge badge-warning">Received</span>
      </div>
      <div class="kds-tickets-container" id="kds-col-new">
        <div style="text-align:center; padding: 40px; color:var(--text-muted);">Loading tickets...</div>
      </div>
    </div>

    <div class="kds-column">
      <div class="kds-column-header preparing">
        <h3>PREPARING</h3>
        <span class="badge badge-info">In Cooking</span>
      </div>
      <div class="kds-tickets-container" id="kds-col-preparing">
        <div style="text-align:center; padding: 40px; color:var(--text-muted);">Loading tickets...</div>
      </div>
    </div>

    <div class="kds-column">
      <div class="kds-column-header ready">
        <h3>READY FOR SERVING</h3>
        <span class="badge badge-success">Pickup / Pass</span>
      </div>
      <div class="kds-tickets-container" id="kds-col-ready">
        <div style="text-align:center; padding: 40px; color:var(--text-muted);">Loading tickets...</div>
      </div>
    </div>

    <div class="kds-column">
      <div class="kds-column-header served">
        <h3>SERVED</h3>
        <span class="badge badge-neutral">Completed</span>
      </div>
      <div class="kds-tickets-container" id="kds-col-served">
        <div style="text-align:center; padding: 40px; color:var(--text-muted);">Loading tickets...</div>
      </div>
    </div>
  </div>
</section>
