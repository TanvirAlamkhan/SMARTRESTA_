<!-- VIEW 2: RESTAURANT FLOORS & DINING TABLES MAP -->
<section id="tables" class="role-view" style="display:none;">
  <div class="card">
    <div class="card-header">
      <div>
        <h3>Restaurant Floor Map & Dining Session Operations</h3>
        <p class="text-sm">Real-time table occupancy, atomic dining session opening, table transfers, and floor status</p>
      </div>
      <div style="display:flex; gap:8px;">
        <button class="btn btn-secondary btn-sm" onclick="loadFloorTables()">Refresh Floor Map</button>
        <button class="btn btn-primary btn-sm" onclick="SmartModal.open('create-table-modal')">+ Create Table</button>
      </div>
    </div>

    <!-- Floor Grid -->
    <div id="floor-tables-container" class="floor-grid">
      <div style="grid-column: 1/-1; text-align:center; padding: 40px; color: var(--text-muted);">Loading restaurant tables...</div>
    </div>
  </div>
</section>
