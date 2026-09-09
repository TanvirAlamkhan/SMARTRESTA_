<!-- VIEW: CRM, RESERVATIONS, LOYALTY, COUPONS & QR ORDERING (Prompt 14) -->
<section id="crm" class="role-view" style="display:none;">

  <!-- CRM Navigation Sub-Tabs -->
  <div class="card" style="margin-bottom:20px; padding:12px 20px;">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
      <div style="display:flex; gap:6px; flex-wrap:wrap;">
        <button class="btn btn-sm btn-primary crm-subtab-btn active" data-crm-tab="customers">👥 Customers Directory</button>
        <button class="btn btn-sm btn-secondary crm-subtab-btn" data-crm-tab="reservations">📅 Table Reservations</button>
        <button class="btn btn-sm btn-secondary crm-subtab-btn" data-crm-tab="loyalty">⭐ Loyalty & Ledger</button>
        <button class="btn btn-sm btn-secondary crm-subtab-btn" data-crm-tab="coupons">🎟️ Coupons & Promotions</button>
        <button class="btn btn-sm btn-secondary crm-subtab-btn" data-crm-tab="qr">📱 QR Code Tokens</button>
      </div>
    </div>
  </div>

  <!-- PANE 1: CUSTOMERS DIRECTORY -->
  <div id="crm-pane-customers" class="crm-tab-pane">
    <div class="card" style="margin-bottom:20px;">
      <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <div style="display:flex; gap:10px; flex-wrap:wrap;">
          <input type="text" id="crm-customer-search" class="form-control" style="width:260px;" placeholder="Search name, phone, email, code..." onkeyup="SmartCRM.loadCustomers()">
          <select id="crm-customer-status-filter" class="form-control" style="width:140px;" onchange="SmartCRM.loadCustomers()">
            <option value="">All Statuses</option>
            <option value="ACTIVE" selected>Active</option>
            <option value="INACTIVE">Inactive</option>
          </select>
        </div>
        <button class="btn btn-primary" onclick="SmartCRM.openCreateCustomerModal()">+ Register Customer</button>
      </div>
      <div class="table-responsive">
        <table class="data-table">
          <thead>
            <tr>
              <th>Code</th>
              <th>Customer Name</th>
              <th>Phone</th>
              <th>Email</th>
              <th>Status</th>
              <th>Loyalty Points</th>
              <th>Lifetime Sales</th>
              <th class="text-right">Actions</th>
            </tr>
          </thead>
          <tbody id="crm-customers-table-tbody">
            <tr><td colspan="8" class="text-center" style="padding:24px;">Loading customers...</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- PANE 2: TABLE RESERVATIONS -->
  <div id="crm-pane-reservations" class="crm-tab-pane" style="display:none;">
    <div class="card" style="margin-bottom:20px;">
      <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <div style="display:flex; gap:10px; flex-wrap:wrap;">
          <input type="date" id="crm-res-date-filter" class="form-control" style="width:170px;" onchange="SmartCRM.loadReservations()">
          <select id="crm-res-status-filter" class="form-control" style="width:160px;" onchange="SmartCRM.loadReservations()">
            <option value="">All Statuses</option>
            <option value="CONFIRMED" selected>Confirmed</option>
            <option value="SEATED">Seated</option>
            <option value="COMPLETED">Completed</option>
            <option value="CANCELLED">Cancelled</option>
          </select>
        </div>
        <button class="btn btn-primary" onclick="SmartCRM.openCreateReservationModal()">+ New Table Reservation</button>
      </div>
      <div class="table-responsive">
        <table class="data-table">
          <thead>
            <tr>
              <th>Res #</th>
              <th>Customer</th>
              <th>Scheduled Date/Time</th>
              <th>Guests</th>
              <th>Table & Zone</th>
              <th>Status</th>
              <th class="text-right">Actions</th>
            </tr>
          </thead>
          <tbody id="crm-reservations-table-tbody">
            <tr><td colspan="8" class="text-center" style="padding:24px;">Loading reservations...</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- PANE 3: LOYALTY & LEDGER -->
  <div id="crm-pane-loyalty" class="crm-tab-pane" style="display:none;">
    <div class="card">
      <div class="card-header">
        <h3>Customer Loyalty Points & Accounts</h3>
      </div>
      <div class="card-body">
        <p style="color:var(--text-muted); font-size:14px; margin-bottom:16px;">
          Loyalty points are automatically earned at 10% on completed orders. Points can be redeemed for direct order discounts or manually adjusted with an audit trail.
        </p>
        <div class="alert alert-info">
          Use the <strong>Customers Directory</strong> tab to view detailed loyalty account ledgers or perform manager points adjustments.
        </div>
      </div>
    </div>
  </div>

  <!-- PANE 4: COUPONS & PROMOTIONS -->
  <div id="crm-pane-coupons" class="crm-tab-pane" style="display:none;">
    <div class="card" style="margin-bottom:20px;">
      <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
        <h3>Active Promotional Coupons</h3>
        <button class="btn btn-primary" onclick="SmartCRM.openCreateCouponModal()">+ Create Coupon Code</button>
      </div>
      <div class="table-responsive">
        <table class="data-table">
          <thead>
            <tr>
              <th>Code</th>
              <th>Name</th>
              <th>Discount</th>
              <th>Rules</th>
              <th>Usage / Limit</th>
              <th>Status</th>
              <th>Valid Until</th>
            </tr>
          </thead>
          <tbody id="crm-coupons-table-tbody">
            <tr><td colspan="7" class="text-center" style="padding:24px;">Loading coupons...</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- PANE 5: QR CODE TOKENS -->
  <div id="crm-pane-qr" class="crm-tab-pane" style="display:none;">
    <div class="card" style="margin-bottom:20px;">
      <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
        <div>
          <h3>Secure Table QR Tokens</h3>
          <span class="text-sm">Cryptographically generated QR access tokens for customer table ordering.</span>
        </div>
        <button class="btn btn-secondary" onclick="SmartCRM.loadQRTables()">Refresh Tokens</button>
      </div>
      <div class="table-responsive">
        <table class="data-table">
          <thead>
            <tr>
              <th>Floor Zone</th>
              <th>Table Number</th>
              <th>Token Status</th>
              <th>Last Updated</th>
              <th>Public QR Link</th>
              <th class="text-right">Actions</th>
            </tr>
          </thead>
          <tbody id="crm-qr-table-tbody">
            <tr><td colspan="6" class="text-center" style="padding:24px;">Loading QR tokens...</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</section>
