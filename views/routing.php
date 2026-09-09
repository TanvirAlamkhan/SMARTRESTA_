<!-- VIEW: STATION ROUTING ENGINE & TICKET DISPATCH -->
<section id="routing-view" class="role-view" style="display:none;">
  <div class="card">
    <div class="card-header">
      <div>
        <h3>Smart Order Routing Engine & Station Dispatch</h3>
        <p class="text-sm">Real-time multi-station order routing, item dispatch, station ticket queue, and operational status</p>
      </div>
      <button class="btn btn-secondary btn-sm" onclick="SmartRouting.loadStationQueue()">Refresh Queue</button>
    </div>

    <!-- Station Filter Tabs -->
    <div id="station-filter-tabs" style="padding: 16px var(--space-4); display:flex; gap:8px; align-items:center; flex-wrap:wrap; border-bottom: 1px solid var(--border);">
      <!-- Populated dynamically via SmartRouting.renderStationFilters -->
    </div>

    <!-- Station Tickets Grid -->
    <div id="station-tickets-grid" style="padding: 20px; display:grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap:20px;">
      <div class="text-muted" style="grid-column: 1 / -1; text-align:center; padding:40px;">Loading station ticket queue...</div>
    </div>
  </div>
</section>
