<?php
/**
 * SMARTRESTA - Main Platform Shell & Production System Architecture
 * Prompt 13 Architecture Repair: Real Role-Based Server-Side Portals
 * Tech Stack: HTML5, CSS3, Vanilla ES6+ JS, PHP 8.x, MySQL 8.x PDO
 */

require_once __DIR__ . '/config/env.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/core/Auth.php';
require_once __DIR__ . '/core/CSRF.php';
require_once __DIR__ . '/core/Router.php';

Auth::requireAuth();

CSRF::init();
$csrfToken = CSRF::getToken();
$currentUser = Auth::user();
$userRole = Auth::role();
$userRoleKey = Router::normalizeRole($userRole);

// Direct access to /index.php redirects to user's assigned portal
if (!isset($currentPortal)) {
    Router::redirectToPortal();
}

$activePortal = Router::normalizeRole($currentPortal ?? $userRoleKey);

// Define Portal Configurations, Navigation Items, and Allowed Views
$portalConfig = [
    'admin' => [
        'name' => 'Admin Portal',
        'badge' => 'ADMIN',
        'default_section' => 'admin',
        'nav' => [
            ['id' => 'admin', 'icon' => '📊', 'label' => 'Admin Overview'],
            ['id' => 'users', 'icon' => '👥', 'label' => 'Users & Staff Roles'],
            ['id' => 'tables', 'icon' => '🪑', 'label' => 'Floors & Tables'],
            ['id' => 'menu', 'icon' => '🍔', 'label' => 'Menu & Catalog'],
            ['id' => 'waiter', 'icon' => '🍷', 'label' => 'Waiter Workspace'],
            ['id' => 'pos', 'icon' => '💳', 'label' => 'POS & Ordering'],
            ['id' => 'kds', 'icon' => '🍳', 'label' => 'Kitchen Display (KDS)'],
            ['id' => 'kitchen', 'icon' => '👨‍🍳', 'label' => 'Kitchen Production'],
            ['id' => 'routing', 'icon' => '🔀', 'label' => 'Station Routing'],
            ['id' => 'payments', 'icon' => '💰', 'label' => 'Payments & Billing'],
            ['id' => 'commission-rules', 'icon' => '📜', 'label' => 'Commission Rules'],
            ['id' => 'commissions-review', 'icon' => '📑', 'label' => 'Commissions Review'],
            ['id' => 'payouts', 'icon' => '💵', 'label' => 'Payout Settlements'],
            ['id' => 'inventory', 'icon' => '📦', 'label' => 'Stock & Inventory'],
            ['id' => 'reports', 'icon' => '📈', 'label' => 'Reports & Analytics'],
            ['id' => 'finance', 'icon' => '💵', 'label' => 'Finance & Day Close'],
            ['id' => 'crm', 'icon' => '🤝', 'label' => 'CRM & Reservations'],
        ],
        'views' => ['admin', 'users', 'tables', 'menu', 'waiter', 'pos', 'kds', 'kitchen', 'routing', 'payments', 'commission_rules', 'commissions_review', 'payouts', 'inventory', 'reports', 'finance', 'crm']
    ],
    'manager' => [
        'name' => 'Manager Portal',
        'badge' => 'MANAGER',
        'default_section' => 'admin',
        'nav' => [
            ['id' => 'admin', 'icon' => '📊', 'label' => 'Manager Dashboard'],
            ['id' => 'waiter', 'icon' => '🍷', 'label' => 'Waiter Performance'],
            ['id' => 'reception', 'icon' => '🛎️', 'label' => 'Reception Front Desk'],
            ['id' => 'tables', 'icon' => '🪑', 'label' => 'Floors & Tables'],
            ['id' => 'menu', 'icon' => '🍔', 'label' => 'Menu Performance'],
            ['id' => 'kds', 'icon' => '🍳', 'label' => 'Kitchen Monitor'],
            ['id' => 'kitchen', 'icon' => '👨‍🍳', 'label' => 'Kitchen Production'],
            ['id' => 'payments', 'icon' => '💰', 'label' => 'Payments History'],
            ['id' => 'commissions-review', 'icon' => '📑', 'label' => 'Commission Approvals'],
            ['id' => 'inventory', 'icon' => '📦', 'label' => 'Inventory & Stock'],
            ['id' => 'reports', 'icon' => '📈', 'label' => 'Reports & Analytics'],
            ['id' => 'finance', 'icon' => '💵', 'label' => 'Finance & Shifts'],
            ['id' => 'crm', 'icon' => '🤝', 'label' => 'Customers & CRM'],
        ],
        'views' => ['admin', 'waiter', 'reception', 'tables', 'menu', 'kds', 'kitchen', 'payments', 'commissions_review', 'inventory', 'reports', 'finance', 'crm']
    ],
    'reception' => [
        'name' => 'Reception Portal',
        'badge' => 'RECEPTION',
        'default_section' => 'reception',
        'nav' => [
            ['id' => 'reception', 'icon' => '🛎️', 'label' => 'Reception Front Desk'],
            ['id' => 'pos', 'icon' => '💳', 'label' => 'POS Billing & Checkout'],
            ['id' => 'tables', 'icon' => '🪑', 'label' => 'Tables & Sessions'],
            ['id' => 'payments', 'icon' => '💰', 'label' => 'Billing & Payment History'],
            ['id' => 'crm', 'icon' => '🤝', 'label' => 'Customers & Reservations'],
        ],
        'views' => ['reception', 'pos', 'tables', 'payments', 'crm']
    ],
    'waiter' => [
        'name' => 'Waiter Portal',
        'badge' => 'WAITER',
        'default_section' => 'waiter',
        'nav' => [
            ['id' => 'waiter', 'icon' => '🍷', 'label' => 'Waiter Workspace'],
            ['id' => 'pos', 'icon' => '💳', 'label' => 'POS & Table Ordering'],
            ['id' => 'tables', 'icon' => '🪑', 'label' => 'Floors & Tables'],
            ['id' => 'kds', 'icon' => '🍳', 'label' => 'Kitchen Feed'],
        ],
        'views' => ['waiter', 'pos', 'tables', 'kds']
    ],
    'kitchen' => [
        'name' => 'Kitchen Portal',
        'badge' => 'KITCHEN',
        'default_section' => 'kitchen',
        'nav' => [
            ['id' => 'kitchen', 'icon' => '👨‍🍳', 'label' => 'Kitchen Production'],
            ['id' => 'kds', 'icon' => '🍳', 'label' => 'Kitchen Display (KDS)'],
            ['id' => 'routing', 'icon' => '🔀', 'label' => 'Station Routing'],
            ['id' => 'inventory', 'icon' => '📦', 'label' => 'Stock & Ingredients'],
        ],
        'views' => ['kitchen', 'kds', 'routing', 'inventory']
    ]
];

$activeConfig = $portalConfig[$activePortal] ?? $portalConfig['admin'];
$requestedSection = $_GET['section'] ?? $_GET['view'] ?? null;
if ($requestedSection && in_array(str_replace('-', '_', $requestedSection), $activeConfig['views'], true)) {
    $initialSection = $requestedSection;
} else {
    $initialSection = $initialSection ?? $activeConfig['default_section'];
}

$scriptPath = $_SERVER['SCRIPT_NAME'] ?? '';
$isPhpSubdir = (
    strpos($scriptPath, '/php/') !== false ||
    strpos($scriptPath, '/admin/') !== false ||
    strpos($scriptPath, '/manager/') !== false ||
    strpos($scriptPath, '/reception/') !== false ||
    strpos($scriptPath, '/waiter/') !== false ||
    strpos($scriptPath, '/kitchen/') !== false
);
$assetPrefix = $isPhpSubdir ? '../' : './';
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
  <title><?= htmlspecialchars($activeConfig['name']) ?> — SMARTRESTA</title>

  <!-- Google Fonts: Poppins -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <!-- Modular CSS Architecture -->
  <link rel="stylesheet" href="<?= $assetPrefix ?>assets/css/variables.css">
  <link rel="stylesheet" href="<?= $assetPrefix ?>assets/css/reset.css">
  <link rel="stylesheet" href="<?= $assetPrefix ?>assets/css/typography.css">
  <link rel="stylesheet" href="<?= $assetPrefix ?>assets/css/layout.css">
  <link rel="stylesheet" href="<?= $assetPrefix ?>assets/css/components.css">
  <link rel="stylesheet" href="<?= $assetPrefix ?>assets/css/tables.css">
  <link rel="stylesheet" href="<?= $assetPrefix ?>assets/css/forms.css">
  <link rel="stylesheet" href="<?= $assetPrefix ?>assets/css/pos.css">
  <link rel="stylesheet" href="<?= $assetPrefix ?>assets/css/kitchen.css">
  <link rel="stylesheet" href="<?= $assetPrefix ?>assets/css/responsive.css">
  <link rel="stylesheet" href="<?= $assetPrefix ?>assets/css/dark-mode.css">

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
        <span class="logo-badge"><?= htmlspecialchars($activeConfig['badge']) ?></span>
      </div>
    </div>

    <nav class="sidebar-nav">
      <div class="nav-section-title"><?= htmlspecialchars(strtoupper($activeConfig['name'])) ?></div>
      <?php foreach ($activeConfig['nav'] as $item): ?>
        <a href="#<?= htmlspecialchars($item['id']) ?>" 
           class="nav-item <?= ($initialSection === $item['id']) ? 'active' : '' ?>" 
           onclick="switchRoleView('<?= htmlspecialchars($item['id']) ?>', this, event)">
          <span class="nav-icon"><?= $item['icon'] ?></span>
          <span><?= htmlspecialchars($item['label']) ?></span>
        </a>
      <?php endforeach; ?>

      <div class="nav-section-title" style="margin-top:16px;">ACCOUNT</div>
      <a href="javascript:void(0)" class="nav-item" onclick="handleLogout()" style="color: var(--danger-color, #ef4444);">
        <span class="nav-icon">🔒</span>
        <span>Sign Out / Logout</span>
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
          <h1 id="view-title"><?= htmlspecialchars($activeConfig['name']) ?></h1>
          <span class="text-sm">User: <strong><?= htmlspecialchars($currentUser['name'] ?? 'Staff') ?></strong> (<span class="badge badge-info"><?= htmlspecialchars(strtoupper($userRole)) ?></span>) | Portal: <strong style="color:var(--accent);"><?= htmlspecialchars(strtoupper($activePortal)) ?></strong> | Branch: Main Outlet</span>
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
        <button class="btn btn-primary" onclick="SmartModal.open('modal-create-reservation')">
          <span>+</span>
          <span>Quick Reservation</span>
        </button>
      </div>
    </header>

    <!-- Dynamic View Container -->
    <div class="page-container">

      <?php
      foreach ($activeConfig['views'] as $v) {
          $viewFile = __DIR__ . '/views/' . $v . '.php';
          if (file_exists($viewFile)) {
              require_once $viewFile;
          }
      }
      ?>

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

<!-- Modal: Create Staff User Account -->
<div id="create-user-modal" class="modal-backdrop">
  <div class="modal-content" style="max-width:520px;">
    <div class="modal-header">
      <h3>Create Staff Account</h3>
      <button class="btn btn-secondary btn-sm" onclick="SmartModal.close('create-user-modal')">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label class="form-label">Full Name *</label>
        <input type="text" id="new-user-name" class="form-control" placeholder="e.g. Salim Khan" required>
      </div>
      <div class="form-group">
        <label class="form-label">Email Address (Login ID) *</label>
        <input type="email" id="new-user-email" class="form-control" placeholder="salim@smartresta.com" required>
      </div>
      <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
        <div class="form-group">
          <label class="form-label">Password *</label>
          <input type="password" id="new-user-password" class="form-control" placeholder="••••••••" required>
        </div>
        <div class="form-group">
          <label class="form-label">Phone Number</label>
          <input type="tel" id="new-user-phone" class="form-control" placeholder="01700000000">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Assigned Staff Role *</label>
        <select id="new-user-role" class="form-control" required>
          <option value="admin">Admin (Full System Access)</option>
          <option value="manager">Manager (Operations & Finance)</option>
          <option value="reception">Reception / Cashier (Billing & Front Desk)</option>
          <option value="waiter" selected>Waiter (POS & Floor Ordering)</option>
          <option value="kitchen">Kitchen Staff (KDS & Production)</option>
        </select>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="SmartModal.close('create-user-modal')">Cancel</button>
      <button class="btn btn-primary" onclick="submitCreateUser()">Create User Account</button>
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
      <div class="form-group">
        <label class="form-label">Product Picture (Upload File or Paste Image URL)</label>
        <input type="file" id="new-prod-image-file" class="form-control" accept="image/*" style="margin-bottom: 0.5rem;">
        <input type="text" id="new-prod-image-url" class="form-control" placeholder="Or paste image URL (e.g. https://.../burger.jpg)">
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="SmartModal.close('create-product-modal')">Cancel</button>
      <button class="btn btn-primary" onclick="submitCreateProduct()">Create Product</button>
    </div>
  </div>
</div>

<!-- Edit Product Modal (Admin Only) -->
<div id="edit-product-modal" class="modal-backdrop">
  <div class="modal-content" style="max-width:580px;">
    <div class="modal-header">
      <h3>✏️ Edit Product</h3>
      <button class="btn btn-secondary btn-sm" onclick="SmartModal.close('edit-product-modal')">✕</button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="edit-prod-id">
      <div class="form-group">
        <label class="form-label">Product Name *</label>
        <input type="text" id="edit-prod-name" class="form-control" placeholder="e.g. Chicken Burger">
      </div>
      <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
        <div class="form-group">
          <label class="form-label">Category</label>
          <select id="edit-prod-category" class="form-control">
            <!-- Populated dynamically -->
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Base Price (৳ BDT) *</label>
          <input type="number" step="0.01" id="edit-prod-price" class="form-control" placeholder="0.00">
        </div>
      </div>
      <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
        <div class="form-group">
          <label class="form-label">SKU (Code)</label>
          <input type="text" id="edit-prod-sku" class="form-control" placeholder="e.g. BRG-001">
        </div>
        <div class="form-group">
          <label class="form-label">Station Routing</label>
          <select id="edit-prod-station" class="form-control">
            <option value="1">Main Kitchen</option>
            <option value="2">Beverage & Bar Counter</option>
            <option value="3">Dessert Station</option>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Short Description</label>
        <input type="text" id="edit-prod-desc" class="form-control" placeholder="Brief description...">
      </div>
      <div class="form-group">
        <label class="form-label">Product Picture</label>
        <input type="file" id="edit-prod-image-file" class="form-control" accept="image/*" style="margin-bottom:0.5rem;">
        <input type="text" id="edit-prod-image-url" class="form-control" placeholder="Or paste image URL">
      </div>
      <div class="form-group">
        <label class="form-label">Availability</label>
        <select id="edit-prod-available" class="form-control">
          <option value="1">Available</option>
          <option value="0">Unavailable</option>
        </select>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="SmartModal.close('edit-product-modal')">Cancel</button>
      <button class="btn btn-primary" onclick="submitEditProduct()">💾 Save Changes</button>
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

<!-- Select Active Order for Payment Settlement Modal -->
<div id="order-select-modal" class="modal-backdrop">
  <div class="modal-content" style="max-width:840px;">
    <div class="modal-header">
      <div>
        <h3>Select Active Order to Settle Payment</h3>
        <span class="text-sm">Choose an active dining or takeaway order with outstanding balance</span>
      </div>
      <button class="btn btn-secondary btn-sm" onclick="SmartModal.close('order-select-modal')">✕</button>
    </div>
    <div class="modal-body">
      <div class="table-container">
        <table class="data-table">
          <thead>
            <tr>
              <th>ORDER ID</th>
              <th>TABLE / TYPE</th>
              <th>WAITER</th>
              <th>TOTAL</th>
              <th>PAID</th>
              <th>BALANCE</th>
              <th>ACTION</th>
            </tr>
          </thead>
          <tbody id="order-select-modal-tbody">
            <tr><td colspan="7" style="text-align:center; padding: 40px; color: var(--text-muted);">Loading active unpaid orders...</td></tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="SmartModal.close('order-select-modal')">Cancel</button>
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
function switchRoleView(viewId, navEl, event) {
  if (event) {
    try { event.preventDefault(); } catch (e) {}
  }
  const cleanId = (viewId || 'admin').replace(/-view$/, '');

  document.querySelectorAll('.role-view').forEach(view => view.style.display = 'none');
  document.querySelectorAll('.nav-item').forEach(item => item.classList.remove('active'));

  const target = document.getElementById(cleanId) || document.getElementById(cleanId + '-view');
  if (target) {
    target.style.display = 'block';
  } else {
    console.warn(`Section #${cleanId} not found in DOM`);
  }

  if (navEl) {
    navEl.classList.add('active');
  } else {
    const link = document.querySelector(`.sidebar-nav a[href*="${cleanId}"]`);
    if (link) link.classList.add('active');
  }

  try {
    window.location.hash = '#' + cleanId;
  } catch (e) {}

  const titles = {
    'admin': 'Manager Dashboard',
    'waiter': 'Waiter Station & Table Operations',
    'reception': 'Reception & Cashier Front Desk',
    'tables': 'Restaurant Floor Map & Tables',
    'menu': 'Restaurant Menu, Categories & Product Catalog',
    'pos': 'POS & Waiter Ordering',
    'kds': 'Kitchen Display System (KDS)',
    'kitchen': 'Kitchen Display System (KDS) & Station Production',
    'users': 'Users & Staff Role Permissions',
    'routing': 'Smart Order Routing & Station Dispatch',
    'payments': 'Billing Engine & Settlement History',
    'commission-rules': 'Configurable Commission Rules Engine',
    'commissions-review': 'Commission Transaction Review & Approvals',
    'payouts': 'Commission Payout Settlements History',
    'inventory': 'Inventory, Purchasing, Recipe Costing & Stock Control Engine',
    'reports': 'Reports & Operational Analytics',
    'finance': 'Finance, Shifts & Day Closing Engine',
    'crm': 'CRM, Reservations, Loyalty, Coupons & QR Ordering Engine'
  };
  const titleEl = document.getElementById('view-title');
  if (titleEl) titleEl.textContent = titles[cleanId] || titles[viewId] || 'SMARTRESTA';

  if (cleanId === 'waiter') {
    if (typeof SmartWaiter !== 'undefined' && typeof SmartWaiter.refreshAll === 'function') SmartWaiter.refreshAll();
  } else if (cleanId === 'reception') {
    if (typeof SmartReception !== 'undefined' && typeof SmartReception.refreshAll === 'function') SmartReception.refreshAll();
  } else if (cleanId === 'tables') {
    if (typeof loadFloorTables === 'function') loadFloorTables();
  } else if (cleanId === 'pos') {
    if (typeof loadPOSProducts === 'function') loadPOSProducts();
    if (typeof loadPOSTableSelector === 'function') loadPOSTableSelector();
  } else if (cleanId === 'menu') {
    if (typeof loadCategoryOptions === 'function') loadCategoryOptions();
    if (typeof loadProductCatalog === 'function') loadProductCatalog();
  } else if (cleanId === 'users') {
    if (typeof loadUsersList === 'function') loadUsersList();
  } else if (cleanId === 'payments') {
    if (typeof SmartBilling !== 'undefined' && typeof SmartBilling.loadPaymentHistory === 'function') SmartBilling.loadPaymentHistory();
  } else if (cleanId === 'commission-rules') {
    if (typeof SmartCommissions !== 'undefined' && typeof SmartCommissions.loadCommissionRules === 'function') SmartCommissions.loadCommissionRules();
  } else if (cleanId === 'commissions-review') {
    if (typeof SmartCommissions !== 'undefined' && typeof SmartCommissions.loadCommissionsReview === 'function') SmartCommissions.loadCommissionsReview();
  } else if (cleanId === 'payouts') {
    if (typeof SmartCommissions !== 'undefined' && typeof SmartCommissions.loadPayoutHistory === 'function') SmartCommissions.loadPayoutHistory();
  } else if (cleanId === 'kds') {
    if (typeof SmartKDS !== 'undefined' && typeof SmartKDS.loadKDSGrid === 'function') SmartKDS.loadKDSGrid();
  } else if (cleanId === 'kitchen') {
    if (typeof SmartKitchen !== 'undefined' && typeof SmartKitchen.refreshAll === 'function') SmartKitchen.refreshAll();
  } else if (cleanId === 'inventory') {
    if (typeof SmartInventory !== 'undefined' && typeof SmartInventory.init === 'function') SmartInventory.init();
  } else if (cleanId === 'reports') {
    if (typeof SmartReports !== 'undefined' && typeof SmartReports.loadCurrentTab === 'function') SmartReports.loadCurrentTab();
  } else if (cleanId === 'finance') {
    if (typeof SmartFinance !== 'undefined' && typeof SmartFinance.init === 'function') SmartFinance.init();
  } else if (cleanId === 'crm') {
    if (typeof SmartCRM !== 'undefined' && typeof SmartCRM.init === 'function') SmartCRM.init();
  } else if (cleanId === 'admin') {
    if (typeof loadWaiterMatrix === 'function') loadWaiterMatrix();
    if (typeof loadActiveOrders === 'function') loadActiveOrders();
  }
}

window.onpopstate = function(e) {
  const pathMatch = window.location.pathname.match(/\/php\/([a-z0-9_-]+)\.php/i);
  const targetSection = pathMatch ? pathMatch[1] : (e.state && e.state.section ? e.state.section : 'admin');
  switchRoleView(targetSection);
};

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
      tbody.innerHTML = res.data.map(p => {
        const thumb = p.image_url || 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=100&auto=format&fit=crop&q=80';
        return `
        <tr>
          <td>
            <div style="display:flex; align-items:center; gap:10px;">
              <img src="${thumb}" alt="${p.name}" style="width:40px; height:40px; border-radius:8px; object-fit:cover; border: 1px solid var(--border-color);" onerror="this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=100&auto=format&fit=crop&q=80'">
              <div>
                <strong>${p.name}</strong>
                ${p.short_description ? `<br><small style="color:var(--text-muted);">${p.short_description}</small>` : ''}
              </div>
            </div>
          </td>
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
            ${window.CURRENT_USER_ROLE && (window.CURRENT_USER_ROLE.toLowerCase().includes('admin') || window.CURRENT_USER_ROLE.toLowerCase().includes('manager')) ? `
            <button class="btn btn-warning btn-sm" onclick="openEditProductModal(${p.id}, '${p.name.replace(/'/g,'\\')}', '${p.category_id}', '${p.price}', '${p.sku || ''}', '${(p.short_description||'').replace(/'/g,'\\'')}', '${p.image_url||''}', '${p.default_station_id||''}', '${p.is_available ? 1:0}')" style="margin-left:4px;">✏️ Edit</button>
            <button class="btn btn-danger btn-sm" onclick="deleteProductAction(${p.id}, '${p.name.replace(/'/g,'\\'')}')" style="margin-left:4px;">🗑️ Delete</button>
            ` : ''}
          </td>
        </tr>
      `}).join('');
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

  const imageFileInput = document.getElementById('new-prod-image-file');
  const imageUrlInput = document.getElementById('new-prod-image-url');
  let image_url = imageUrlInput ? imageUrlInput.value.trim() : null;

  if (!name || !category_id || price === '') {
    SmartNotifications.show('Please fill in product name, category, and base price', 'warning');
    return;
  }

  try {
    // 1. Handle direct file upload if selected
    if (imageFileInput && imageFileInput.files && imageFileInput.files.length > 0) {
      const formData = new FormData();
      formData.append('image', imageFileInput.files[0]);

      const uploadRes = await fetch('api/v1/products/upload.php', {
        method: 'POST',
        body: formData
      });
      const uploadData = await uploadRes.json();
      if (uploadData.success && uploadData.data && uploadData.data.image_url) {
        image_url = uploadData.data.image_url;
      }
    }

    // 2. Submit Product Creation
    const res = await SmartAPI.post('api/v1/products/index.php', { 
      name, category_id, sku, price, default_station_id, short_description, image_url 
    });

    if (res.success) {
      SmartNotifications.show('Product created successfully!', 'success');
      SmartModal.close('create-product-modal');
      // Reset inputs
      document.getElementById('new-prod-name').value = '';
      document.getElementById('new-prod-sku').value = '';
      document.getElementById('new-prod-price').value = '';
      document.getElementById('new-prod-desc').value = '';
      if (imageFileInput) imageFileInput.value = '';
      if (imageUrlInput) imageUrlInput.value = '';
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
  const loginPath = window.location.pathname.includes('/public/') ? 'login.php' : 'public/login.php';
  try {
    if (window.SmartAPI && typeof window.SmartAPI.post === 'function') {
      await SmartAPI.post('api/v1/auth/logout.php', {});
    } else {
      const apiPath = window.location.pathname.includes('/public/') ? '../api/v1/auth/logout.php' : 'api/v1/auth/logout.php';
      await fetch(apiPath, { method: 'POST' });
    }
    if (window.SmartNotifications && typeof window.SmartNotifications.show === 'function') {
      SmartNotifications.show('Successfully logged out', 'info');
    }
    setTimeout(() => { window.location.href = loginPath; }, 300);
  } catch (e) {
    window.location.href = loginPath;
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

window.CURRENT_USER_ROLE = "<?= htmlspecialchars($userRole, ENT_QUOTES, 'UTF-8') ?>";
window.CURRENT_PORTAL = "<?= htmlspecialchars($activePortal, ENT_QUOTES, 'UTF-8') ?>";
window.INITIAL_ACTIVE_SECTION = "<?= htmlspecialchars($initialSection ?? '', ENT_QUOTES, 'UTF-8') ?>";

document.addEventListener('DOMContentLoaded', () => {
  const portalNavIds = <?= json_encode(array_column($activeConfig['nav'], 'id')) ?>;
  const hashClean = window.location.hash ? window.location.hash.replace('#', '').replace(/-view$/, '') : null;

  let initialView = hashClean || window.INITIAL_ACTIVE_SECTION || portalNavIds[0] || 'admin';
  if (!portalNavIds.includes(initialView)) {
    initialView = portalNavIds[0] || window.INITIAL_ACTIVE_SECTION;
  }

  const targetNavLink = document.querySelector(`.sidebar-nav a[href*="${initialView}"]`);
  switchRoleView(initialView, targetNavLink);

  // Load operational data for authenticated roles
  try { if (typeof loadPOSProducts === 'function') loadPOSProducts(); } catch (e) {}
  try { if (typeof loadPOSTableSelector === 'function') loadPOSTableSelector(); } catch (e) {}
  try { if (typeof loadActiveOrders === 'function') loadActiveOrders(); } catch (e) {}
  if (['admin', 'manager'].includes(window.CURRENT_PORTAL)) {
    try { if (typeof loadWaiterMatrix === 'function') loadWaiterMatrix(); } catch (e) {}
    try { if (typeof loadUsersList === 'function') loadUsersList(); } catch (e) {}
  }
});

async function loadUsersList() {
  const tbody = document.getElementById('users-table-tbody');
  if (!tbody) return;
  try {
    const res = await SmartAPI.get('api/v1/users/index.php');
    if (res.data && res.data.length > 0) {
      tbody.innerHTML = res.data.map(u => `
        <tr>
          <td><strong>${u.name}</strong></td>
          <td>${u.email}</td>
          <td>${u.phone || 'N/A'}</td>
          <td><span class="badge badge-info">${(u.role || 'staff').toUpperCase()}</span></td>
          <td><small class="text-muted">${u.last_login || 'Never'}</small></td>
          <td><span class="badge ${u.status === 'ACTIVE' ? 'badge-success' : 'badge-danger'}">${u.status}</span></td>
          <td>
            <button class="btn btn-secondary btn-sm" onclick="alert('User account #${u.id} active')">Status</button>
          </td>
        </tr>
      `).join('');
    } else {
      tbody.innerHTML = `<tr><td colspan="7" style="text-align:center; padding: 40px; color: var(--text-muted);">No staff accounts found.</td></tr>`;
    }
  } catch (e) {
    tbody.innerHTML = `<tr><td colspan="7" style="text-align:center; padding: 40px; color: var(--danger);">Failed to load users from database.</td></tr>`;
  }
}

async function submitCreateUser() {
  const name = document.getElementById('new-user-name').value.trim();
  const email = document.getElementById('new-user-email').value.trim();
  const password = document.getElementById('new-user-password').value;
  const role = document.getElementById('new-user-role').value;
  const phone = document.getElementById('new-user-phone').value.trim();

  if (!name || !email || !password) {
    SmartNotifications.show('Name, Email, and Password are required', 'warning');
    return;
  }

  try {
    const res = await SmartAPI.post('api/v1/users/create.php', { name, email, password, role, phone });
    if (res.success) {
      SmartNotifications.show(`User account created for ${name} (${role.toUpperCase()})!`, 'success');
      SmartModal.close('create-user-modal');
      document.getElementById('new-user-name').value = '';
      document.getElementById('new-user-email').value = '';
      document.getElementById('new-user-password').value = '';
      document.getElementById('new-user-phone').value = '';
      loadUsersList();
    } else {
      SmartNotifications.show(res.message || 'Failed to create user account', 'danger');
    }
  } catch (err) {
    SmartNotifications.show(err.message || 'Failed to create user account', 'danger');
  }
}


// ==========================================
// POS Catalog & Table Selector
// ==========================================
let posAllProducts = [];
let posCurrentCatId = '';

async function loadPOSProducts() {
  const grid = document.getElementById('pos-product-grid');
  const pillContainer = document.getElementById('pos-category-pills');
  if (!grid || !pillContainer) return;

  grid.innerHTML = `<div style="grid-column:1/-1; text-align:center; padding:40px; color:var(--text-muted);"><div style="font-size:2rem;">⏳</div><p>Loading menu items...</p></div>`;

  try {
    const [prodRes, catRes] = await Promise.all([
      SmartAPI.get('api/v1/products/index.php?is_available=1'),
      SmartAPI.get('api/v1/categories/index.php')
    ]);

    posAllProducts = prodRes.data || [];

    // Build category pills
    const cats = catRes.data || [];
    pillContainer.innerHTML = `<button class="category-pill active" data-cat="" onclick="filterPOSCategory('', this)">All Items</button>`
      + cats.map(c => `<button class="category-pill" data-cat="${c.id}" onclick="filterPOSCategory('${c.id}', this)">${c.name}</button>`).join('');

    renderPOSGrid(posAllProducts);
  } catch (err) {
    grid.innerHTML = `<div style="grid-column:1/-1; text-align:center; padding:40px; color:var(--danger);">Failed to load menu. Please refresh.</div>`;
  }
}

function filterPOSCategory(catId, btn) {
  posCurrentCatId = catId;
  document.querySelectorAll('#pos-category-pills .category-pill').forEach(b => b.classList.remove('active'));
  if (btn) btn.classList.add('active');

  const filtered = catId ? posAllProducts.filter(p => String(p.category_id) === String(catId)) : posAllProducts;
  renderPOSGrid(filtered);
}

function renderPOSGrid(products) {
  const grid = document.getElementById('pos-product-grid');
  if (!grid) return;

  const isAdminRole = window.CURRENT_USER_ROLE &&
    (window.CURRENT_USER_ROLE.toLowerCase().includes('admin') || window.CURRENT_USER_ROLE.toLowerCase().includes('manager'));


  if (!products || products.length === 0) {
    grid.innerHTML = `<div style="grid-column:1/-1; text-align:center; padding:40px; color:var(--text-muted);"><div style="font-size:2.5rem; margin-bottom:8px;">🍽️</div><p>No items found in this category.</p></div>`;
    return;
  }

  grid.innerHTML = products.map(p => {
    const thumb = p.image_url || '';
    const imgHtml = thumb ? `<img src="${thumb}" alt="${p.name}" style="width:100%; height:100px; object-fit:cover; border-radius:8px 8px 0 0; display:block;" onerror="this.style.display='none'">` : '';
    const adminBtns = isAdminRole ? `
      <div style="display:flex; gap:4px; margin-top:6px;">
        <button class="btn btn-warning btn-sm" style="flex:1; font-size:0.7rem; padding:3px 6px;" onclick="event.stopPropagation(); openEditProductModal(${p.id}, '${(p.name||'').replace(/'/g,"&apos;")}', '${p.category_id}', '${p.price}', '${p.sku||''}', '${(p.short_description||'').replace(/'/g,"&apos;")}', '${p.image_url||''}', '${p.default_station_id||1}', '${p.is_available?1:0}')">✏️ Edit</button>
        <button class="btn btn-danger btn-sm" style="flex:1; font-size:0.7rem; padding:3px 6px;" onclick="event.stopPropagation(); deleteProductAction(${p.id}, '${(p.name||'').replace(/'/g,"&apos;")}')">🗑️ Del</button>
      </div>
    ` : '';
    return `
      <div class="product-card" onclick="SmartPOS.addItem(${p.id}, '${(p.name||'').replace(/'/g,"&apos;")}', ${p.price}, '${p.station_name || 'Main Kitchen'}')" style="cursor:pointer; padding:0; overflow:hidden;">
        ${imgHtml}
        <div style="padding:10px;">
          <div class="product-title">${p.name}</div>
          ${p.short_description ? `<div class="text-sm" style="margin-bottom:4px; color:var(--text-muted);">${p.short_description}</div>` : ''}
          <div class="product-meta">
            <span class="price-tag">৳${parseFloat(p.price).toFixed(2)}</span>
            <span class="product-add-btn">+</span>
          </div>
          ${adminBtns}
        </div>
      </div>
    `;
  }).join('');
}

async function loadPOSTableSelector() {
  const select = document.getElementById('pos-table-selector');
  if (!select) return;
  try {
    const res = await SmartAPI.get('api/v1/tables/index.php');
    if (res.data && res.data.length > 0) {
      select.innerHTML = `<option value="">— Pick a table —</option>`
        + res.data.map(t => `<option value="${t.id}" data-num="${t.table_number}" data-session="${t.active_session_id || ''}">${t.table_number} (${t.status}${t.active_session_id ? ' - Session #' + t.active_session_id : ''})</option>`).join('');
    }
  } catch (e) { console.warn('POS table selector load failed', e); }
}

function onPOSTableSelected() {
  const select = document.getElementById('pos-table-selector');
  const opt = select.options[select.selectedIndex];
  const tableId = opt.value;
  const tableNum = opt.getAttribute('data-num') || '';
  const sessionId = opt.getAttribute('data-session') || null;
  if (tableId) {
    SmartPOS.setTableAndSession(tableId, tableNum, sessionId);
    const heading = document.getElementById('pos-table-heading-cart');
    if (heading) heading.textContent = `Table ${tableNum} Order`;
    const badge = document.getElementById('pos-table-heading');
    if (badge) badge.textContent = `Table ${tableNum}${sessionId ? ' · Session #' + sessionId : ''}`;
  }
}

// ==========================================
// Edit & Delete Product (Admin)
// ==========================================
async function openEditProductModal(id, name, catId, price, sku, desc, imageUrl, stationId, isAvailable) {
  document.getElementById('edit-prod-id').value = id;
  document.getElementById('edit-prod-name').value = name || '';
  document.getElementById('edit-prod-price').value = price || '';
  document.getElementById('edit-prod-sku').value = sku || '';
  document.getElementById('edit-prod-desc').value = desc || '';
  document.getElementById('edit-prod-image-url').value = imageUrl || '';
  document.getElementById('edit-prod-available').value = isAvailable ? '1' : '0';
  const stationSelect = document.getElementById('edit-prod-station');
  if (stationId) stationSelect.value = stationId;

  // Populate category dropdown
  try {
    const catRes = await SmartAPI.get('api/v1/categories/index.php');
    const cats = catRes.data || [];
    const catSelect = document.getElementById('edit-prod-category');
    catSelect.innerHTML = cats.map(c => `<option value="${c.id}" ${String(c.id) === String(catId) ? 'selected' : ''}>${c.name}</option>`).join('');
  } catch (e) {
    console.warn('Failed to load categories for edit modal');
  }

  SmartModal.open('edit-product-modal');
}

async function submitEditProduct() {
  const id = document.getElementById('edit-prod-id').value;
  const name = document.getElementById('edit-prod-name').value.trim();
  const category_id = document.getElementById('edit-prod-category').value;
  const price = document.getElementById('edit-prod-price').value;
  const sku = document.getElementById('edit-prod-sku').value.trim();
  const short_description = document.getElementById('edit-prod-desc').value.trim();
  const is_available = document.getElementById('edit-prod-available').value;
  const default_station_id = document.getElementById('edit-prod-station').value;
  const imageFileInput = document.getElementById('edit-prod-image-file');
  let image_url = document.getElementById('edit-prod-image-url').value.trim();

  if (!name || !price) {
    SmartNotifications.show('Product name and price are required', 'warning');
    return;
  }

  try {
    // Handle image upload if a new file selected
    if (imageFileInput && imageFileInput.files && imageFileInput.files.length > 0) {
      const formData = new FormData();
      formData.append('image', imageFileInput.files[0]);
      const uploadRes = await fetch('api/v1/products/upload.php', { method: 'POST', body: formData });
      const uploadData = await uploadRes.json();
      if (uploadData.success && uploadData.data && uploadData.data.image_url) {
        image_url = uploadData.data.image_url;
      }
    }

    const res = await SmartAPI.put(`api/v1/products/index.php?id=${id}`, {
      id, name, category_id, price, sku, short_description, image_url, is_available, default_station_id
    });

    if (res.success) {
      SmartNotifications.show('Product updated successfully!', 'success');
      SmartModal.close('edit-product-modal');
      loadProductCatalog();
      loadPOSProducts();
    } else {
      SmartNotifications.show(res.message || 'Failed to update product', 'danger');
    }
  } catch (err) {
    SmartNotifications.show(err.message || 'Failed to update product', 'danger');
  }
}

async function deleteProductAction(id, name) {
  if (!confirm(`Are you sure you want to delete "${name}"? This action cannot be undone.`)) return;
  try {
    const res = await SmartAPI.delete(`api/v1/products/index.php?id=${id}`, { id });
    if (res.success) {
      SmartNotifications.show(`Product "${name}" deleted successfully`, 'success');
      loadProductCatalog();
      loadPOSProducts();
    } else {
      SmartNotifications.show(res.message || 'Failed to delete product', 'danger');
    }
  } catch (err) {
    SmartNotifications.show(err.message || 'Failed to delete product', 'danger');
  }
}
</script>

<!-- Core Infrastructure JS -->
<script src="<?= $assetPrefix ?>assets/js/notifications.js"></script>
<script src="<?= $assetPrefix ?>assets/js/ajax.js"></script>
<script src="<?= $assetPrefix ?>assets/js/modal.js"></script>
<script src="<?= $assetPrefix ?>assets/js/app.js"></script>

<!-- Module Operational JS Controllers -->
<script src="<?= $assetPrefix ?>assets/js/pos.js"></script>
<script src="<?= $assetPrefix ?>assets/js/kds.js"></script>
<script src="<?= $assetPrefix ?>assets/js/routing.js"></script>
<script src="<?= $assetPrefix ?>assets/js/billing.js"></script>
<script src="<?= $assetPrefix ?>assets/js/commissions.js"></script>
<script src="<?= $assetPrefix ?>assets/js/inventory.js"></script>
<script src="<?= $assetPrefix ?>assets/js/reports.js"></script>
<script src="<?= $assetPrefix ?>assets/js/finance.js"></script>
<script src="<?= $assetPrefix ?>assets/js/crm.js"></script>
<script src="<?= $assetPrefix ?>assets/js/reception.js"></script>
<script src="<?= $assetPrefix ?>assets/js/waiter.js"></script>
<script src="<?= $assetPrefix ?>assets/js/kitchen.js"></script>

</body>
</html>
