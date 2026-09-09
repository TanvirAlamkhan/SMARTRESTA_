<?php
/**
 * SMARTRESTA - Main Platform Shell & Production System Architecture
 * Prompt 04 Compliance: Restaurant Structure, Floors, Tables, Operations & Dining Sessions Engine
 * Tech Stack: HTML5, CSS3, Vanilla ES6+ JS, PHP 8.x, MySQL 8.x PDO
 */

require_once __DIR__ . '/config/env.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/core/Auth.php';
require_once __DIR__ . '/core/CSRF.php';

Auth::requireAuth();

CSRF::init();
$csrfToken = CSRF::getToken();
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
  <title>SMARTRESTA — Restaurant Operations & Floor Management</title>

  <!-- Google Fonts: Poppins -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <!-- Modular CSS Architecture -->
  <link rel="stylesheet" href="assets/css/variables.css">
  <link rel="stylesheet" href="assets/css/reset.css">
  <link rel="stylesheet" href="assets/css/typography.css">
  <link rel="stylesheet" href="assets/css/layout.css">
  <link rel="stylesheet" href="assets/css/components.css">
  <link rel="stylesheet" href="assets/css/tables.css">
  <link rel="stylesheet" href="assets/css/forms.css">
  <link rel="stylesheet" href="assets/css/pos.css">
  <link rel="stylesheet" href="assets/css/kitchen.css">
  <link rel="stylesheet" href="assets/css/responsive.css">
  <link rel="stylesheet" href="assets/css/dark-mode.css">

  <style>
    .floor-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
      gap: var(--space-5);
      margin-top: var(--space-4);
    }
    .table-card {
      background-color: var(--surface);
      border: 2px solid var(--border);
      border-radius: var(--radius-lg);
      padding: var(--space-4);
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      gap: var(--space-3);
      transition: all var(--transition-fast);
      box-shadow: var(--shadow-sm);
    }
    .table-card.status-available { border-color: var(--success); }
    .table-card.status-occupied { border-color: var(--warning); background-color: var(--warning-light); }
    .table-card.status-reserved { border-color: var(--info); }
    .table-card.status-cleaning { border-color: var(--text-muted); }
    .table-card.status-out_of_service { border-color: var(--danger); opacity: 0.7; }
    
    .table-header-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .table-num {
      font-size: 1.25rem;
      font-weight: 700;
      color: var(--text-primary);
    }
  </style>
</head>
<body>

<div class="app-wrapper">

  <!-- Sidebar Navigation -->
  <aside class="sidebar">
    <div class="sidebar-header">
      <div class="brand-logo">
        <span>SMARTRESTA</span>
        <span class="logo-badge">OS</span>
      </div>
    </div>

    <nav class="sidebar-nav">
      <div class="nav-section-title">OPERATIONS</div>
      <a href="#admin-view" class="nav-item active" onclick="switchRoleView('admin-view', this)">
        <span class="nav-icon">📊</span>
        <span>Manager Dashboard</span>
      </a>
      <a href="#tables-view" class="nav-item" onclick="switchRoleView('tables-view', this)">
        <span class="nav-icon">🪑</span>
        <span>Floors & Dining Tables</span>
      </a>
      <a href="#menu-view" class="nav-item" onclick="switchRoleView('menu-view', this)">
        <span class="nav-icon">🍔</span>
        <span>Menu & Product Catalog</span>
      </a>
      <a href="#pos-view" class="nav-item" onclick="switchRoleView('pos-view', this)">
        <span class="nav-icon">💳</span>
        <span>POS & Waiter Ordering</span>
      </a>
      <a href="#kds-view" class="nav-item" onclick="switchRoleView('kds-view', this); SmartKDS.loadKDSGrid();">
        <span class="nav-icon">🍳</span>
        <span>Kitchen Display (KDS)</span>
      </a>
      <a href="#routing-view" class="nav-item" onclick="switchRoleView('routing-view', this); SmartRouting.loadStationQueue();">
        <span class="nav-icon">🔀</span>
        <span>Station Routing Engine</span>
      </a>
      <a href="#payments-view" class="nav-item" onclick="switchRoleView('payments-view', this); SmartBilling.loadPaymentHistory();">
        <span class="nav-icon">💰</span>
        <span>Billing & Payments History</span>
      </a>
      <a href="#commission-rules-view" class="nav-item" onclick="switchRoleView('commission-rules-view', this); SmartCommissions.loadCommissionRules();">
        <span class="nav-icon">📜</span>
        <span>Commission Rules Engine</span>
      </a>
      <a href="#commissions-review-view" class="nav-item" onclick="switchRoleView('commissions-review-view', this); SmartCommissions.loadCommissionsReview();">
        <span class="nav-icon">📑</span>
        <span>Commission Review & Approvals</span>
      </a>
      <a href="#payouts-view" class="nav-item" onclick="switchRoleView('payouts-view', this); SmartCommissions.loadPayoutHistory();">
        <span class="nav-icon">💵</span>
        <span>Commission Payout Settlements</span>
      </a>

      <div class="nav-section-title" style="margin-top:16px;">ADMIN & ACCESS</div>
      <a href="#users-view" class="nav-item" onclick="switchRoleView('users-view', this)">
        <span class="nav-icon">👥</span>
        <span>Users & Staff Roles</span>
      </a>
      <a href="#inventory-view" class="nav-item" onclick="switchRoleView('inventory-view', this); SmartInventory.init();">
        <span class="nav-icon">📦</span>
        <span>Stock & Ingredients</span>
      </a>
      <a href="#reports-view" class="nav-item" onclick="switchRoleView('reports-view', this); SmartReports.loadCurrentTab();">
        <span class="nav-icon">📈</span>
        <span>Reports & Analytics</span>
      </a>
      <a href="#finance-view" class="nav-item" onclick="switchRoleView('finance-view', this); SmartFinance.init();">
        <span class="nav-icon">💵</span>
        <span>Finance, Shifts & Day Close</span>
      </a>
      <a href="#crm-view" class="nav-item" onclick="switchRoleView('crm-view', this); SmartCRM.init();">
        <span class="nav-icon">🤝</span>
        <span>CRM & QR Ordering</span>
      </a>
    </nav>
  </aside>

  <!-- Main Content Canvas -->
  <main class="main-content">
    
    <!-- Topbar Header -->
    <header class="topbar">
      <div class="topbar-left">
        <button id="mobile-menu-btn" class="btn btn-secondary btn-icon" style="display:none;">☰</button>
        <div>
          <h1 id="view-title">Manager Overview</h1>
          <span class="text-sm">Branch: Main Outlet | Currency: ৳ (BDT)</span>
        </div>
      </div>

      <div class="topbar-right">
        <button id="theme-toggle-btn" class="btn btn-secondary">
          <span>🌙</span>
          <span>Toggle Dark Mode</span>
        </button>
        <button class="btn btn-secondary" onclick="handleLogout()">
          <span>🔒</span>
          <span>Sign Out</span>
        </button>
        <button class="btn btn-primary" onclick="SmartModal.open('new-order-modal')">
          <span>+</span>
          <span>Quick Reservation</span>
        </button>
      </div>
    </header>

    <!-- Dynamic View Container -->
    <div class="page-container">

      <!-- VIEW 1: ADMIN / MANAGER DASHBOARD -->
      <section id="admin-view" class="role-view">
        <div class="grid-kpi" id="kpi-container">
          <div class="kpi-card">
            <span class="kpi-label">Today Net Sales</span>
            <div class="kpi-value" id="kpi-sales">৳0.00</div>
            <span class="kpi-trend positive">Live Database Data</span>
          </div>

          <div class="kpi-card">
            <span class="kpi-label">Active Tables</span>
            <div class="kpi-value" id="kpi-tables">0 / 0</div>
            <span class="kpi-trend positive">Floor Status</span>
          </div>

          <div class="kpi-card">
            <span class="kpi-label">Orders Processed</span>
            <div class="kpi-value" id="kpi-orders">0</div>
            <span class="kpi-trend positive">Shift Total</span>
          </div>

          <div class="kpi-card">
            <span class="kpi-label">Waiter Commission Owed</span>
            <div class="kpi-value" id="kpi-commission">৳0.00</div>
            <span class="kpi-trend negative">5.0% Base Rate</span>
          </div>
        </div>

        <div class="card" style="margin-top: 24px;">
          <div class="card-header">
            <div>
              <h3>Waiter Performance & Commission Matrix</h3>
              <p class="text-sm">Real-time order throughput, sales volume, and commission breakdown from MySQL</p>
            </div>
            <button class="btn btn-secondary btn-sm" onclick="loadWaiterMatrix()">Refresh Data</button>
          </div>

          <div class="table-container">
            <table class="data-table">
              <thead>
                <tr>
                  <th>WAITER</th>
                  <th>ORDERS</th>
                  <th>TABLES SERVED</th>
                  <th>TOTAL SALES</th>
                  <th>PAID</th>
                  <th>PENDING</th>
                  <th>COMMISSION (5%)</th>
                  <th>STATUS</th>
                </tr>
              </thead>
              <tbody id="waiter-matrix-tbody">
                <tr><td colspan="8" style="text-align:center; padding: 40px; color: var(--text-muted);">Loading waiter performance data...</td></tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="card" style="margin-top: 24px;">
          <div class="card-header">
            <h3>Live Orders & Multi-Counter Station Routing</h3>
            <button class="btn btn-secondary btn-sm" onclick="loadActiveOrders()">Refresh Orders</button>
          </div>

          <div class="table-container">
            <table class="data-table">
              <thead>
                <tr>
                  <th>ORDER ID</th>
                  <th>TABLE</th>
                  <th>WAITER</th>
                  <th>ROUTED STATIONS</th>
                  <th>TOTAL</th>
                  <th>PAYMENT</th>
                  <th>STATUS</th>
                  <th>ACTION</th>
                </tr>
              </thead>
              <tbody id="orders-tbody">
                <tr><td colspan="8" style="text-align:center; padding: 40px; color: var(--text-muted);">Loading active orders from database...</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- VIEW 2: RESTAURANT FLOORS & DINING TABLES MAP -->
      <section id="tables-view" class="role-view" style="display:none;">
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

      <!-- VIEW: RESTAURANT MENU & PRODUCT CATALOG -->
      <section id="menu-view" class="role-view" style="display:none;">
        <div class="card">
          <div class="card-header">
            <div>
              <h3>Restaurant Menu, Categories & Product Catalog</h3>
              <p class="text-sm">Manage sellable items, base prices, variants, extra modifiers, availability & station routing</p>
            </div>
            <div style="display:flex; gap:8px;">
              <button class="btn btn-secondary btn-sm" onclick="SmartModal.open('create-category-modal')">+ Create Category</button>
              <button class="btn btn-secondary btn-sm" onclick="SmartModal.open('create-modifier-modal')">+ Create Modifier</button>
              <button class="btn btn-primary btn-sm" onclick="SmartModal.open('create-product-modal')">+ Create Product</button>
            </div>
          </div>

          <!-- Filters Bar -->
          <div style="padding: 16px var(--space-4); display:flex; gap:12px; align-items:center; flex-wrap:wrap; border-bottom: 1px solid var(--border);">
            <input type="text" id="product-search-input" class="form-control" style="max-width:240px;" placeholder="Search SKU or name..." oninput="loadProductCatalog()">
            <select id="product-category-filter" class="form-control" style="max-width:200px;" onchange="loadProductCatalog()">
              <option value="">All Categories</option>
            </select>
            <select id="product-status-filter" class="form-control" style="max-width:160px;" onchange="loadProductCatalog()">
              <option value="">All Availability</option>
              <option value="1">Available Only</option>
              <option value="0">Unavailable Only</option>
            </select>
            <button class="btn btn-secondary btn-sm" onclick="loadProductCatalog()">Refresh Catalog</button>
          </div>

          <div class="table-container">
            <table class="data-table">
              <thead>
                <tr>
                  <th>PRODUCT NAME</th>
                  <th>CATEGORY</th>
                  <th>SKU</th>
                  <th>BASE PRICE</th>
                  <th>DEFAULT STATION</th>
                  <th>VARIANTS</th>
                  <th>AVAILABILITY</th>
                  <th>STATUS</th>
                  <th>ACTIONS</th>
                </tr>
              </thead>
              <tbody id="products-table-tbody">
                <tr><td colspan="9" style="text-align:center; padding: 40px; color: var(--text-muted);">Loading product catalog...</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- VIEW 3: POS & WAITER ORDERING -->
      <section id="pos-view" class="role-view" style="display:none;">
        <div class="pos-layout">
          <div>
            <div class="category-slider">
              <button class="category-pill active">All Items</button>
              <button class="category-pill">Main Dishes</button>
              <button class="category-pill">Fast Food</button>
              <button class="category-pill">Beverages / Bar</button>
              <button class="category-pill">Desserts</button>
            </div>

            <div class="product-grid">
              <div class="product-card" onclick="SmartPOS.addItem('p1', 'Chicken Cheeseburger', 320, 'KITCHEN')">
                <div class="product-title">Chicken Cheeseburger</div>
                <div class="text-sm">Classic grilled chicken with melted cheese</div>
                <div class="product-meta">
                  <span class="price-tag">320</span>
                  <span class="product-add-btn">+</span>
                </div>
              </div>

              <div class="product-card" onclick="SmartPOS.addItem('p2', 'Kacchi Biryani', 450, 'KITCHEN')">
                <div class="product-title">Beef Kacchi Biryani</div>
                <div class="text-sm">Aromatic mutton & basmati rice</div>
                <div class="product-meta">
                  <span class="price-tag">450</span>
                  <span class="product-add-btn">+</span>
                </div>
              </div>
            </div>
          </div>

          <div class="pos-cart">
            <div class="cart-header">
              <h3>Table T-12 Order</h3>
              <span class="badge badge-info">Waiter: Assigned</span>
            </div>

            <div id="pos-cart-items" class="cart-items-list">
              <div style="text-align:center; padding: 40px var(--space-4); color: var(--text-muted);">
                <div style="font-size: 2rem; margin-bottom: 8px;">🛒</div>
                <p style="font-size:0.875rem;">No items in current order</p>
              </div>
            </div>

            <div class="cart-footer">
              <div class="cart-summary-row">
                <span>Subtotal</span>
                <span id="cart-subtotal">৳0.00</span>
              </div>
              <div class="cart-summary-row">
                <span>VAT (5%)</span>
                <span id="cart-tax">৳0.00</span>
              </div>
              <div class="cart-summary-row total">
                <span>Total Amount</span>
                <span id="cart-total" class="price-tag">0.00</span>
              </div>

              <div style="margin-top: 8px;">
                <span class="text-uppercase-label">Select Payment Method</span>
                <div class="payment-methods-grid">
                  <div class="payment-method-btn active" data-method="Cash" onclick="SmartPOS.setPaymentMethod('Cash')">Cash</div>
                  <div class="payment-method-btn" data-method="bKash" onclick="SmartPOS.setPaymentMethod('bKash')">bKash</div>
                  <div class="payment-method-btn" data-method="Nagad" onclick="SmartPOS.setPaymentMethod('Nagad')">Nagad</div>
                </div>
              </div>

              <button class="btn btn-primary btn-lg" style="width:100%; margin-top:8px;" onclick="SmartPOS.submitOrder()">
                Submit & Route Order
              </button>
            </div>
          </div>
        </div>
      </section>

      <!-- VIEW 4: KITCHEN DISPLAY SYSTEM (KDS) -->
      <section id="kds-view" class="role-view" style="display:none;">
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

      <!-- VIEW 5: USER & ROLE MANAGEMENT -->
      <section id="users-view" class="role-view" style="display:none;">
        <div class="card">
          <div class="card-header">
            <div>
              <h3>User Accounts & Role Permissions Matrix</h3>
              <p class="text-sm">Manage restaurant staff accounts, Bcrypt authentication, and RBAC permissions</p>
            </div>
            <button class="btn btn-primary btn-sm" onclick="SmartModal.open('create-user-modal')">+ Create Staff Account</button>
          </div>

          <div class="table-container">
            <table class="data-table">
              <thead>
                <tr>
                  <th>USER NAME</th>
                  <th>EMAIL</th>
                  <th>PHONE</th>
                  <th>ROLE</th>
                  <th>LAST LOGIN</th>
                  <th>ACCOUNT STATUS</th>
                  <th>ACTION</th>
                </tr>
              </thead>
              <tbody id="users-table-tbody">
                <tr><td colspan="7" style="text-align:center; padding: 40px; color: var(--text-muted);">Loading user accounts...</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>

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

      <!-- VIEW: BILLING & PAYMENT SETTLEMENT HISTORY -->
      <section id="payments-view" class="role-view" style="display:none;">
        <div class="card">
          <div class="card-header">
            <div>
              <h3>Billing & Settlement History</h3>
              <p class="text-sm">Real-time payment transactions, receipts, invoices, split payment allocations, and refund processing</p>
            </div>
            <button class="btn btn-secondary btn-sm" onclick="SmartBilling.loadPaymentHistory()">Refresh Payments</button>
          </div>

          <div class="table-container">
            <table class="data-table">
              <thead>
                <tr>
                  <th>PAYMENT ID</th>
                  <th>ORDER / SESSION</th>
                  <th>METHOD</th>
                  <th>AMOUNT</th>
                  <th>STATUS</th>
                  <th>TRANSACTION REF</th>
                  <th>DATE</th>
                  <th>ACTIONS</th>
                </tr>
              </thead>
              <tbody id="payments-table-tbody">
                <tr><td colspan="8" style="text-align:center; padding: 40px; color: var(--text-muted);">Loading payment transaction history...</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- VIEW: COMMISSION RULES CONFIGURATION -->
      <section id="commission-rules-view" class="role-view" style="display:none;">
        <div class="card">
          <div class="card-header">
            <div>
              <h3>Configurable Commission Rules Engine</h3>
              <p class="text-sm">Manage waiter commission bases (Percentage of Net Sales, Per Order, Per Item, Fixed Amount)</p>
            </div>
            <button class="btn btn-primary btn-sm" onclick="SmartModal.open('create-commission-rule-modal')">+ Create Commission Rule</button>
          </div>

          <div class="table-container">
            <table class="data-table">
              <thead>
                <tr>
                  <th>RULE NAME</th>
                  <th>CALCULATION BASE</th>
                  <th>RATE (%)</th>
                  <th>FIXED AMOUNT</th>
                  <th>MINIMUM SALES</th>
                  <th>ELIGIBILITY</th>
                  <th>STATUS</th>
                </tr>
              </thead>
              <tbody id="rules-table-tbody">
                <tr><td colspan="7" style="text-align:center; padding: 40px; color: var(--text-muted);">Loading commission rules...</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- VIEW: COMMISSION TRANSACTION REVIEW & APPROVALS -->
      <section id="commissions-review-view" class="role-view" style="display:none;">
        <div class="card">
          <div class="card-header">
            <div>
              <h3>Commission Transaction Review & Approvals</h3>
              <p class="text-sm">Manager review, approval, rejection, and historical audit for waiter commissions</p>
            </div>
            <div style="display:flex; gap:8px;">
              <select id="comm-status-filter" class="form-control" style="max-width:160px;" onchange="SmartCommissions.loadCommissionsReview()">
                <option value="">All Statuses</option>
                <option value="PENDING" selected>PENDING Only</option>
                <option value="APPROVED">APPROVED Only</option>
                <option value="PAID">PAID Only</option>
                <option value="REJECTED">REJECTED Only</option>
              </select>
              <button class="btn btn-success btn-sm" onclick="SmartCommissions.approveSelectedCommissions()">Approve Selected</button>
              <button class="btn btn-danger btn-sm" onclick="SmartCommissions.rejectSelectedCommissions()">Reject Selected</button>
            </div>
          </div>

          <div class="table-container">
            <table class="data-table">
              <thead>
                <tr>
                  <th><input type="checkbox" disabled></th>
                  <th>COMMISSION ID</th>
                  <th>WAITER</th>
                  <th>ORDER NUMBER</th>
                  <th>BASE AMOUNT</th>
                  <th>APPLIED RULE</th>
                  <th>COMMISSION</th>
                  <th>STATUS</th>
                  <th>DATE</th>
                </tr>
              </thead>
              <tbody id="commissions-review-tbody">
                <tr><td colspan="9" style="text-align:center; padding: 40px; color: var(--text-muted);">Loading commission review list...</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- VIEW: COMMISSION PAYOUT SETTLEMENTS -->
      <section id="payouts-view" class="role-view" style="display:none;">
        <div class="card">
          <div class="card-header">
            <div>
              <h3>Commission Payout Settlements History</h3>
              <p class="text-sm">Manager processed waiter payroll payout settlements with itemized transaction links</p>
            </div>
            <button class="btn btn-secondary btn-sm" onclick="SmartCommissions.loadPayoutHistory()">Refresh Payouts</button>
          </div>

          <div class="table-container">
            <table class="data-table">
              <thead>
                <tr>
                  <th>PAYOUT NUMBER</th>
                  <th>WAITER</th>
                  <th>AMOUNT PAID</th>
                  <th>PAYMENT METHOD</th>
                  <th>REFERENCE</th>
                  <th>PROCESSED BY</th>
                  <th>STATUS</th>
                  <th>DATE</th>
                </tr>
              </thead>
              <tbody id="payouts-table-tbody">
                <tr><td colspan="8" style="text-align:center; padding: 40px; color: var(--text-muted);">Loading payout settlement history...</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- VIEW: INVENTORY & STOCK CONTROL -->
      <section id="inventory-view" class="role-view" style="display:none;">
        <!-- Inventory Top Bar Sub-Navigation -->
        <div class="card" style="margin-bottom:20px; padding:16px 20px;">
          <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
            <div id="inventory-tabs-nav" style="display:flex; gap:8px; flex-wrap:wrap;">
              <button id="inv-tab-btn-stock" class="btn btn-sm btn-primary active-filter" onclick="SmartInventory.switchSubTab('stock')">📦 Ingredients & Stock Balances</button>
              <button id="inv-tab-btn-recipes" class="btn btn-sm btn-secondary" onclick="SmartInventory.switchSubTab('recipes')">🍳 Recipe BOM & Costing</button>
              <button id="inv-tab-btn-purchases" class="btn btn-sm btn-secondary" onclick="SmartInventory.switchSubTab('purchases')">📑 Purchase Orders</button>
              <button id="inv-tab-btn-suppliers" class="btn btn-sm btn-secondary" onclick="SmartInventory.switchSubTab('suppliers')">🏬 Suppliers</button>
              <button id="inv-tab-btn-wastage" class="btn btn-sm btn-secondary" onclick="SmartInventory.switchSubTab('wastage')">🗑️ Stock Wastage Logs</button>
              <button id="inv-tab-btn-ledger" class="btn btn-sm btn-secondary" onclick="SmartInventory.switchSubTab('ledger')">📜 Audit Ledger</button>
            </div>
            <div>
              <button class="btn btn-sm btn-primary" onclick="SmartInventory.openAddIngredientModal()">+ Add Ingredient</button>
              <button class="btn btn-sm btn-secondary" onclick="SmartInventory.openCreatePOModal()">+ Create PO</button>
            </div>
          </div>
        </div>

        <!-- SECTION 1: INGREDIENTS & STOCK BALANCES -->
        <div id="inv-section-stock" class="card">
          <div class="card-header">
            <div>
              <h3>Raw Ingredients & Stock Balances</h3>
              <p class="text-sm">Real-time database stock levels across store locations with weighted average costing</p>
            </div>
            <button class="btn btn-secondary btn-sm" onclick="SmartInventory.loadStockBalances()">Refresh Stock</button>
          </div>
          <div class="table-container">
            <table class="data-table">
              <thead>
                <tr>
                  <th>INGREDIENT</th>
                  <th>CATEGORY</th>
                  <th>TOTAL STOCK</th>
                  <th>AVG COST / UOM</th>
                  <th>MIN ALERT</th>
                  <th>STATUS</th>
                  <th>ACTIONS</th>
                </tr>
              </thead>
              <tbody id="ingredients-table-body">
                <tr><td colspan="7" class="text-center text-muted" style="padding:40px;">Loading stock balances from database...</td></tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- SECTION 2: RECIPE BOM & COSTING -->
        <div id="inv-section-recipes" class="card" style="display:none;">
          <div class="card-header">
            <div>
              <h3>Recipe Bill of Materials (BOM) & Profit Margin Analysis</h3>
              <p class="text-sm">Ingredient portioning breakdown, live recipe costing, and food margin analysis</p>
            </div>
            <button class="btn btn-secondary btn-sm" onclick="SmartInventory.loadRecipesList()">Refresh Recipes</button>
          </div>
          <div class="table-container">
            <table class="data-table">
              <thead>
                <tr>
                  <th>PRODUCT / MENU ITEM</th>
                  <th>BOM INGREDIENTS</th>
                  <th>YIELD</th>
                  <th>RECIPE COST</th>
                  <th>SELLING PRICE</th>
                  <th>FOOD MARGIN %</th>
                  <th>ACTIONS</th>
                </tr>
              </thead>
              <tbody id="recipes-table-body">
                <tr><td colspan="7" class="text-center text-muted" style="padding:40px;">Loading recipes list from database...</td></tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- SECTION 3: PURCHASE ORDERS -->
        <div id="inv-section-purchases" class="card" style="display:none;">
          <div class="card-header">
            <div>
              <h3>Purchase Orders & Goods Receiving Lifecycle</h3>
              <p class="text-sm">Raise POs, approve supplier orders, and receive stock into inventory</p>
            </div>
            <button class="btn btn-primary btn-sm" onclick="SmartInventory.openCreatePOModal()">+ Create Purchase Order</button>
          </div>
          <div class="table-container">
            <table class="data-table">
              <thead>
                <tr>
                  <th>PO NUMBER</th>
                  <th>SUPPLIER</th>
                  <th>DESTINATION LOCATION</th>
                  <th>TOTAL AMOUNT</th>
                  <th>STATUS</th>
                  <th>DATE CREATED</th>
                  <th>ACTIONS</th>
                </tr>
              </thead>
              <tbody id="purchases-table-body">
                <tr><td colspan="7" class="text-center text-muted" style="padding:40px;">Loading purchase orders from database...</td></tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- SECTION 4: SUPPLIERS -->
        <div id="inv-section-suppliers" class="card" style="display:none;">
          <div class="card-header">
            <div>
              <h3>Approved Suppliers Directory</h3>
              <p class="text-sm">Vendor contacts, terms, and purchase order history</p>
            </div>
            <button class="btn btn-primary btn-sm" onclick="SmartInventory.openAddSupplierModal()">+ Add Supplier</button>
          </div>
          <div class="table-container">
            <table class="data-table">
              <thead>
                <tr>
                  <th>SUPPLIER NAME</th>
                  <th>PHONE</th>
                  <th>EMAIL</th>
                  <th>ADDRESS</th>
                  <th>STATUS</th>
                  <th>ACTIONS</th>
                </tr>
              </thead>
              <tbody id="suppliers-table-body">
                <tr><td colspan="6" class="text-center text-muted" style="padding:40px;">Loading suppliers from database...</td></tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- SECTION 5: WASTAGE LOGS -->
        <div id="inv-section-wastage" class="card" style="display:none;">
          <div class="card-header">
            <div>
              <h3>Recorded Inventory Wastage Logs</h3>
              <p class="text-sm">Track kitchen spoilage, damage, and expired item write-offs with cost impact</p>
            </div>
            <button class="btn btn-secondary btn-sm" onclick="SmartInventory.loadWastageLogs()">Refresh Wastage</button>
          </div>
          <div class="table-container">
            <table class="data-table">
              <thead>
                <tr>
                  <th>ITEM</th>
                  <th>LOCATION</th>
                  <th>QUANTITY WASTED</th>
                  <th>TOTAL COST IMPACT</th>
                  <th>REASON</th>
                  <th>TIMESTAMP</th>
                </tr>
              </thead>
              <tbody id="wastage-table-body">
                <tr><td colspan="6" class="text-center text-muted" style="padding:40px;">Loading wastage logs...</td></tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- SECTION 6: AUDIT LEDGER -->
        <div id="inv-section-ledger" class="card" style="display:none;">
          <div class="card-header">
            <div>
              <h3>Immutable Inventory Audit Ledger</h3>
              <p class="text-sm">Full audit trail of all stock movements, PO receipts, order deductions, and adjustments</p>
            </div>
            <button class="btn btn-secondary btn-sm" onclick="SmartInventory.loadLedgerTransactions()">Refresh Ledger</button>
          </div>
          <div class="table-container">
            <table class="data-table">
              <thead>
                <tr>
                  <th>DATE & TIME</th>
                  <th>TYPE</th>
                  <th>INGREDIENT</th>
                  <th>LOCATION</th>
                  <th>QUANTITY CHANGE</th>
                  <th>POST BALANCE</th>
                  <th>REFERENCE</th>
                </tr>
              </thead>
              <tbody id="ledger-table-body">
                <tr><td colspan="7" class="text-center text-muted" style="padding:40px;">Loading transaction ledger logs...</td></tr>
              </tbody>
            </table>
          </div>
        </div>

      </section>

      <!-- VIEW: REPORTS & ANALYTICS DASHBOARD (Prompt 12) -->
      <section id="reports-view" class="role-view" style="display:none;">

        <!-- Global Date/Time Range Filter Toolbar -->
        <div class="card" style="margin-bottom:20px; padding:16px 20px;">
          <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
            <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
              <label class="form-label" style="margin:0; font-weight:600;">📅 Period:</label>
              <select id="report-date-preset" class="form-control" style="max-width:180px;">
                <option value="today" selected>Today</option>
                <option value="yesterday">Yesterday</option>
                <option value="this_week">This Week</option>
                <option value="last_week">Last Week</option>
                <option value="this_month">This Month</option>
                <option value="last_month">Last Month</option>
                <option value="this_year">This Year</option>
                <option value="custom">Custom Range</option>
              </select>
              <div id="report-custom-date-box" style="display:none; gap:8px; align-items:center;">
                <input type="date" id="report-date-from" class="form-control" style="max-width:160px;">
                <span>to</span>
                <input type="date" id="report-date-to" class="form-control" style="max-width:160px;">
                <button id="btn-apply-custom-date" class="btn btn-sm btn-primary">Apply</button>
              </div>
            </div>
            <div style="display:flex; gap:8px;">
              <button class="btn btn-sm btn-secondary" onclick="SmartReports.loadCurrentTab()">🔄 Refresh</button>
              <button class="btn btn-sm btn-success" onclick="SmartReports.exportReport(SmartReports.currentTab)">📥 Export CSV</button>
            </div>
          </div>
        </div>

        <!-- Report Tab Navigation -->
        <div class="card" style="margin-bottom:20px; padding:12px 20px;">
          <div style="display:flex; gap:6px; flex-wrap:wrap;">
            <button class="btn btn-sm btn-primary report-tab-btn active" data-tab="overview">📊 Overview</button>
            <button class="btn btn-sm btn-secondary report-tab-btn" data-tab="sales">💰 Sales</button>
            <button class="btn btn-sm btn-secondary report-tab-btn" data-tab="orders">📋 Orders</button>
            <button class="btn btn-sm btn-secondary report-tab-btn" data-tab="waiters">👤 Waiters</button>
            <button class="btn btn-sm btn-secondary report-tab-btn" data-tab="tables">🪑 Tables</button>
            <button class="btn btn-sm btn-secondary report-tab-btn" data-tab="products">🍔 Products</button>
            <button class="btn btn-sm btn-secondary report-tab-btn" data-tab="payments">💳 Payments</button>
            <button class="btn btn-sm btn-secondary report-tab-btn" data-tab="kds">🍳 KDS Stations</button>
            <button class="btn btn-sm btn-secondary report-tab-btn" data-tab="inventory">📦 Inventory</button>
          </div>
        </div>

        <!-- TAB: Overview Dashboard -->
        <div id="tab-overview-content" class="report-tab-pane">
          <!-- KPI Cards -->
          <div class="grid-kpi" style="margin-bottom:20px;">
            <div class="kpi-card"><span class="kpi-label">Gross Sales</span><div class="kpi-value" id="kpi-gross-sales">৳0.00</div></div>
            <div class="kpi-card"><span class="kpi-label">Net Sales</span><div class="kpi-value" id="kpi-net-sales">৳0.00</div></div>
            <div class="kpi-card"><span class="kpi-label">Total Orders</span><div class="kpi-value" id="kpi-total-orders">0</div></div>
            <div class="kpi-card"><span class="kpi-label">Avg Order Value</span><div class="kpi-value" id="kpi-aov">৳0.00</div></div>
            <div class="kpi-card"><span class="kpi-label">Paid Amount</span><div class="kpi-value" id="kpi-paid-amount">৳0.00</div></div>
            <div class="kpi-card"><span class="kpi-label">Pending Amount</span><div class="kpi-value" id="kpi-pending-amount">৳0.00</div></div>
          </div>
          <!-- Operational Alerts -->
          <div class="card">
            <div class="card-header"><h3>⚠️ Operational Alerts</h3></div>
            <div style="padding:16px; display:flex; gap:16px; flex-wrap:wrap;">
              <div class="kpi-card" style="border-left:4px solid var(--danger); cursor:pointer;" onclick="document.querySelector('[data-tab=inventory]').click()">
                <span class="kpi-label">Low Stock</span><div class="kpi-value" id="alert-low-stock" style="color:var(--danger)">0 Items</div>
              </div>
              <div class="kpi-card" style="border-left:4px solid var(--warning); cursor:pointer;" onclick="document.querySelector('[data-tab=orders]').click()">
                <span class="kpi-label">Unpaid Orders</span><div class="kpi-value" id="alert-unpaid-orders" style="color:var(--warning)">0 Orders</div>
              </div>
              <div class="kpi-card" style="border-left:4px solid var(--danger); cursor:pointer;" onclick="document.querySelector('[data-tab=kds]').click()">
                <span class="kpi-label">Delayed KDS</span><div class="kpi-value" id="alert-delayed-kds" style="color:var(--danger)">0 Tickets</div>
              </div>
              <div class="kpi-card" style="border-left:4px solid var(--info); cursor:pointer;">
                <span class="kpi-label">Pending Commissions</span><div class="kpi-value" id="alert-pending-comm" style="color:var(--info)">0 Pending</div>
              </div>
            </div>
          </div>
        </div>

        <!-- TAB: Sales Analytics -->
        <div id="tab-sales-content" class="report-tab-pane" style="display:none;">
          <div class="card">
            <div class="card-header"><h3>💰 Sales Analytics Breakdown</h3></div>
            <div class="table-container"><table class="data-table">
              <thead><tr><th>Period</th><th>Orders</th><th>Gross Sales</th><th>Discounts</th><th>Tax</th><th>Net Sales</th></tr></thead>
              <tbody id="table-sales-body"><tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted)">Select a date range and click Refresh.</td></tr></tbody>
            </table></div>
          </div>
        </div>

        <!-- TAB: Orders Report -->
        <div id="tab-orders-content" class="report-tab-pane" style="display:none;">
          <div class="card" style="margin-bottom:16px; padding:12px 20px;">
            <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
              <select id="report-order-status-filter" class="form-control" style="max-width:180px;">
                <option value="">All Statuses</option>
                <option value="DRAFT">Draft</option>
                <option value="SUBMITTED">Submitted</option>
                <option value="PREPARING">Preparing</option>
                <option value="READY">Ready</option>
                <option value="SERVED">Served</option>
                <option value="COMPLETED">Completed</option>
                <option value="CANCELLED">Cancelled</option>
                <option value="REFUNDED">Refunded</option>
              </select>
              <input type="text" id="report-order-search" class="form-control" style="max-width:220px;" placeholder="Search order #, waiter, table...">
            </div>
          </div>
          <div class="card">
            <div class="card-header"><h3>📋 Orders Performance Report</h3></div>
            <div class="table-container"><table class="data-table">
              <thead><tr><th>Order #</th><th>Date</th><th>Type</th><th>Table</th><th>Waiter</th><th>Total</th><th>Payment</th><th>Status</th><th>Actions</th></tr></thead>
              <tbody id="table-orders-body"><tr><td colspan="9" style="text-align:center;padding:40px;color:var(--text-muted)">Loading...</td></tr></tbody>
            </table></div>
            <div id="orders-pagination-controls" style="padding:12px 20px;"></div>
          </div>
        </div>

        <!-- TAB: Waiter Performance -->
        <div id="tab-waiters-content" class="report-tab-pane" style="display:none;">
          <div class="card">
            <div class="card-header"><h3>👤 Waiter Performance & Commission Report</h3></div>
            <div class="table-container"><table class="data-table">
              <thead><tr><th>Waiter</th><th>Orders</th><th>Tables</th><th>Total Sales</th><th>AOV</th><th>Pending Comm.</th><th>Approved Comm.</th><th>Paid Comm.</th></tr></thead>
              <tbody id="table-waiters-body"><tr><td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted)">Loading...</td></tr></tbody>
            </table></div>
          </div>
        </div>

        <!-- TAB: Table Performance -->
        <div id="tab-tables-content" class="report-tab-pane" style="display:none;">
          <div class="card">
            <div class="card-header"><h3>🪑 Table & Dining Session Report</h3></div>
            <div class="table-container"><table class="data-table">
              <thead><tr><th>Table (Floor)</th><th>Capacity</th><th>Sessions</th><th>Guests</th><th>Total Sales</th><th>Avg Spend/Session</th><th>Avg Duration</th></tr></thead>
              <tbody id="table-tables-body"><tr><td colspan="7" style="text-align:center;padding:40px;color:var(--text-muted)">Loading...</td></tr></tbody>
            </table></div>
          </div>
        </div>

        <!-- TAB: Product Performance -->
        <div id="tab-products-content" class="report-tab-pane" style="display:none;">
          <div class="card">
            <div class="card-header"><h3>🍔 Product Sales & Recipe Margin Analysis</h3></div>
            <div class="table-container"><table class="data-table">
              <thead><tr><th>Product</th><th>Category</th><th>Units Sold</th><th>Gross Sales</th><th>Recipe Cost/Unit</th><th>Total Cost</th><th>Gross Profit</th><th>Margin %</th></tr></thead>
              <tbody id="table-products-body"><tr><td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted)">Loading...</td></tr></tbody>
            </table></div>
          </div>
        </div>

        <!-- TAB: Payment Reconciliation -->
        <div id="tab-payments-content" class="report-tab-pane" style="display:none;">
          <div class="card" style="margin-bottom:20px;">
            <div class="card-header"><h3>💳 Payment Method Reconciliation</h3></div>
            <div class="table-container"><table class="data-table">
              <thead><tr><th>Payment Method</th><th>Transactions</th><th>Collected</th><th>Refunded</th><th>Net Collected</th></tr></thead>
              <tbody id="table-payment-methods-body"><tr><td colspan="5" style="text-align:center;padding:40px;color:var(--text-muted)">Loading...</td></tr></tbody>
            </table></div>
          </div>
          <div class="card">
            <div class="card-header"><h3>⚠️ Outstanding Unpaid Orders</h3></div>
            <div class="table-container"><table class="data-table">
              <thead><tr><th>Order #</th><th>Table</th><th>Total</th><th>Paid</th><th>Outstanding</th><th>Status</th></tr></thead>
              <tbody id="table-unpaid-orders-body"><tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted)">Loading...</td></tr></tbody>
            </table></div>
          </div>
        </div>

        <!-- TAB: KDS Station Report -->
        <div id="tab-kds-content" class="report-tab-pane" style="display:none;">
          <div class="card">
            <div class="card-header"><h3>🍳 KDS Station Production & SLA Report</h3></div>
            <div class="table-container"><table class="data-table">
              <thead><tr><th>Station</th><th>Total Tickets</th><th>Completed</th><th>Cancelled</th><th>Avg Prep Time</th><th>SLA Status</th></tr></thead>
              <tbody id="table-kds-body"><tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted)">Loading...</td></tr></tbody>
            </table></div>
          </div>
        </div>

        <!-- TAB: Inventory Report -->
        <div id="tab-inventory-content" class="report-tab-pane" style="display:none;">
          <div style="display:flex; gap:16px; margin-bottom:20px; flex-wrap:wrap;">
            <div class="kpi-card"><span class="kpi-label">Total Ingredients</span><div class="kpi-value" id="report-inv-val-count">0</div></div>
            <div class="kpi-card"><span class="kpi-label">Total Stock Value</span><div class="kpi-value" id="report-inv-val-total">৳0.00</div></div>
          </div>
          <div class="card" style="margin-bottom:20px;">
            <div class="card-header"><h3>🗑️ Wastage Breakdown by Reason</h3></div>
            <div class="table-container"><table class="data-table">
              <thead><tr><th>Reason</th><th>Records</th><th>Total Cost</th></tr></thead>
              <tbody id="table-inv-wastage-body"><tr><td colspan="3" style="text-align:center;padding:40px;color:var(--text-muted)">Loading...</td></tr></tbody>
            </table></div>
          </div>
          <div class="card">
            <div class="card-header"><h3>📦 Ingredient Consumption Summary</h3></div>
            <div class="table-container"><table class="data-table">
              <thead><tr><th>Ingredient</th><th>Consumed Qty</th><th>Estimated Cost</th></tr></thead>
              <tbody id="table-inv-cons-body"><tr><td colspan="3" style="text-align:center;padding:40px;color:var(--text-muted)">Loading...</td></tr></tbody>
            </table></div>
          </div>
        </div>

      </section>

      <!-- VIEW: FINANCE, SHIFTS & DAY CLOSING (Prompt 13) -->
      <section id="finance-view" class="role-view" style="display:none;">

        <!-- Active Shift Banner -->
        <div id="active-shift-banner" style="margin-bottom:20px;"></div>

        <!-- Finance Navigation Tabs -->
        <div class="card" style="margin-bottom:20px; padding:12px 20px;">
          <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
            <div style="display:flex; gap:6px; flex-wrap:wrap;">
              <button class="btn btn-sm btn-primary finance-tab-btn active" data-tab="overview">📊 Finance Overview</button>
              <button class="btn btn-sm btn-secondary finance-tab-btn" data-tab="expenses">💸 Operating Expenses</button>
              <button class="btn btn-sm btn-secondary finance-tab-btn" data-tab="shifts">🔑 Shifts & Cash Drawers</button>
              <button class="btn btn-sm btn-secondary finance-tab-btn" data-tab="dayclosing">📅 Day Closing Audit</button>
            </div>
            <div>
              <span class="text-sm text-muted">Day Status: </span>
              <span class="badge badge-success" id="fin-day-status-badge">OPEN</span>
            </div>
          </div>
        </div>

        <!-- TAB: Overview -->
        <div id="tab-fin-overview-content" class="finance-tab-pane">
          <div class="grid-kpi" style="margin-bottom:20px;">
            <div class="kpi-card"><span class="kpi-label">Gross Sales</span><div class="kpi-value" id="fin-kpi-gross">৳0.00</div></div>
            <div class="kpi-card"><span class="kpi-label">Net Revenue</span><div class="kpi-value" id="fin-kpi-net">৳0.00</div></div>
            <div class="kpi-card"><span class="kpi-label">Cash Collected</span><div class="kpi-value" id="fin-kpi-cash" style="color:var(--success)">৳0.00</div></div>
            <div class="kpi-card"><span class="kpi-label">Non-Cash (MFS/Card)</span><div class="kpi-value" id="fin-kpi-noncash" style="color:var(--info)">৳0.00</div></div>
            <div class="kpi-card"><span class="kpi-label">Operating Expenses</span><div class="kpi-value" id="fin-kpi-exp" style="color:var(--danger)">৳0.00</div></div>
            <div class="kpi-card"><span class="kpi-label">Active Shifts</span><div class="kpi-value" id="fin-kpi-shifts">0 Open</div></div>
          </div>
        </div>

        <!-- TAB: Expenses -->
        <div id="tab-fin-expenses-content" class="finance-tab-pane" style="display:none;">
          <div class="card" style="margin-bottom:16px; padding:12px 20px; display:flex; justify-content:space-between; align-items:center;">
            <h3>💸 Operating Expenses Ledger</h3>
            <button class="btn btn-sm btn-primary" onclick="SmartFinance.openCreateExpenseModal()">+ Record Expense</button>
          </div>
          <div class="card">
            <div class="table-container"><table class="data-table">
              <thead><tr><th>ID</th><th>Title / Reference</th><th>Category</th><th>Amount</th><th>Method</th><th>Status</th><th>Recorded By</th><th>Date</th></tr></thead>
              <tbody id="table-expenses-body"><tr><td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted)">Loading...</td></tr></tbody>
            </table></div>
          </div>
        </div>

        <!-- TAB: Shifts -->
        <div id="tab-fin-shifts-content" class="finance-tab-pane" style="display:none;">
          <div class="card">
            <div class="card-header"><h3>🔑 Cashier Shift History</h3></div>
            <div class="table-container"><table class="data-table">
              <thead><tr><th>Shift #</th><th>Cashier</th><th>Drawer</th><th>Opened</th><th>Opening Cash</th><th>Expected Cash</th><th>Actual Cash</th><th>Difference</th><th>Status</th></tr></thead>
              <tbody id="table-shifts-body"><tr><td colspan="9" style="text-align:center;padding:40px;color:var(--text-muted)">Shift history logged automatically.</td></tr></tbody>
            </table></div>
          </div>
        </div>

        <!-- TAB: Day Closing -->
        <div id="tab-fin-dayclosing-content" class="finance-tab-pane" style="display:none;">
          <div id="dayclosing-review-box">
            <!-- Populated dynamically by SmartFinance.loadDayClosingReview -->
          </div>
        </div>

      </section>

      <!-- VIEW: CRM, RESERVATIONS, LOYALTY, COUPONS & QR ORDERING (Prompt 14) -->
      <section id="crm-view" class="role-view" style="display:none;">

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

    </div>
  </main>
</div>

<!-- Modal: Create Customer -->
<div id="modal-create-customer" class="modal-backdrop">
  <div class="modal-content" style="max-width:540px;">
    <div class="modal-header">
      <h3>Register New Customer</h3>
      <button class="btn btn-secondary btn-sm" onclick="document.getElementById('modal-create-customer').classList.remove('active')">✕</button>
    </div>
    <form id="form-create-customer" onsubmit="event.preventDefault(); SmartCRM.submitCreateCustomer();">
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label">Full Name *</label>
          <input type="text" name="name" class="form-control" required placeholder="e.g. Tanvir Ahmed">
        </div>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
          <div class="form-group">
            <label class="form-label">Phone Number *</label>
            <input type="tel" name="phone" class="form-control" required placeholder="01700000000">
          </div>
          <div class="form-group">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-control" placeholder="customer@example.com">
          </div>
        </div>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
          <div class="form-group">
            <label class="form-label">First Name</label>
            <input type="text" name="first_name" class="form-control">
          </div>
          <div class="form-group">
            <label class="form-label">Last Name</label>
            <input type="text" name="last_name" class="form-control">
          </div>
        </div>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
          <div class="form-group">
            <label class="form-label">Company Name</label>
            <input type="text" name="company_name" class="form-control">
          </div>
          <div class="form-group">
            <label class="form-label">Date of Birth</label>
            <input type="date" name="date_of_birth" class="form-control">
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Gender</label>
          <select name="gender" class="form-control">
            <option value="UNDISCLOSED">Undisclosed</option>
            <option value="MALE">Male</option>
            <option value="FEMALE">Female</option>
            <option value="OTHER">Other</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Notes</label>
          <textarea name="notes" class="form-control" rows="2" placeholder="VIP customer, allergies..."></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="document.getElementById('modal-create-customer').classList.remove('active')">Cancel</button>
        <button type="submit" class="btn btn-primary">Create Customer Profile</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal: Customer Profile Drawer -->
<div id="modal-customer-profile" class="modal-backdrop">
  <div class="modal-content" style="max-width:860px;">
    <div class="modal-header">
      <h3>Customer Full Profile</h3>
      <button class="btn btn-secondary btn-sm" onclick="document.getElementById('modal-customer-profile').classList.remove('active')">✕</button>
    </div>
    <div class="modal-body" id="customer-profile-content">
      <!-- Populated dynamically by SmartCRM.openCustomerProfileModal -->
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="document.getElementById('modal-customer-profile').classList.remove('active')">Close Profile</button>
    </div>
  </div>
</div>

<!-- Modal: Create Reservation -->
<div id="modal-create-reservation" class="modal-backdrop">
  <div class="modal-content" style="max-width:540px;">
    <div class="modal-header">
      <h3>Create Table Reservation</h3>
      <button class="btn btn-secondary btn-sm" onclick="document.getElementById('modal-create-reservation').classList.remove('active')">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label class="form-label">Customer Name *</label>
        <input type="text" id="res-modal-name" class="form-control" placeholder="e.g. Rahat Khan">
      </div>
      <div class="form-group">
        <label class="form-label">Customer Phone *</label>
        <input type="tel" id="res-modal-phone" class="form-control" placeholder="01800000000">
      </div>
      <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
        <div class="form-group">
          <label class="form-label">Reservation Date *</label>
          <input type="date" id="res-modal-date" class="form-control" onchange="SmartCRM.checkReservationAvailability()">
        </div>
        <div class="form-group">
          <label class="form-label">Reservation Time *</label>
          <input type="time" id="res-modal-time" class="form-control" value="19:00" onchange="SmartCRM.checkReservationAvailability()">
        </div>
      </div>
      <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
        <div class="form-group">
          <label class="form-label">Guest Count *</label>
          <input type="number" id="res-modal-guests" class="form-control" value="2" min="1" max="20" onchange="SmartCRM.checkReservationAvailability()">
        </div>
        <div class="form-group">
          <label class="form-label">Duration (Minutes)</label>
          <input type="number" id="res-modal-duration" class="form-control" value="90">
        </div>
      </div>
      <div id="res-modal-availability-box" class="alert alert-info" style="margin-top:6px;">
        Fill date, time, and guests to check real-time table conflict status.
      </div>
      <div class="form-group">
        <label class="form-label">Reservation Notes</label>
        <input type="text" id="res-modal-notes" class="form-control" placeholder="e.g. Window seat requested">
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="document.getElementById('modal-create-reservation').classList.remove('active')">Cancel</button>
      <button class="btn btn-primary" onclick="SmartCRM.submitCreateReservation()">Confirm Reservation</button>
    </div>
  </div>
</div>

<!-- Modal: Create Coupon -->
<div id="modal-create-coupon" class="modal-backdrop">
  <div class="modal-content" style="max-width:500px;">
    <div class="modal-header">
      <h3>Create Promotional Coupon Code</h3>
      <button class="btn btn-secondary btn-sm" onclick="document.getElementById('modal-create-coupon').classList.remove('active')">✕</button>
    </div>
    <div class="modal-body">
      <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
        <div class="form-group">
          <label class="form-label">Coupon Code *</label>
          <input type="text" id="cpn-modal-code" class="form-control" placeholder="e.g. SAVE20">
        </div>
        <div class="form-group">
          <label class="form-label">Discount Type *</label>
          <select id="cpn-modal-type" class="form-control">
            <option value="PERCENTAGE">Percentage (%)</option>
            <option value="FIXED">Fixed Amount ($)</option>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Coupon Name</label>
        <input type="text" id="cpn-modal-name" class="form-control" placeholder="e.g. 20% Off Weekend Promotion">
      </div>
      <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
        <div class="form-group">
          <label class="form-label">Discount Value *</label>
          <input type="number" step="0.01" id="cpn-modal-amount" class="form-control" placeholder="20.00">
        </div>
        <div class="form-group">
          <label class="form-label">Min Order Spend ($)</label>
          <input type="number" step="0.01" id="cpn-modal-minspend" class="form-control" value="0.00">
        </div>
      </div>
      <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
        <div class="form-group">
          <label class="form-label">Max Discount ($ Cap)</label>
          <input type="number" step="0.01" id="cpn-modal-maxdisc" class="form-control" value="0.00">
        </div>
        <div class="form-group">
          <label class="form-label">Usage Limit (0=unlimited)</label>
          <input type="number" id="cpn-modal-limit" class="form-control" value="0">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Valid Until Date</label>
        <input type="date" id="cpn-modal-until" class="form-control">
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="document.getElementById('modal-create-coupon').classList.remove('active')">Cancel</button>
      <button class="btn btn-primary" onclick="SmartCRM.submitCreateCoupon()">Create Coupon</button>
    </div>
  </div>
</div>


    </div>
  </main>
</div>

<!-- Open Dining Session Modal -->
<div id="open-session-modal" class="modal-backdrop">
  <div class="modal-content">
    <div class="modal-header">
      <h3 id="session-modal-title">Open Dining Session</h3>
      <button class="btn btn-secondary btn-sm" onclick="SmartModal.close('open-session-modal')">✕</button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="open-session-table-id">
      <div class="form-group">
        <label class="form-label">Guest Count (Seating)</label>
        <input type="number" id="open-guest-count" class="form-control" value="2" min="1" max="20">
      </div>
      <div class="form-group">
        <label class="form-label">Session / Guest Notes</label>
        <input type="text" id="open-session-notes" class="form-control" placeholder="e.g. High chair needed, Anniversary">
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="SmartModal.close('open-session-modal')">Cancel</button>
      <button class="btn btn-primary" onclick="submitOpenSession()">Open Session & Occupy Table</button>
    </div>
  </div>
</div>

<!-- Transfer Table Modal -->
<div id="transfer-table-modal" class="modal-backdrop">
  <div class="modal-content">
    <div class="modal-header">
      <h3>Transfer Active Session to New Table</h3>
      <button class="btn btn-secondary btn-sm" onclick="SmartModal.close('transfer-table-modal')">✕</button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="transfer-session-id">
      <div class="form-group">
        <label class="form-label">Select Available Destination Table</label>
        <select id="transfer-dest-table" class="form-control">
          <!-- Populated dynamically via loadAvailableTables -->
        </select>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="SmartModal.close('transfer-table-modal')">Cancel</button>
      <button class="btn btn-primary" onclick="submitTransferTable()">Transfer Session</button>
    </div>
  </div>
</div>

<!-- Create Product Modal -->
<div id="create-product-modal" class="modal-backdrop">
  <div class="modal-content">
    <div class="modal-header">
      <h3>Create Restaurant Product</h3>
      <button class="btn btn-secondary btn-sm" onclick="SmartModal.close('create-product-modal')">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label class="form-label">Product Name</label>
        <input type="text" id="new-prod-name" class="form-control" placeholder="e.g. Chicken Alfredo Pasta">
      </div>
      <div class="form-group">
        <label class="form-label">Category</label>
        <select id="new-prod-category" class="form-control">
          <!-- Populated dynamically -->
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">SKU (Code)</label>
        <input type="text" id="new-prod-sku" class="form-control" placeholder="e.g. PASTA-01">
      </div>
      <div class="form-group">
        <label class="form-label">Base Price (৳ BDT)</label>
        <input type="number" step="0.01" id="new-prod-price" class="form-control" placeholder="0.00">
      </div>
      <div class="form-group">
        <label class="form-label">Default Station Routing</label>
        <select id="new-prod-station" class="form-control">
          <option value="1">Main Kitchen</option>
          <option value="2">Beverage & Bar Counter</option>
          <option value="3">Dessert Station</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Short Description</label>
        <input type="text" id="new-prod-desc" class="form-control" placeholder="e.g. Creamy fettuccine with grilled chicken breast">
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="SmartModal.close('create-product-modal')">Cancel</button>
      <button class="btn btn-primary" onclick="submitCreateProduct()">Create Product</button>
    </div>
  </div>
</div>

<!-- Create Category Modal -->
<div id="create-category-modal" class="modal-backdrop">
  <div class="modal-content">
    <div class="modal-header">
      <h3>Create Product Category</h3>
      <button class="btn btn-secondary btn-sm" onclick="SmartModal.close('create-category-modal')">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label class="form-label">Category Name</label>
        <input type="text" id="new-cat-name" class="form-control" placeholder="e.g. Italian Pasta">
      </div>
      <div class="form-group">
        <label class="form-label">Description</label>
        <input type="text" id="new-cat-desc" class="form-control" placeholder="e.g. Fresh handmade pastas & risottos">
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="SmartModal.close('create-category-modal')">Cancel</button>
      <button class="btn btn-primary" onclick="submitCreateCategory()">Create Category</button>
    </div>
  </div>
</div>

<!-- Create Modifier Modal -->
<div id="create-modifier-modal" class="modal-backdrop">
  <div class="modal-content">
    <div class="modal-header">
      <h3>Create Extra Modifier</h3>
      <button class="btn btn-secondary btn-sm" onclick="SmartModal.close('create-modifier-modal')">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label class="form-label">Modifier Name</label>
        <input type="text" id="new-mod-name" class="form-control" placeholder="e.g. Extra Parmesan Cheese">
      </div>
      <div class="form-group">
        <label class="form-label">Additional Charge (৳ BDT)</label>
        <input type="number" step="0.01" id="new-mod-price" class="form-control" value="0.00" placeholder="0.00">
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="SmartModal.close('create-modifier-modal')">Cancel</button>
      <button class="btn btn-primary" onclick="submitCreateModifier()">Create Modifier</button>
    </div>
  </div>
</div>

<!-- Manage Variants Modal -->
<div id="manage-variants-modal" class="modal-backdrop">
  <div class="modal-content">
    <div class="modal-header">
      <h3 id="variants-modal-title">Product Variants</h3>
      <button class="btn btn-secondary btn-sm" onclick="SmartModal.close('manage-variants-modal')">✕</button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="variant-product-id">
      <div class="form-group" style="display:flex; gap:8px;">
        <input type="text" id="new-variant-name" class="form-control" placeholder="Variant name (e.g. Large / Double)">
        <input type="number" step="0.01" id="new-variant-price" class="form-control" style="max-width:140px;" placeholder="Price ৳">
        <button class="btn btn-primary" onclick="submitCreateVariant()">+ Add</button>
      </div>
      <div id="variants-list-container" style="margin-top:16px;">
        <!-- List of existing variants -->
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="SmartModal.close('manage-variants-modal')">Close</button>
    </div>
  </div>
</div>

<!-- Manage Modifiers Modal -->
<div id="manage-modifiers-modal" class="modal-backdrop">
  <div class="modal-content">
    <div class="modal-header">
      <h3 id="modifiers-modal-title">Attach Modifiers to Product</h3>
      <button class="btn btn-secondary btn-sm" onclick="SmartModal.close('manage-modifiers-modal')">✕</button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="modifiers-product-id">
      <div id="modifiers-selection-list" style="display:flex; flex-direction:column; gap:10px;">
        <!-- Checkboxes of available modifiers -->
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="SmartModal.close('manage-modifiers-modal')">Done</button>
    </div>
  </div>
</div>

<!-- Order Details & Lifecycle Modal -->
<div id="order-details-modal" class="modal-backdrop">
  <div class="modal-content" style="max-width:720px;">
    <div class="modal-header">
      <div>
        <h3 id="order-modal-title">Order Details</h3>
        <span class="text-sm" id="order-modal-subtitle">Table --</span>
      </div>
      <button class="btn btn-secondary btn-sm" onclick="SmartModal.close('order-details-modal')">✕</button>
    </div>
    <div class="modal-body" id="order-modal-body">
      <!-- Itemized breakdown & totals -->
    </div>
    <div class="modal-footer" id="order-modal-footer">
      <!-- State Machine Action buttons -->
    </div>
  </div>
</div>

<!-- Order Routing & Dispatch Modal -->
<div id="order-routing-modal" class="modal-backdrop">
  <div class="modal-content" style="max-width:840px;">
    <div class="modal-header">
      <div>
        <h3 id="routing-modal-title">Station Routing</h3>
        <span class="text-sm" id="routing-modal-subtitle">Table --</span>
      </div>
      <button class="btn btn-secondary btn-sm" onclick="SmartModal.close('order-routing-modal')">✕</button>
    </div>
    <div class="modal-body" id="routing-modal-body">
      <!-- Item-level routing breakdown & generated tickets -->
    </div>
    <div class="modal-footer" id="routing-modal-footer">
      <!-- Dispatch buttons -->
    </div>
  </div>
</div>

<!-- Order Billing & Payment Settlement Modal -->
<div id="order-billing-modal" class="modal-backdrop">
  <div class="modal-content" style="max-width:800px;">
    <div class="modal-header">
      <div>
        <h3 id="billing-modal-title">Order Billing & Settlement</h3>
        <span class="text-sm" id="billing-modal-subtitle">Order # --</span>
      </div>
      <button class="btn btn-secondary btn-sm" onclick="SmartModal.close('order-billing-modal')">✕</button>
    </div>
    <div class="modal-body" id="billing-modal-body">
      <!-- Populated dynamically by SmartBilling -->
    </div>
    <div class="modal-footer" id="billing-modal-footer">
      <button class="btn btn-secondary" onclick="SmartModal.close('order-billing-modal')">Close</button>
    </div>
  </div>
</div>

<!-- Printable Thermal Receipt Modal -->
<div id="receipt-modal" class="modal-backdrop">
  <div class="modal-content" style="max-width:500px;">
    <div class="modal-header">
      <h3 id="receipt-modal-title">Official Thermal Receipt</h3>
      <button class="btn btn-secondary btn-sm" onclick="SmartModal.close('receipt-modal')">✕</button>
    </div>
    <div class="modal-body" id="receipt-modal-body">
      <!-- Printable receipt snapshot formatted here -->
    </div>
    <div class="modal-footer" id="receipt-modal-footer">
      <button class="btn btn-secondary" onclick="SmartModal.close('receipt-modal')">Close</button>
      <button class="btn btn-primary" onclick="SmartBilling.printReceipt()">🖨️ Print Receipt</button>
    </div>
  </div>
</div>

<!-- Create Commission Rule Modal -->
<div id="create-commission-rule-modal" class="modal-backdrop">
  <div class="modal-content">
    <div class="modal-header">
      <h3>Create Commission Rule</h3>
      <button class="btn btn-secondary btn-sm" onclick="SmartModal.close('create-commission-rule-modal')">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label class="form-label">Rule Name</label>
        <input type="text" id="rule-name" class="form-control" placeholder="e.g. Peak Hours 7% Commission">
      </div>
      <div class="form-group">
        <label class="form-label">Calculation Base</label>
        <select id="rule-base" class="form-control">
          <option value="PERCENTAGE">Percentage of Net Sales (%)</option>
          <option value="PER_ORDER">Fixed Amount Per Order (৳)</option>
          <option value="PER_ITEM">Fixed Amount Per Item (৳)</option>
          <option value="FIXED_AMOUNT">Flat Transaction Fee (৳)</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Commission Rate (%)</label>
        <input type="number" step="0.1" id="rule-rate" class="form-control" value="5.0" placeholder="5.0">
      </div>
      <div class="form-group">
        <label class="form-label">Fixed Amount (৳ BDT)</label>
        <input type="number" step="0.01" id="rule-fixed" class="form-control" value="0.00" placeholder="0.00">
      </div>
      <div class="form-group">
        <label class="form-label">Minimum Sales Threshold (৳ BDT)</label>
        <input type="number" step="0.01" id="rule-minsales" class="form-control" value="0.00" placeholder="0.00">
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="SmartModal.close('create-commission-rule-modal')">Cancel</button>
      <button class="btn btn-primary" onclick="SmartCommissions.submitCreateCommissionRule()">Create Rule</button>
    </div>
  </div>
</div>

<!-- Commission Payout Modal -->
<div id="commission-payout-modal" class="modal-backdrop">
  <div class="modal-content">
    <div class="modal-header">
      <h3 id="payout-modal-title">Commission Payout Settlement</h3>
      <button class="btn btn-secondary btn-sm" onclick="SmartModal.close('commission-payout-modal')">✕</button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="payout-waiter-id">
      <div style="background:var(--surface); border:1px solid var(--border); padding:16px; border-radius:8px; margin-bottom:16px; text-align:center;">
        <span class="text-sm text-muted">Approved Unpaid Commission Balance</span>
        <div style="font-size:2rem; font-weight:700; color:var(--success);" id="payout-amount-display">৳0.00</div>
      </div>
      <div class="form-group">
        <label class="form-label">Payout Settlement Method</label>
        <select id="payout-method" class="form-control">
          <option value="CASH">Cash Payment</option>
          <option value="BANK_TRANSFER">Bank Direct Transfer</option>
          <option value="MOBILE_WALLET">Mobile Wallet (bKash / Nagad)</option>
          <option value="OTHER">Other Settlement</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Reference / Transaction Number</label>
        <input type="text" id="payout-ref" class="form-control" placeholder="e.g. BKASH-PAY-889922">
      </div>
      <div class="form-group">
        <label class="form-label">Settlement Notes</label>
        <input type="text" id="payout-notes" class="form-control" placeholder="e.g. Weekly commission payout">
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="SmartModal.close('commission-payout-modal')">Cancel</button>
      <button class="btn btn-primary" onclick="SmartCommissions.submitProcessPayout()">Process Payout Settlement</button>
    </div>
  </div>
</div>

<!-- Waiter Performance Drill-Down Modal -->
<div id="waiter-drilldown-modal" class="modal-backdrop">
  <div class="modal-content" style="max-width:760px;">
    <div class="modal-header">
      <div>
        <h3>Waiter Performance Drill-Down</h3>
        <span class="text-sm" id="waiter-drilldown-title">Staff Details</span>
      </div>
      <button class="btn btn-secondary btn-sm" onclick="SmartModal.close('waiter-drilldown-modal')">✕</button>
    </div>
    <div class="modal-body" id="waiter-drilldown-body">
      <!-- Populated dynamically -->
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="SmartModal.close('waiter-drilldown-modal')">Close</button>
    </div>
  </div>
</div>

<!-- Create Table Modal -->
<div id="create-table-modal" class="modal-backdrop">
  <div class="modal-content">
    <div class="modal-header">
      <h3>Create Dining Table</h3>
      <button class="btn btn-secondary btn-sm" onclick="SmartModal.close('create-table-modal')">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label class="form-label">Floor Zone</label>
        <select id="new-table-floor" class="form-control">
          <option value="1">Main Dining Hall</option>
          <option value="2">Terrace Lounge</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Table Number / Code</label>
        <input type="text" id="new-table-number" class="form-control" placeholder="e.g. T-15">
      </div>
      <div class="form-group">
        <label class="form-label">Guest Capacity</label>
        <input type="number" id="new-table-capacity" class="form-control" value="4" min="1">
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="SmartModal.close('create-table-modal')">Cancel</button>
      <button class="btn btn-primary" onclick="submitCreateTable()">Create Table</button>
    </div>
  </div>
</div>

<!-- Create User Modal -->
<div id="create-user-modal" class="modal-backdrop">
  <div class="modal-content">
    <div class="modal-header">
      <h3>Create Staff User Account</h3>
      <button class="btn btn-secondary btn-sm" onclick="SmartModal.close('create-user-modal')">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label class="form-label">Full Name</label>
        <input type="text" id="user-name" class="form-control" placeholder="e.g. Tanvir Ahmed">
      </div>
      <div class="form-group">
        <label class="form-label">Email Address</label>
        <input type="email" id="user-email" class="form-control" placeholder="tanvir@smartresta.com">
      </div>
      <div class="form-group">
        <label class="form-label">Temporary Password</label>
        <input type="password" id="user-password" class="form-control" placeholder="Minimum 8 characters">
      </div>
      <div class="form-group">
        <label class="form-label">Role Privilege</label>
        <select id="user-role" class="form-control">
          <option value="waiter">Waiter (Order Taker)</option>
          <option value="reception">Reception / Cashier</option>
          <option value="kitchen">Kitchen Operator</option>
          <option value="manager">Restaurant Manager</option>
          <option value="admin">System Administrator</option>
        </select>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="SmartModal.close('create-user-modal')">Cancel</button>
      <button class="btn btn-primary" onclick="submitCreateUser()">Create Account</button>
    </div>
  </div>
</div>

<!-- Reservation Modal -->
<div id="new-order-modal" class="modal-backdrop">
  <div class="modal-content">
    <div class="modal-header">
      <h3>Create Quick Table Reservation</h3>
      <button class="btn btn-secondary btn-sm" onclick="SmartModal.close('new-order-modal')">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label class="form-label">Customer Name</label>
        <input type="text" id="res-name" class="form-control" placeholder="e.g. Tanvir Hossain">
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="SmartModal.close('new-order-modal')">Cancel</button>
      <button class="btn btn-primary" onclick="confirmReservation()">Confirm Reservation</button>
    </div>
  </div>
</div>

<!-- Re-Fire Ticket Modal -->
<div id="refire-ticket-modal" class="modal-backdrop">
  <div class="modal-content">
    <div class="modal-header">
      <h3 id="refire-modal-title">Re-Fire Production Ticket</h3>
      <button class="btn btn-secondary btn-sm" onclick="SmartModal.close('refire-ticket-modal')">✕</button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="refire-ticket-id">
      <div style="background:var(--warning-light); border:1px solid var(--warning); padding:12px; border-radius:6px; margin-bottom:14px; font-size:0.875rem; color:var(--warning-dark);">
        ⚠️ Re-firing will dispatch a new ticket tagged with <strong>URGENT</strong> priority while preserving the original production ticket history.
      </div>
      <div class="form-group">
        <label class="form-label">Re-Fire Reason (Required)</label>
        <select id="refire-reason" class="form-control">
          <option value="Burned / Overcooked">Burned / Overcooked</option>
          <option value="Cold / Undercooked">Cold / Undercooked</option>
          <option value="Wrong Item / Modifier Error">Wrong Item / Modifier Error</option>
          <option value="Customer Quality Request">Customer Quality Request</option>
          <option value="Spill / Kitchen Accident">Spill / Kitchen Accident</option>
        </select>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="SmartModal.close('refire-ticket-modal')">Cancel</button>
      <button class="btn btn-warning" onclick="SmartKDS.submitRefireTicket()">🔥 Re-Fire Urgent Ticket</button>
    </div>
  </div>
</div>

<!-- Cancel Ticket Modal -->
<div id="cancel-ticket-modal" class="modal-backdrop">
  <div class="modal-content">
    <div class="modal-header">
      <h3 id="cancel-modal-title">Cancel Station Ticket</h3>
      <button class="btn btn-secondary btn-sm" onclick="SmartModal.close('cancel-ticket-modal')">✕</button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="cancel-ticket-id">
      <div class="form-group">
        <label class="form-label">Cancellation Reason (Required)</label>
        <input type="text" id="cancel-reason" class="form-control" placeholder="e.g. Customer cancelled item, Out of ingredient">
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="SmartModal.close('cancel-ticket-modal')">Back</button>
      <button class="btn btn-danger" onclick="SmartKDS.submitCancelTicket()">Confirm Cancellation</button>
    </div>
  </div>
</div>

<!-- Ticket Status History Modal -->
<div id="ticket-history-modal" class="modal-backdrop">
  <div class="modal-content" style="max-width:680px;">
    <div class="modal-header">
      <h3 id="history-modal-title">Ticket Audit History</h3>
      <button class="btn btn-secondary btn-sm" onclick="SmartModal.close('ticket-history-modal')">✕</button>
    </div>
    <div class="modal-body" id="history-timeline-container">
      <!-- Populated dynamically by SmartKDS.openHistoryModal -->
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="SmartModal.close('ticket-history-modal')">Close</button>
    </div>
  </div>
</div>

<!-- Create Operational Station Modal -->
<div id="manage-station-modal" class="modal-backdrop">
  <div class="modal-content">
    <div class="modal-header">
      <h3>Create Operational Station / Counter</h3>
      <button class="btn btn-secondary btn-sm" onclick="SmartModal.close('manage-station-modal')">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label class="form-label">Station Name</label>
        <input type="text" id="new-station-name" class="form-control" placeholder="e.g. Grill Station">
      </div>
      <div class="form-group">
        <label class="form-label">Station Type</label>
        <select id="new-station-type" class="form-control">
          <option value="KITCHEN">KITCHEN</option>
          <option value="BAR">BAR</option>
          <option value="COFFEE">COFFEE</option>
          <option value="GRILL">GRILL</option>
          <option value="DESSERT">DESSERT</option>
          <option value="PACKAGING">PACKAGING</option>
          <option value="OTHER">OTHER</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Badge Code</label>
        <input type="text" id="new-station-code" class="form-control" placeholder="e.g. ST-GRILL">
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="SmartModal.close('manage-station-modal')">Cancel</button>
      <button class="btn btn-primary" onclick="submitCreateStation()">Create Station</button>
    </div>
  <!-- Ingredient Modal -->
<div id="ingredient-modal" class="modal-backdrop">
  <div class="modal-content">
    <div class="modal-header">
      <h3 id="ing-modal-title">Add New Ingredient</h3>
      <button class="btn btn-secondary btn-sm" onclick="SmartModal.close('ingredient-modal')">✕</button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="ing-id">
      <div class="form-group">
        <label class="form-label">Ingredient Code / SKU</label>
        <input type="text" id="ing-code" class="form-control" placeholder="e.g. ING-1001">
      </div>
      <div class="form-group">
        <label class="form-label">Ingredient Name</label>
        <input type="text" id="ing-name" class="form-control" placeholder="e.g. Chicken Breast (Boneless)">
      </div>
      <div class="form-group">
        <label class="form-label">Category</label>
        <select id="ing-category-id" class="form-control">
          <!-- Populated dynamically -->
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Unit of Measure (UOM)</label>
        <select id="ing-uom" class="form-control">
          <option value="kg">Kilogram (kg)</option>
          <option value="gram">Gram (g)</option>
          <option value="liter">Liter (l)</option>
          <option value="ml">Milliliter (ml)</option>
          <option value="pcs">Pieces (pcs)</option>
          <option value="box">Box</option>
          <option value="pack">Pack</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Average Unit Cost (৳ BDT)</label>
        <input type="number" step="0.01" id="ing-cost" class="form-control" placeholder="0.00">
      </div>
      <div class="form-group">
        <label class="form-label">Minimum Low Stock Alert Threshold</label>
        <input type="number" step="0.01" id="ing-min-alert" class="form-control" value="10.00">
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="SmartModal.close('ingredient-modal')">Cancel</button>
      <button class="btn btn-primary" onclick="SmartInventory.saveIngredient()">Save Ingredient</button>
    </div>
  </div>
</div>

<!-- Manual Stock Adjustment Modal -->
<div id="adjust-stock-modal" class="modal-backdrop">
  <div class="modal-content">
    <div class="modal-header">
      <h3 id="adj-modal-title">Adjust Stock</h3>
      <button class="btn btn-secondary btn-sm" onclick="SmartModal.close('adjust-stock-modal')">✕</button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="adj-ing-id">
      <div class="form-group">
        <label class="form-label">Inventory Location</label>
        <select id="adj-location-id" class="form-control">
          <!-- Populated dynamically -->
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Adjustment Type</label>
        <select id="adj-type" class="form-control">
          <option value="ADJUSTMENT_IN">Stock Increase (Adjustment IN)</option>
          <option value="ADJUSTMENT_OUT">Stock Decrease (Adjustment OUT)</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Quantity</label>
        <input type="number" step="0.001" id="adj-qty" class="form-control" value="1.00">
      </div>
      <div class="form-group">
        <label class="form-label">Reason / Justification</label>
        <input type="text" id="adj-reason" class="form-control" placeholder="e.g. Stock audit variance, Delivery correction">
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="SmartModal.close('adjust-stock-modal')">Cancel</button>
      <button class="btn btn-primary" onclick="SmartInventory.submitAdjustStock()">Submit Stock Adjustment</button>
    </div>
  </div>
</div>

<!-- Recipe Builder Modal -->
<div id="recipe-builder-modal" class="modal-backdrop">
  <div class="modal-content" style="max-width:820px;">
    <div class="modal-header">
      <div>
        <h3 id="rec-modal-title">Recipe Bill of Materials (BOM) Builder</h3>
        <span class="text-sm">Link menu products to raw ingredients for automatic stock deduction</span>
      </div>
      <button class="btn btn-secondary btn-sm" onclick="SmartModal.close('recipe-builder-modal')">✕</button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="rec-product-id">
      <input type="hidden" id="rec-variant-id">

      <div style="display:flex; gap:16px; margin-bottom:16px; background:var(--surface-hover); padding:12px 16px; border-radius:8px;">
        <div style="flex:1;">
          <label class="form-label">Yield Quantity</label>
          <input type="number" step="0.1" id="rec-yield-qty" class="form-control" value="1.0" oninput="SmartInventory.calculateLiveRecipeCost()">
        </div>
        <div style="flex:1; display:flex; flex-direction:column; justify-content:center; align-items:flex-end;">
          <span class="text-sm text-muted">Live Calculated Recipe Cost</span>
          <div style="font-size:1.5rem; font-weight:700; color:var(--primary);" id="rec-live-cost">৳0.00</div>
        </div>
      </div>

      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
        <label class="form-label" style="margin:0;">BOM Ingredient Portions</label>
        <button class="btn btn-sm btn-secondary" onclick="SmartInventory.addRecipeItemRow()">+ Add Ingredient Row</button>
      </div>

      <div id="rec-items-container" style="max-height:280px; overflow-y:auto;">
        <!-- Dynamic recipe item rows -->
      </div>

      <div class="form-group" style="margin-top:14px;">
        <label class="form-label">Preparation / Recipe Notes</label>
        <input type="text" id="rec-notes" class="form-control" placeholder="e.g. Standard portion per serving">
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="SmartModal.close('recipe-builder-modal')">Cancel</button>
      <button class="btn btn-primary" onclick="SmartInventory.saveRecipe()">Save Recipe BOM</button>
    </div>
  </div>
</div>

<!-- Supplier Modal -->
<div id="supplier-modal" class="modal-backdrop">
  <div class="modal-content">
    <div class="modal-header">
      <h3 id="sup-modal-title">Supplier Profile</h3>
      <button class="btn btn-secondary btn-sm" onclick="SmartModal.close('supplier-modal')">✕</button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="sup-id">
      <div class="form-group">
        <label class="form-label">Supplier / Company Name</label>
        <input type="text" id="sup-name" class="form-control" placeholder="e.g. Dhaka Agro Poultry Ltd.">
      </div>
      <div class="form-group">
        <label class="form-label">Contact Person</label>
        <input type="text" id="sup-contact" class="form-control" placeholder="e.g. Rafiqul Islam">
      </div>
      <div class="form-group">
        <label class="form-label">Phone Number</label>
        <input type="text" id="sup-phone" class="form-control" placeholder="e.g. +8801711002233">
      </div>
      <div class="form-group">
        <label class="form-label">Email Address</label>
        <input type="email" id="sup-email" class="form-control" placeholder="e.g. sales@dhakaagro.com">
      </div>
      <div class="form-group">
        <label class="form-label">Address</label>
        <input type="text" id="sup-address" class="form-control" placeholder="e.g. Tejgaon Industrial Area, Dhaka">
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="SmartModal.close('supplier-modal')">Cancel</button>
      <button class="btn btn-primary" onclick="SmartInventory.saveSupplier()">Save Supplier</button>
    </div>
  </div>
</div>

<!-- Create Purchase Order Modal -->
<div id="create-po-modal" class="modal-backdrop">
  <div class="modal-content" style="max-width:780px;">
    <div class="modal-header">
      <h3>Create Purchase Order (PO)</h3>
      <button class="btn btn-secondary btn-sm" onclick="SmartModal.close('create-po-modal')">✕</button>
    </div>
    <div class="modal-body">
      <div style="display:flex; gap:12px; margin-bottom:16px;">
        <div style="flex:1;">
          <label class="form-label">Supplier</label>
          <select id="po-sup-select" class="form-control">
            <!-- Dynamic suppliers -->
          </select>
        </div>
        <div style="flex:1;">
          <label class="form-label">Destination Store Location</label>
          <select id="po-loc-select" class="form-control">
            <!-- Dynamic locations -->
          </select>
        </div>
      </div>

      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
        <label class="form-label" style="margin:0;">PO Line Items</label>
        <button class="btn btn-sm btn-secondary" onclick="SmartInventory.addPOItemRow()">+ Add Item Row</button>
      </div>

      <div id="po-items-container" style="max-height:240px; overflow-y:auto;">
        <!-- Dynamic PO item rows -->
      </div>

      <div class="form-group" style="margin-top:14px;">
        <label class="form-label">Purchase Order Notes</label>
        <input type="text" id="po-notes" class="form-control" placeholder="e.g. Urgent delivery requested by Friday morning">
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="SmartModal.close('create-po-modal')">Cancel</button>
      <button class="btn btn-primary" onclick="SmartInventory.submitCreatePO()">Submit Purchase Order</button>
    </div>
  </div>
</div>

<!-- Receive Goods Modal -->
<div id="receive-goods-modal" class="modal-backdrop">
  <div class="modal-content" style="max-width:760px;">
    <div class="modal-header">
      <h3 id="rec-goods-title">Receive Goods into Inventory</h3>
      <button class="btn btn-secondary btn-sm" onclick="SmartModal.close('receive-goods-modal')">✕</button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="rec-goods-po-id">
      <input type="hidden" id="rec-goods-loc-id">

      <div class="form-group" style="margin-bottom:16px;">
        <label class="form-label">Supplier Invoice / Goods Receipt Reference Number</label>
        <input type="text" id="rec-goods-invoice" class="form-control" placeholder="e.g. INV-998822">
      </div>

      <label class="form-label" style="margin-bottom:10px;">Verify Received Quantities & Unit Costs</label>
      <div id="rec-goods-items-container" style="max-height:260px; overflow-y:auto;">
        <!-- Dynamic receive items -->
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="SmartModal.close('receive-goods-modal')">Cancel</button>
      <button class="btn btn-success" onclick="SmartInventory.submitReceiveGoods()">✓ Post Goods Receipt to Stock</button>
    </div>
  </div>
</div>

<!-- Scripts -->
<script src="assets/js/app.js"></script>
<script src="assets/js/ajax.js"></script>
<script src="assets/js/notifications.js"></script>
<script src="assets/js/modal.js"></script>
<script src="assets/js/pos.js"></script>
<script src="assets/js/routing.js"></script>
<script src="assets/js/kds.js"></script>
<script src="assets/js/billing.js"></script>
<script src="assets/js/commissions.js"></script>
<script src="assets/js/inventory.js"></script>
<script src="assets/js/reports.js"></script>
<script src="assets/js/finance.js"></script>
<script src="assets/js/crm.js"></script>

<!-- Open Shift Modal -->
<div id="open-shift-modal" class="modal-backdrop">
  <div class="modal-content">
    <div class="modal-header">
      <h3>🔑 Open Cashier Shift</h3>
      <button class="btn btn-secondary btn-sm" onclick="SmartModal.close('open-shift-modal')">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label class="form-label">Opening Cash Balance (৳ BDT)</label>
        <input type="number" step="0.01" id="shift-opening-cash" class="form-control" value="10000.00">
      </div>
      <div class="form-group">
        <label class="form-label">Opening Notes / Drawer Location</label>
        <input type="text" id="shift-open-notes" class="form-control" placeholder="e.g. Front Counter POS 1 - Morning Shift">
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="SmartModal.close('open-shift-modal')">Cancel</button>
      <button class="btn btn-primary" onclick="SmartFinance.submitOpenShift()">🔑 Open Cash Drawer Shift</button>
    </div>
  </div>
</div>

<!-- Close Shift Modal with Denominations Counter -->
<div id="close-shift-modal" class="modal-backdrop">
  <div class="modal-content" style="max-width:680px;">
    <div class="modal-header">
      <h3>🔒 Close Cashier Shift & Physical Cash Audit</h3>
      <button class="btn btn-secondary btn-sm" onclick="SmartModal.close('close-shift-modal')">✕</button>
    </div>
    <div class="modal-body">
      <div style="display:flex; gap:16px; margin-bottom:16px; background:var(--surface); border:1px solid var(--border); padding:16px; border-radius:8px;">
        <div style="flex:1;">
          <span class="text-sm text-muted">Expected Drawer Cash:</span>
          <div style="font-size:1.5rem; font-weight:700; color:var(--primary);" id="close-shift-expected-display">৳0.00</div>
        </div>
        <div style="flex:1;">
          <span class="text-sm text-muted">Cash Difference (Over/Short):</span>
          <div style="font-size:1.5rem; font-weight:700;" id="close-shift-diff-display">৳0.00</div>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Total Actual Physical Cash Counted (৳ BDT)</label>
        <input type="number" step="0.01" id="close-shift-actual" class="form-control" style="font-size:1.25rem; font-weight:700;" oninput="SmartFinance.calculateDenominations()">
      </div>

      <label class="form-label" style="margin-top:12px;">Physical Currency Denomination Counter (Optional)</label>
      <div style="display:grid; grid-template-columns:repeat(2, 1fr); gap:8px; margin-bottom:14px;">
        <div style="display:flex; align-items:center; gap:8px;"><span>৳1000 ×</span><input type="number" class="form-control denom-qty" data-value="1000" value="0" min="0" oninput="SmartFinance.calculateDenominations()"></div>
        <div style="display:flex; align-items:center; gap:8px;"><span>৳500 ×</span><input type="number" class="form-control denom-qty" data-value="500" value="0" min="0" oninput="SmartFinance.calculateDenominations()"></div>
        <div style="display:flex; align-items:center; gap:8px;"><span>৳200 ×</span><input type="number" class="form-control denom-qty" data-value="200" value="0" min="0" oninput="SmartFinance.calculateDenominations()"></div>
        <div style="display:flex; align-items:center; gap:8px;"><span>৳100 ×</span><input type="number" class="form-control denom-qty" data-value="100" value="0" min="0" oninput="SmartFinance.calculateDenominations()"></div>
        <div style="display:flex; align-items:center; gap:8px;"><span>৳50 ×</span><input type="number" class="form-control denom-qty" data-value="50" value="0" min="0" oninput="SmartFinance.calculateDenominations()"></div>
        <div style="display:flex; align-items:center; gap:8px;"><span>৳20 ×</span><input type="number" class="form-control denom-qty" data-value="20" value="0" min="0" oninput="SmartFinance.calculateDenominations()"></div>
      </div>

      <div class="form-group">
        <label class="form-label">Closing Notes / Discrepancy Reason</label>
        <input type="text" id="close-shift-notes" class="form-control" placeholder="e.g. ৳50 shortage due to coin change roundings">
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="SmartModal.close('close-shift-modal')">Cancel</button>
      <button class="btn btn-danger" onclick="SmartFinance.submitCloseShift()">🔒 Finalize & Close Cashier Shift</button>
    </div>
  </div>
</div>

<!-- Cash Movement Modal (Cash In / Out) -->
<div id="cash-movement-modal" class="modal-backdrop">
  <div class="modal-content">
    <div class="modal-header">
      <h3>💵 Log Cash Movement (In / Out)</h3>
      <button class="btn btn-secondary btn-sm" onclick="SmartModal.close('cash-movement-modal')">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label class="form-label">Movement Type</label>
        <select id="cash-move-type" class="form-control">
          <option value="CASH_IN">Cash In (Petty Cash Added / Change)</option>
          <option value="CASH_OUT">Cash Out (Bank Deposit / Supplier Payment)</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Amount (৳ BDT)</label>
        <input type="number" step="0.01" id="cash-move-amount" class="form-control" value="500.00">
      </div>
      <div class="form-group">
        <label class="form-label">Reason / Justification (Required)</label>
        <input type="text" id="cash-move-reason" class="form-control" placeholder="e.g. Added petty cash float, Bank deposit">
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="SmartModal.close('cash-movement-modal')">Cancel</button>
      <button class="btn btn-primary" onclick="SmartFinance.submitCashMovement()">Log Cash Movement</button>
    </div>
  </div>
</div>

<!-- Record Operating Expense Modal -->
<div id="create-expense-modal" class="modal-backdrop">
  <div class="modal-content">
    <div class="modal-header">
      <h3>💸 Record Operating Expense</h3>
      <button class="btn btn-secondary btn-sm" onclick="SmartModal.close('create-expense-modal')">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label class="form-label">Expense Title</label>
        <input type="text" id="exp-title" class="form-control" placeholder="e.g. Monthly Electricity Bill">
      </div>
      <div class="form-group">
        <label class="form-label">Amount (৳ BDT)</label>
        <input type="number" step="0.01" id="exp-amount" class="form-control" placeholder="0.00">
      </div>
      <div class="form-group">
        <label class="form-label">Expense Category</label>
        <select id="exp-category" class="form-control">
          <option value="1">Utilities (Electricity, Water, Gas)</option>
          <option value="2">Rent & Lease</option>
          <option value="3">Cleaning & Maintenance</option>
          <option value="4">Transportation & Freight</option>
          <option value="5">Supplies & Consumables</option>
          <option value="6">Marketing & Promotion</option>
          <option value="7">Staff Expenses</option>
          <option value="8">Other Operating Expenses</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Payment Method</label>
        <select id="exp-method" class="form-control">
          <option value="1">Cash Register</option>
          <option value="5">Bank Transfer</option>
          <option value="2">bKash Merchant</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Notes / Reference Invoice Number</label>
        <input type="text" id="exp-desc" class="form-control" placeholder="e.g. Invoice #DESCO-998822">
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="SmartModal.close('create-expense-modal')">Cancel</button>
      <button class="btn btn-primary" onclick="SmartFinance.submitCreateExpense()">Save & Approve Expense</button>
    </div>
  </div>
</div>

<!-- Order Drill-Down Modal -->
<div id="modal-order-drilldown" class="modal-backdrop">
  <div class="modal-content" style="max-width:900px;">
    <div class="modal-header">
      <h3>🔍 Order Drill-Down: <span id="drilldown-order-number"></span></h3>
      <button class="btn btn-secondary btn-sm" onclick="SmartModal.close('modal-order-drilldown')">✕</button>
    </div>
    <div class="modal-body" style="max-height:70vh; overflow-y:auto;">
      <div id="drilldown-order-header" style="margin-bottom:16px;"></div>

      <h4 style="margin:16px 0 8px;">📝 Order Items</h4>
      <table class="data-table"><thead><tr><th>Product</th><th>Qty</th><th>Unit Price</th><th>Line Total</th></tr></thead>
        <tbody id="drilldown-items-list"></tbody></table>

      <h4 style="margin:16px 0 8px;">🍳 Station Tickets (KDS)</h4>
      <table class="data-table"><thead><tr><th>Ticket #</th><th>Station</th><th>Status</th><th>Created</th></tr></thead>
        <tbody id="drilldown-tickets-list"></tbody></table>

      <h4 style="margin:16px 0 8px;">💳 Payments</h4>
      <table class="data-table"><thead><tr><th>Payment #</th><th>Method</th><th>Amount</th><th>Status</th></tr></thead>
        <tbody id="drilldown-payments-list"></tbody></table>

      <h4 style="margin:16px 0 8px;">📦 Inventory Consumption</h4>
      <table class="data-table"><thead><tr><th>Ingredient</th><th>Consumed Qty</th><th>Cost</th></tr></thead>
        <tbody id="drilldown-inventory-list"></tbody></table>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="SmartModal.close('modal-order-drilldown')">Close</button>
    </div>
  </div>
</div>

<script>
function switchRoleView(viewId, navEl) {
  document.querySelectorAll('.role-view').forEach(view => view.style.display = 'none');
  document.querySelectorAll('.nav-item').forEach(item => item.classList.remove('active'));
  
  document.getElementById(viewId).style.display = 'block';
  if (navEl) navEl.classList.add('active');

  const titles = {
    'admin-view': 'Manager Dashboard',
    'tables-view': 'Restaurant Floor Map & Tables',
    'menu-view': 'Restaurant Menu, Categories & Product Catalog',
    'pos-view': 'POS & Waiter Ordering',
    'kds-view': 'Kitchen Display System (KDS)',
    'users-view': 'Users & Staff Role Permissions',
    'routing-view': 'Smart Order Routing & Station Dispatch',
    'payments-view': 'Billing Engine & Settlement History',
    'commission-rules-view': 'Configurable Commission Rules Engine',
    'commissions-review-view': 'Commission Transaction Review & Approvals',
    'payouts-view': 'Commission Payout Settlements History',
    'inventory-view': 'Inventory, Purchasing, Recipe Costing & Stock Control Engine',
    'reports-view': 'Reports & Operational Analytics',
    'finance-view': 'Finance, Shifts & Day Closing Engine',
    'crm-view': 'CRM, Reservations, Loyalty, Coupons & QR Ordering Engine'
  };
  document.getElementById('view-title').textContent = titles[viewId] || 'SMARTRESTA';

  if (viewId === 'tables-view') {
    loadFloorTables();
  } else if (viewId === 'menu-view') {
    loadCategoryOptions();
    loadProductCatalog();
  } else if (viewId === 'users-view') {
    loadUsersList();
  } else if (viewId === 'payments-view') {
    SmartBilling.loadPaymentHistory();
  } else if (viewId === 'commission-rules-view') {
    SmartCommissions.loadCommissionRules();
  } else if (viewId === 'commissions-review-view') {
    SmartCommissions.loadCommissionsReview();
  } else if (viewId === 'payouts-view') {
    SmartCommissions.loadPayoutHistory();
  } else if (viewId === 'kds-view') {
    SmartKDS.loadKDSGrid();
  } else if (viewId === 'inventory-view') {
    SmartInventory.init();
  } else if (viewId === 'reports-view') {
    SmartReports.loadCurrentTab();
  } else if (viewId === 'finance-view') {
    SmartFinance.init();
  } else if (viewId === 'crm-view') {
    SmartCRM.init();
  }
}

async function loadCategoryOptions() {
  try {
    const res = await SmartAPI.get('api/v1/categories/index.php');
    const filterSelect = document.getElementById('product-category-filter');
    const createSelect = document.getElementById('new-prod-category');
    if (res.data && res.data.length > 0) {
      filterSelect.innerHTML = `<option value="">All Categories</option>` + res.data.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
      createSelect.innerHTML = res.data.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
    } else {
      filterSelect.innerHTML = `<option value="">No categories defined</option>`;
      createSelect.innerHTML = `<option value="">No categories defined</option>`;
    }
  } catch (e) {
    console.error(e);
  }
}

async function loadProductCatalog() {
  const tbody = document.getElementById('products-table-tbody');
  const catId = document.getElementById('product-category-filter').value;
  const status = document.getElementById('product-status-filter').value;
  const search = document.getElementById('product-search-input').value.trim();

  let query = 'api/v1/products/index.php?';
  if (catId) query += `category_id=${catId}&`;
  if (status !== '') query += `is_available=${status}&`;
  if (search) query += `search=${encodeURIComponent(search)}&`;

  try {
    const res = await SmartAPI.get(query);
    if (res.data && res.data.length > 0) {
      tbody.innerHTML = res.data.map(p => `
        <tr>
          <td><strong>${p.name}</strong>${p.short_description ? `<br><small style="color:var(--text-muted);">${p.short_description}</small>` : ''}</td>
          <td><span class="badge badge-info">${p.category_name}</span></td>
          <td><span class="font-mono">${p.sku || 'N/A'}</span></td>
          <td class="price-tag">৳${parseFloat(p.price).toFixed(2)}</td>
          <td>${p.station_name || 'Main Kitchen'}</td>
          <td><button class="btn btn-secondary btn-sm" onclick="openVariantsModal(${p.id}, '${p.name}')">${p.variant_count || 0} Variants</button></td>
          <td>
            <button class="btn ${p.is_available ? 'btn-success' : 'btn-danger'} btn-sm" onclick="toggleProductAvailability(${p.id}, ${!p.is_available})">
              ${p.is_available ? 'Available' : 'Unavailable'}
            </button>
          </td>
          <td><span class="badge ${p.status === 'ACTIVE' ? 'badge-success' : 'badge-danger'}">${p.status}</span></td>
          <td>
            <button class="btn btn-secondary btn-sm" onclick="openModifiersModal(${p.id}, '${p.name}')">Modifiers (${p.modifier_count || 0})</button>
          </td>
        </tr>
      `).join('');
    } else {
      tbody.innerHTML = `<tr><td colspan="9" style="text-align:center; padding: 40px; color: var(--text-muted);">No products found matching filters.</td></tr>`;
    }
  } catch (err) {
    tbody.innerHTML = `<tr><td colspan="9" style="text-align:center; padding: 40px; color: var(--danger);">Unable to load product catalog from database.</td></tr>`;
  }
}

async function submitCreateProduct() {
  const name = document.getElementById('new-prod-name').value.trim();
  const category_id = document.getElementById('new-prod-category').value;
  const sku = document.getElementById('new-prod-sku').value.trim();
  const price = document.getElementById('new-prod-price').value;
  const default_station_id = document.getElementById('new-prod-station').value;
  const short_description = document.getElementById('new-prod-desc').value.trim();

  if (!name || !category_id || price === '') {
    SmartNotifications.show('Please fill in product name, category, and base price', 'warning');
    return;
  }

  try {
    const res = await SmartAPI.post('api/v1/products/index.php', { name, category_id, sku, price, default_station_id, short_description });
    if (res.success) {
      SmartNotifications.show('Product created successfully!', 'success');
      SmartModal.close('create-product-modal');
      loadProductCatalog();
    }
  } catch (err) {
    SmartNotifications.show(err.message || 'Failed to create product', 'danger');
  }
}

async function submitCreateCategory() {
  const name = document.getElementById('new-cat-name').value.trim();
  const description = document.getElementById('new-cat-desc').value.trim();

  if (!name) {
    SmartNotifications.show('Please enter category name', 'warning');
    return;
  }

  try {
    const res = await SmartAPI.post('api/v1/categories/index.php', { name, description });
    if (res.success) {
      SmartNotifications.show('Category created successfully!', 'success');
      SmartModal.close('create-category-modal');
      loadCategoryOptions();
      loadProductCatalog();
    }
  } catch (err) {
    SmartNotifications.show(err.message || 'Failed to create category', 'danger');
  }
}

async function submitCreateModifier() {
  const name = document.getElementById('new-mod-name').value.trim();
  const price = document.getElementById('new-mod-price').value;

  if (!name) {
    SmartNotifications.show('Please enter modifier name', 'warning');
    return;
  }

  try {
    const res = await SmartAPI.post('api/v1/modifiers/index.php', { action: 'create', name, price });
    if (res.success) {
      SmartNotifications.show('Modifier created successfully!', 'success');
      SmartModal.close('create-modifier-modal');
    }
  } catch (err) {
    SmartNotifications.show(err.message || 'Failed to create modifier', 'danger');
  }
}

async function toggleProductAvailability(productId, isAvailable) {
  try {
    const res = await SmartAPI.post('api/v1/products/toggle_availability.php', { product_id: productId, is_available: isAvailable });
    if (res.success) {
      SmartNotifications.show(res.message, 'success');
      loadProductCatalog();
    }
  } catch (err) {
    SmartNotifications.show(err.message || 'Failed to toggle availability', 'danger');
  }
}

async function openVariantsModal(productId, productName) {
  document.getElementById('variant-product-id').value = productId;
  document.getElementById('variants-modal-title').textContent = `Variants for ${productName}`;
  SmartModal.open('manage-variants-modal');
  loadProductVariantsList(productId);
}

async function loadProductVariantsList(productId) {
  const container = document.getElementById('variants-list-container');
  try {
    const res = await SmartAPI.get(`api/v1/variants/index.php?product_id=${productId}`);
    if (res.data && res.data.length > 0) {
      container.innerHTML = res.data.map(v => `
        <div style="display:flex; justify-content:space-between; align-items:center; padding: 8px 12px; background:var(--surface); border:1px solid var(--border); border-radius:6px; margin-bottom:6px;">
          <div><strong>${v.variant_name}</strong> ${v.sku ? `<span class="text-muted font-mono">(${v.sku})</span>` : ''}</div>
          <div class="price-tag">৳${parseFloat(v.price).toFixed(2)}</div>
        </div>
      `).join('');
    } else {
      container.innerHTML = `<div style="text-align:center; color:var(--text-muted); padding:20px;">No size/type variants added yet.</div>`;
    }
  } catch (e) {
    container.innerHTML = `<div style="text-align:center; color:var(--danger);">Failed to load variants.</div>`;
  }
}

async function submitCreateVariant() {
  const productId = document.getElementById('variant-product-id').value;
  const name = document.getElementById('new-variant-name').value.trim();
  const price = document.getElementById('new-variant-price').value;

  if (!name || price === '') {
    SmartNotifications.show('Please enter variant name and price', 'warning');
    return;
  }

  try {
    const res = await SmartAPI.post('api/v1/variants/index.php', { product_id: productId, variant_name: name, price });
    if (res.success) {
      SmartNotifications.show('Variant added!', 'success');
      document.getElementById('new-variant-name').value = '';
      document.getElementById('new-variant-price').value = '';
      loadProductVariantsList(productId);
      loadProductCatalog();
    }
  } catch (err) {
    SmartNotifications.show(err.message || 'Failed to add variant', 'danger');
  }
}

async function openModifiersModal(productId, productName) {
  document.getElementById('modifiers-product-id').value = productId;
  document.getElementById('modifiers-modal-title').textContent = `Modifiers for ${productName}`;
  SmartModal.open('manage-modifiers-modal');
  loadProductModifiersChecklist(productId);
}

async function loadProductModifiersChecklist(productId) {
  const container = document.getElementById('modifiers-selection-list');
  try {
    const [modsRes, prodRes] = await Promise.all([
      SmartAPI.get('api/v1/modifiers/index.php'),
      SmartAPI.get(`api/v1/products/detail.php?id=${productId}`)
    ]);

    const allMods = modsRes.data || [];
    const attachedIds = (prodRes.data && prodRes.data.modifiers) ? prodRes.data.modifiers.map(m => m.id) : [];

    if (allMods.length > 0) {
      container.innerHTML = allMods.map(m => {
        const isChecked = attachedIds.includes(m.id);
        return `
          <label style="display:flex; align-items:center; justify-content:space-between; padding:10px 14px; background:var(--surface); border:1px solid var(--border); border-radius:6px; cursor:pointer;">
            <div style="display:flex; align-items:center; gap:10px;">
              <input type="checkbox" ${isChecked ? 'checked' : ''} onchange="toggleModifierAttachment(${productId}, ${m.id}, this.checked)">
              <strong>${m.name}</strong>
            </div>
            <span class="price-tag">+৳${parseFloat(m.price).toFixed(2)}</span>
          </label>
        `;
      }).join('');
    } else {
      container.innerHTML = `<div style="text-align:center; color:var(--text-muted); padding:20px;">No global modifiers created yet. Use "+ Create Modifier" first.</div>`;
    }
  } catch (e) {
    container.innerHTML = `<div style="text-align:center; color:var(--danger);">Failed to load modifiers checklist.</div>`;
  }
}

async function toggleModifierAttachment(productId, modifierId, isChecked) {
  const action = isChecked ? 'attach' : 'detach';
  try {
    const res = await SmartAPI.post('api/v1/modifiers/index.php', { action, product_id: productId, modifier_id: modifierId });
    if (res.success) {
      SmartNotifications.show(isChecked ? 'Modifier attached!' : 'Modifier detached!', 'info');
      loadProductCatalog();
    }
  } catch (err) {
    SmartNotifications.show(err.message || 'Failed to update modifier relationship', 'danger');
  }
}

let cachedTables = [];

async function loadFloorTables() {
  const container = document.getElementById('floor-tables-container');
  try {
    const res = await SmartAPI.get('api/v1/tables/index.php');
    if (res.data && res.data.length > 0) {
      cachedTables = res.data;
      let activeCount = 0;
      let totalCapacity = 0;

      container.innerHTML = res.data.map(t => {
        if (t.status === 'OCCUPIED') activeCount++;
        totalCapacity += parseInt(t.capacity || 0);

        let badgeClass = 'badge-success';
        if (t.status === 'OCCUPIED') badgeClass = 'badge-warning';
        if (t.status === 'RESERVED') badgeClass = 'badge-info';
        if (t.status === 'OUT_OF_SERVICE') badgeClass = 'badge-danger';

        return `
          <div class="table-card status-${t.status.toLowerCase()}">
            <div class="table-header-row">
              <span class="table-num">${t.table_number}</span>
              <span class="badge ${badgeClass}">${t.status}</span>
            </div>
            <div class="text-sm" style="color:var(--text-secondary);">
              <div>Floor: <strong>${t.floor_name || 'Main Floor'}</strong></div>
              <div>Capacity: <strong>${t.capacity} Guests</strong></div>
              ${t.active_session_id ? `<div style="margin-top:4px; color:var(--accent-dark); font-weight:600;">Session #${t.active_session_id} (${t.guest_count} Guests)</div>` : ''}
              ${t.waiter_name ? `<div>Waiter: <strong>${t.waiter_name}</strong></div>` : ''}
            </div>
            <div style="margin-top: var(--space-2); display:flex; gap:6px;">
              ${t.status === 'AVAILABLE' ? `
                <button class="btn btn-primary btn-sm" style="width:100%;" onclick="openSessionModal(${t.id}, '${t.table_number}')">Open Session</button>
              ` : ''}
              ${t.status === 'OCCUPIED' ? `
                <button class="btn btn-secondary btn-sm" onclick="openTransferModal(${t.active_session_id})">Transfer</button>
                <button class="btn btn-danger btn-sm" onclick="closeSession(${t.active_session_id})">Close</button>
              ` : ''}
            </div>
          </div>
        `;
      }).join('');

      document.getElementById('kpi-tables').textContent = `${activeCount} / ${res.data.length}`;
    } else {
      container.innerHTML = `<div style="grid-column: 1/-1; text-align:center; padding: 40px; color: var(--text-muted);">No dining tables configured in database.</div>`;
    }
  } catch (err) {
    container.innerHTML = `<div style="grid-column: 1/-1; text-align:center; padding: 40px; color: var(--danger);">Unable to load floor tables from database. Showing clean state.</div>`;
  }
}

function openSessionModal(tableId, tableNum) {
  document.getElementById('open-session-table-id').value = tableId;
  document.getElementById('session-modal-title').textContent = `Open Dining Session on ${tableNum}`;
  SmartModal.open('open-session-modal');
}

async function submitOpenSession() {
  const tableId = document.getElementById('open-session-table-id').value;
  const guestCount = document.getElementById('open-guest-count').value;
  const notes = document.getElementById('open-session-notes').value;

  try {
    const res = await SmartAPI.post('api/v1/dining_sessions/open.php', { table_id: tableId, guest_count: guestCount, notes });
    if (res.success) {
      SmartNotifications.show(res.message || 'Dining session successfully opened!', 'success');
      SmartModal.close('open-session-modal');
      loadFloorTables();
    }
  } catch (err) {
    SmartNotifications.show(err.message || 'Failed to open session', 'danger');
  }
}

function openTransferModal(sessionId) {
  document.getElementById('transfer-session-id').value = sessionId;
  const select = document.getElementById('transfer-dest-table');
  const availableTables = cachedTables.filter(t => t.status === 'AVAILABLE');
  
  if (availableTables.length === 0) {
    SmartNotifications.show('No available destination tables on floor', 'warning');
    return;
  }

  select.innerHTML = availableTables.map(t => `<option value="${t.id}">${t.table_number} (${t.floor_name || 'Floor'} - ${t.capacity} Guests)</option>`).join('');
  SmartModal.open('transfer-table-modal');
}

async function submitTransferTable() {
  const sessionId = document.getElementById('transfer-session-id').value;
  const destTableId = document.getElementById('transfer-dest-table').value;

  try {
    const res = await SmartAPI.post('api/v1/dining_sessions/transfer.php', { session_id: sessionId, destination_table_id: destTableId });
    if (res.success) {
      SmartNotifications.show(res.message || 'Session transferred successfully!', 'success');
      SmartModal.close('transfer-table-modal');
      loadFloorTables();
    }
  } catch (err) {
    SmartNotifications.show(err.message || 'Table transfer failed', 'danger');
  }
}

async function closeSession(sessionId) {
  if (!confirm('Are you sure you want to close this active dining session?')) return;
  try {
    const res = await SmartAPI.post('api/v1/dining_sessions/close.php', { session_id: sessionId });
    if (res.success) {
      SmartNotifications.show('Dining session closed and table set AVAILABLE', 'success');
      loadFloorTables();
    }
  } catch (err) {
    SmartNotifications.show(err.message || 'Failed to close session', 'danger');
  }
}

async function submitCreateTable() {
  const floorId = document.getElementById('new-table-floor').value;
  const tableNumber = document.getElementById('new-table-number').value.trim();
  const capacity = document.getElementById('new-table-capacity').value;

  if (!tableNumber) {
    SmartNotifications.show('Please enter a table number/code', 'warning');
    return;
  }

  try {
    const res = await SmartAPI.post('api/v1/tables/index.php', { floor_id: floorId, table_number: tableNumber, capacity });
    if (res.success) {
      SmartNotifications.show(res.message || 'Table created successfully', 'success');
      SmartModal.close('create-table-modal');
      loadFloorTables();
    }
  } catch (err) {
    SmartNotifications.show(err.message || 'Failed to create table', 'danger');
  }
}

async function handleLogout() {
  try {
    await SmartAPI.post('api/v1/auth/logout.php', {});
    SmartNotifications.show('Successfully logged out', 'info');
    setTimeout(() => { window.location.href = 'public/login.php'; }, 400);
  } catch (e) {
    window.location.href = 'public/login.php';
  }
}

async function loadWaiterMatrix() {
  const tbody = document.getElementById('waiter-matrix-tbody');
  try {
    const res = await SmartAPI.get('api/v1/waiters/get_matrix.php');
    if (res.data && res.data.length > 0) {
      let totalSales = 0, totalComm = 0;
      tbody.innerHTML = res.data.map(w => {
        totalSales += parseFloat(w.total_sales || 0);
        totalComm += parseFloat(w.commission_amount || 0);
        return `
          <tr>
            <td><strong>${w.waiter_name}</strong></td>
            <td>${w.total_orders || 0}</td>
            <td>${w.tables_served || 0}</td>
            <td class="price-tag">${parseFloat(w.total_sales || 0).toLocaleString()}</td>
            <td>${w.paid_orders || 0}</td>
            <td>${w.pending_orders || 0}</td>
            <td><strong>৳${parseFloat(w.commission_amount || 0).toLocaleString()}</strong></td>
            <td><span class="badge badge-success">${w.status}</span></td>
          </tr>
        `;
      }).join('');
      document.getElementById('kpi-sales').textContent = `৳${totalSales.toLocaleString()}`;
      document.getElementById('kpi-commission').textContent = `৳${totalComm.toLocaleString()}`;
    } else {
      tbody.innerHTML = `<tr><td colspan="8" style="text-align:center; padding: 40px; color: var(--text-muted);">No waiter performance data recorded in database.</td></tr>`;
    }
  } catch(e) {
    tbody.innerHTML = `<tr><td colspan="8" style="text-align:center; padding: 40px; color: var(--danger);">Unable to connect to database. Showing clean empty state.</td></tr>`;
  }
}

async function loadActiveOrders(statusFilter = '') {
  const tbody = document.getElementById('orders-tbody');
  let url = 'api/v1/orders/index.php';
  if (statusFilter) url += `?order_status=${statusFilter}`;

  try {
    const res = await SmartAPI.get(url);
    if (res.data && res.data.length > 0) {
      document.getElementById('kpi-orders').textContent = res.data.length;
      tbody.innerHTML = res.data.map(o => {
        let badgeClass = 'badge-info';
        if (o.order_status === 'SUBMITTED') badgeClass = 'badge-warning';
        if (o.order_status === 'PREPARING') badgeClass = 'badge-warning';
        if (o.order_status === 'READY') badgeClass = 'badge-success';
        if (o.order_status === 'COMPLETED') badgeClass = 'badge-neutral';
        if (o.order_status === 'CANCELLED') badgeClass = 'badge-danger';

        return `
          <tr>
            <td><span class="font-mono">${o.order_number}</span></td>
            <td>${o.table_number ? `Table ${o.table_number}` : (o.order_type || 'Takeaway')}</td>
            <td>${o.waiter_name || 'Staff'}</td>
            <td><span class="badge badge-neutral">${o.item_count || 0} Items</span></td>
            <td class="price-tag">৳${parseFloat(o.total || 0).toFixed(2)}</td>
            <td><span class="badge ${o.payment_status === 'PAID' ? 'badge-success' : 'badge-warning'}">${o.payment_status || 'UNPAID'}</span></td>
            <td><span class="badge ${badgeClass}">${o.order_status}</span></td>
            <td>
              <button class="btn btn-secondary btn-sm" onclick="openOrderDetailModal(${o.id})">Details</button>
              <button class="btn btn-primary btn-sm" onclick="SmartRouting.openOrderRoutingModal(${o.id})">Routing</button>
              <button class="btn btn-success btn-sm" onclick="SmartBilling.openBillingModal(${o.id})">Pay / Bill</button>
            </td>
          </tr>
        `;
      }).join('');
    } else {
      tbody.innerHTML = `<tr><td colspan="8" style="text-align:center; padding: 40px; color: var(--text-muted);">No orders found.</td></tr>`;
    }
  } catch(e) {
    tbody.innerHTML = `<tr><td colspan="8" style="text-align:center; padding: 40px; color: var(--danger);">Unable to load orders from database.</td></tr>`;
  }
}

async function openOrderDetailModal(orderId) {
  try {
    const res = await SmartAPI.get(`api/v1/orders/detail.php?id=${orderId}`);
    if (!res.success || !res.data) {
      SmartNotifications.show('Failed to load order details', 'danger');
      return;
    }

    const o = res.data;
    document.getElementById('order-modal-title').textContent = `Order #${o.order_number}`;
    document.getElementById('order-modal-subtitle').textContent = `Type: ${o.order_type} | Table: ${o.table_number || 'N/A'} | Waiter: ${o.taken_by_name || 'Staff'}`;

    const body = document.getElementById('order-modal-body');
    body.innerHTML = `
      <div style="margin-bottom:16px; display:flex; justify-content:space-between; align-items:center;">
        <div>
          <span class="badge badge-info">Status: ${o.order_status}</span>
          <span class="badge ${o.payment_status === 'PAID' ? 'badge-success' : 'badge-warning'}" style="margin-left:6px;">Payment: ${o.payment_status}</span>
        </div>
        <div class="text-sm text-muted">Created: ${o.created_at}</div>
      </div>

      <table class="data-table" style="margin-bottom:16px;">
        <thead>
          <tr>
            <th>ITEM</th>
            <th>QTY</th>
            <th>UNIT PRICE</th>
            <th>MODIFIERS</th>
            <th>LINE TOTAL</th>
          </tr>
        </thead>
        <tbody>
          ${(o.items || []).map(i => `
            <tr>
              <td><strong>${i.item_name}</strong> ${i.variant_name ? `<small>(${i.variant_name})</small>` : ''}</td>
              <td>${i.quantity}</td>
              <td>৳${parseFloat(i.unit_price).toFixed(2)}</td>
              <td>
                ${(i.modifiers || []).length > 0 ? i.modifiers.map(m => `<small class="badge badge-neutral">+ ${m.modifier_name} (৳${m.unit_price})</small>`).join(' ') : '<span class="text-muted">None</span>'}
              </td>
              <td class="price-tag">৳${parseFloat(i.subtotal).toFixed(2)}</td>
            </tr>
          `).join('')}
        </tbody>
      </table>

      <div style="background:var(--surface); border:1px solid var(--border); padding:16px; border-radius:8px;">
        <div style="display:flex; justify-content:space-between; margin-bottom:4px;"><span>Subtotal</span><span>৳${parseFloat(o.subtotal).toFixed(2)}</span></div>
        <div style="display:flex; justify-content:space-between; margin-bottom:4px;"><span>VAT (5%)</span><span>৳${parseFloat(o.tax).toFixed(2)}</span></div>
        <div style="display:flex; justify-content:space-between; font-weight:700; font-size:1.1rem; border-top:1px dashed var(--border); padding-top:8px; margin-top:8px;">
          <span>Grand Total</span>
          <span class="price-tag">৳${parseFloat(o.total).toFixed(2)}</span>
        </div>
      </div>
    `;

    const footer = document.getElementById('order-modal-footer');
    let actionButtons = `<button class="btn btn-secondary" onclick="SmartModal.close('order-details-modal')">Close</button>`;

    if (o.order_status === 'DRAFT') {
      actionButtons += `<button class="btn btn-primary" onclick="advanceOrderStatus(${o.id}, 'SUBMITTED')">Submit Order</button>`;
    } else if (o.order_status === 'SUBMITTED') {
      actionButtons += `<button class="btn btn-primary" onclick="advanceOrderStatus(${o.id}, 'PREPARING')">Advance to Preparing</button>`;
    } else if (o.order_status === 'PREPARING') {
      actionButtons += `<button class="btn btn-success" onclick="advanceOrderStatus(${o.id}, 'READY')">Mark Ready</button>`;
    } else if (o.order_status === 'READY') {
      actionButtons += `<button class="btn btn-success" onclick="advanceOrderStatus(${o.id}, 'SERVED')">Mark Served</button>`;
    } else if (o.order_status === 'SERVED') {
      actionButtons += `<button class="btn btn-neutral" onclick="advanceOrderStatus(${o.id}, 'COMPLETED')">Complete Order</button>`;
    }

    if (!['COMPLETED', 'CANCELLED', 'REFUNDED'].includes(o.order_status)) {
      actionButtons += `<button class="btn btn-danger" onclick="cancelOrderAction(${o.id})">Cancel Order</button>`;
    }

    footer.innerHTML = actionButtons;
    SmartModal.open('order-details-modal');

  } catch (err) {
    SmartNotifications.show(err.message || 'Failed to view order details', 'danger');
  }
}

async function advanceOrderStatus(orderId, newStatus) {
  try {
    const res = await SmartAPI.post('api/v1/orders/status.php', { order_id: orderId, status: newStatus });
    if (res.success) {
      SmartNotifications.show(`Order status updated to ${newStatus}`, 'success');
      SmartModal.close('order-details-modal');
      loadActiveOrders();
    }
  } catch (err) {
    SmartNotifications.show(err.message || 'Failed to update order status', 'danger');
  }
}

async function cancelOrderAction(orderId) {
  const reason = prompt('Please enter cancellation reason:');
  if (!reason) return;

  try {
    const res = await SmartAPI.post('api/v1/orders/cancel.php', { order_id: orderId, reason });
    if (res.success) {
      SmartNotifications.show('Order cancelled successfully', 'info');
      SmartModal.close('order-details-modal');
      loadActiveOrders();
    }
  } catch (err) {
    SmartNotifications.show(err.message || 'Failed to cancel order', 'danger');
  }
}

async function submitCreateStation() {
  const name = document.getElementById('new-station-name').value.trim();
  const type = document.getElementById('new-station-type').value;
  const badge_code = document.getElementById('new-station-code').value.trim();

  if (!name) {
    SmartNotifications.show('Please enter station name', 'warning');
    return;
  }

  try {
    const res = await SmartAPI.post('api/v1/kds/stations.php', { action: 'create', name, type, badge_code });
    if (res.success) {
      SmartNotifications.show(`Operational Station "${name}" created!`, 'success');
      SmartModal.close('manage-station-modal');
      document.getElementById('new-station-name').value = '';
      document.getElementById('new-station-code').value = '';
      if (typeof SmartKDS !== 'undefined') {
        await SmartKDS.loadStations();
        await SmartKDS.loadKDSGrid();
      }
    }
  } catch (err) {
    SmartNotifications.show(err.message || 'Failed to create station', 'danger');
  }
}

document.addEventListener('DOMContentLoaded', () => {
  loadWaiterMatrix();
  loadActiveOrders();
});
</script>

</body>
</html>
