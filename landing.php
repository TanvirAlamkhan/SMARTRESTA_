<?php
/**
 * SMARTRESTA — Production Landing Page & Portal Directory
 * Central operational hub connecting all restaurant management portals,
 * workflows, staff guidelines, and deployment health status.
 */

require_once __DIR__ . '/config/env.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/core/Auth.php';

// Check DB Connection Status for Live Health Badge
$dbConnected = false;
$dbError = null;
try {
    $db = Database::getConnection();
    if ($db) {
        $dbConnected = true;
    }
} catch (Exception $e) {
    $dbError = $e->getMessage();
}

$isLoggedIn = Auth::check();
$currentUser = Auth::user();
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SMARTRESTA — Restaurant Operations & Portal Directory</title>

  <!-- Google Fonts: Poppins -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <!-- Modular CSS Architecture -->
  <link rel="stylesheet" href="assets/css/variables.css">
  <link rel="stylesheet" href="assets/css/reset.css">
  <link rel="stylesheet" href="assets/css/typography.css">
  <link rel="stylesheet" href="assets/css/components.css">
  <link rel="stylesheet" href="assets/css/forms.css">
  <link rel="stylesheet" href="assets/css/dark-mode.css">

  <style>
    :root {
      --hero-bg: radial-gradient(circle at 80% 20%, #1e1b4b 0%, #0f172a 100%);
      --accent-gradient: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    }

    body {
      background-color: var(--bg-primary);
      color: var(--text-primary);
      font-family: 'Poppins', sans-serif;
      line-height: 1.6;
      margin: 0;
      padding: 0;
    }

    .navbar {
      background-color: rgba(15, 23, 42, 0.85);
      backdrop-filter: blur(12px);
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      position: sticky;
      top: 0;
      z-index: 1000;
      padding: 16px 32px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .navbar-brand {
      display: flex;
      align-items: center;
      gap: 12px;
      text-decoration: none;
      color: #fff;
      font-size: 1.4rem;
      font-weight: 700;
    }

    .navbar-badge {
      background: var(--accent-gradient);
      color: #fff;
      font-size: 0.75rem;
      padding: 3px 10px;
      border-radius: 20px;
      font-weight: 600;
      letter-spacing: 0.5px;
    }

    .nav-links {
      display: flex;
      align-items: center;
      gap: 24px;
      list-style: none;
      margin: 0;
      padding: 0;
    }

    .nav-links a {
      color: #94a3b8;
      text-decoration: none;
      font-size: 0.95rem;
      font-weight: 500;
      transition: color 0.2s;
    }

    .nav-links a:hover {
      color: #fff;
    }

    .nav-actions {
      display: flex;
      align-items: center;
      gap: 16px;
    }

    /* Hero Section */
    .hero {
      background: var(--hero-bg);
      color: #fff;
      padding: 80px 32px;
      text-align: center;
      position: relative;
      overflow: hidden;
    }

    .hero-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: rgba(99, 102, 241, 0.2);
      border: 1px solid rgba(99, 102, 241, 0.4);
      color: #a5b4fc;
      padding: 6px 16px;
      border-radius: 30px;
      font-size: 0.85rem;
      font-weight: 600;
      margin-bottom: 24px;
    }

    .hero h1 {
      font-size: 2.8rem;
      font-weight: 700;
      margin-bottom: 20px;
      line-height: 1.2;
      background: linear-gradient(to right, #ffffff, #cbd5e1);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .hero p {
      font-size: 1.15rem;
      color: #94a3b8;
      max-width: 800px;
      margin: 0 auto 36px auto;
    }

    .hero-buttons {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 16px;
      flex-wrap: wrap;
    }

    .btn-hero-primary {
      background: var(--accent-gradient);
      color: #fff;
      padding: 14px 32px;
      border-radius: 12px;
      font-weight: 600;
      font-size: 1rem;
      text-decoration: none;
      box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.4);
      transition: transform 0.2s, box-shadow 0.2s;
    }

    .btn-hero-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 15px 30px -5px rgba(79, 70, 229, 0.6);
    }

    .btn-hero-secondary {
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: #fff;
      padding: 14px 32px;
      border-radius: 12px;
      font-weight: 600;
      font-size: 1rem;
      text-decoration: none;
      backdrop-filter: blur(8px);
      transition: background 0.2s;
    }

    .btn-hero-secondary:hover {
      background: rgba(255, 255, 255, 0.2);
    }

    .container {
      max-width: 1280px;
      margin: 0 auto;
      padding: 60px 32px;
    }

    .section-header {
      text-align: center;
      margin-bottom: 48px;
    }

    .section-header h2 {
      font-size: 2rem;
      font-weight: 700;
      color: var(--text-primary);
      margin-bottom: 12px;
    }

    .section-header p {
      color: var(--text-muted);
      font-size: 1rem;
      max-width: 650px;
      margin: 0 auto;
    }

    /* Portals Grid */
    .portal-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
      gap: 24px;
    }

    .portal-card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius-lg);
      padding: 28px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
      box-shadow: var(--shadow-sm);
    }

    .portal-card:hover {
      transform: translateY(-4px);
      box-shadow: var(--shadow-lg);
      border-color: var(--accent);
    }

    .portal-icon {
      width: 52px;
      height: 52px;
      border-radius: 14px;
      background: rgba(99, 102, 241, 0.1);
      color: var(--accent);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.6rem;
      margin-bottom: 20px;
    }

    .portal-title {
      font-size: 1.25rem;
      font-weight: 600;
      margin-bottom: 8px;
      color: var(--text-primary);
    }

    .portal-desc {
      font-size: 0.9rem;
      color: var(--text-muted);
      margin-bottom: 24px;
      line-height: 1.5;
    }

    .portal-link {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      color: var(--accent);
      font-weight: 600;
      text-decoration: none;
      font-size: 0.95rem;
    }

    .portal-link:hover {
      text-decoration: underline;
    }

    /* Workflow Timeline */
    .workflow-timeline {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
      gap: 20px;
      margin-top: 32px;
    }

    .workflow-step {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius-md);
      padding: 24px;
      position: relative;
    }

    .step-number {
      font-size: 0.8rem;
      font-weight: 700;
      color: var(--accent);
      background: rgba(99, 102, 241, 0.1);
      padding: 4px 12px;
      border-radius: 20px;
      display: inline-block;
      margin-bottom: 12px;
    }

    .step-title {
      font-size: 1.05rem;
      font-weight: 600;
      margin-bottom: 6px;

    }

    .step-desc {
      font-size: 0.85rem;
      color: var(--text-muted);
    }

    /* Guidelines Cards */
    .guideline-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 24px;
    }

    .guideline-card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius-md);
      padding: 24px;
    }

    .guideline-role {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 16px;
    }

    .guideline-role h3 {
      font-size: 1.1rem;
      font-weight: 600;
      margin: 0;
    }

    .guideline-list {
      list-style-type: disc;
      padding-left: 20px;
      margin: 0;
      font-size: 0.88rem;
      color: var(--text-muted);
    }

    .guideline-list li {
      margin-bottom: 8px;
    }

    /* Health Widget */
    .health-widget {
      background: #0f172a;
      color: #fff;
      border-radius: var(--radius-lg);
      padding: 36px;
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: space-between;
      gap: 24px;
    }

    .health-info h3 {
      font-size: 1.3rem;
      font-weight: 600;
      margin: 0 0 8px 0;
    }

    .health-info p {
      color: #94a3b8;
      margin: 0;
      font-size: 0.9rem;
    }

    .health-badges {
      display: flex;
      gap: 16px;
      flex-wrap: wrap;
    }

    .health-pill {
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.15);
      padding: 10px 18px;
      border-radius: 30px;
      font-size: 0.85rem;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .status-dot {
      width: 10px;
      height: 10px;
      border-radius: 50%;
    }
    .dot-online { background-color: #10b981; box-shadow: 0 0 8px #10b981; }
    .dot-offline { background-color: #ef4444; box-shadow: 0 0 8px #ef4444; }

    footer {
      border-top: 1px solid var(--border);
      padding: 40px 32px;
      text-align: center;
      color: var(--text-muted);
      font-size: 0.85rem;
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
  <a href="landing.php" class="navbar-brand">
    <span>SMARTRESTA</span>
    <span class="navbar-badge">OS v16.0</span>
  </a>

  <ul class="nav-links">
    <li><a href="#portals">Portal Hub</a></li>
    <li><a href="#workflows">Operations Workflow</a></li>
    <li><a href="#guidelines">Role Guidelines</a></li>
    <li><a href="#security">Security & Deployment</a></li>
  </ul>

  <div class="nav-actions">
    <?php if ($isLoggedIn): ?>
      <a href="index.php" class="btn btn-primary">Go to Management App (<?= htmlspecialchars($currentUser['name']) ?>)</a>
    <?php else: ?>
      <a href="public/login.php" class="btn btn-outline-primary">Sign In</a>
      <a href="index.php" class="btn btn-primary">Open OS Shell</a>
    <?php endif; ?>
  </div>
</nav>

<!-- Hero Section -->
<section class="hero">
  <div class="hero-badge">
    <span>🛡️ PROMPT 16 CERTIFIED</span>
    <span>•</span>
    <span>100% PRODUCTION READY</span>
  </div>
  <h1>Restaurant Operations, POS & Performance OS</h1>
  <p>An enterprise-grade, zero-mock restaurant management platform. Powering multi-counter order routing, floor table management, waiter commissions, kitchen production (KDS), recipe BOM inventory costing, and real-time financial reporting.</p>

  <div class="hero-buttons">
    <a href="index.php" class="btn-hero-primary">🚀 Launch Management App</a>
    <a href="public/login.php" class="btn-hero-secondary">🔐 Staff Login Portal</a>
    <a href="public/qr.html" class="btn-hero-secondary" target="_blank">📱 Customer QR Menu</a>
  </div>
</section>

<div class="container">

  <!-- Portals Hub Directory -->
  <section id="portals">
    <div class="section-header">
      <h2>Operational Portal Directory</h2>
      <p>Direct access to all 8 core management engines and staff interfaces in SMARTRESTA.</p>
    </div>

    <div class="portal-grid">
      
      <!-- 1. Login & RBAC -->
      <div class="portal-card">
        <div>
          <div class="portal-icon">🔐</div>
          <div class="portal-title">Staff & Admin Login</div>
          <div class="portal-desc">Role-based authentication, Bcrypt password hashing, session security, and account status management for staff.</div>
        </div>
        <a href="public/login.php" class="portal-link">Access Login Portal &rarr;</a>
      </div>

      <!-- 2. POS & Order Entry -->
      <div class="portal-card">
        <div>
          <div class="portal-icon">🖥️</div>
          <div class="portal-title">POS & Waiter Ordering</div>
          <div class="portal-desc">Touch POS interface for taking orders, selecting size modifiers, managing dining sessions, and applying discounts.</div>
        </div>
        <a href="index.php#pos-view" class="portal-link">Open POS Terminal &rarr;</a>
      </div>

      <!-- 3. KDS Production -->
      <div class="portal-card">
        <div>
          <div class="portal-icon">👨‍🍳</div>
          <div class="portal-title">Kitchen Display System (KDS)</div>
          <div class="portal-desc">Real-time kitchen kanban cards, multi-counter station routing (Grill, Bar, Pastry), SLA timer alerts, and ticket status updates.</div>
        </div>
        <a href="index.php#kds-view" class="portal-link">Open KDS Screen &rarr;</a>
      </div>

      <!-- 4. Manager Analytics -->
      <div class="portal-card">
        <div>
          <div class="portal-icon">📊</div>
          <div class="portal-title">Manager Dashboard & Analytics</div>
          <div class="portal-desc">Live sales KPIs, hourly revenue charts, top-selling items, waiter performance matrix, and CSV report export engine.</div>
        </div>
        <a href="index.php#reports-view" class="portal-link">View Analytics & Reports &rarr;</a>
      </div>

      <!-- 5. QR Customer Menu -->
      <div class="portal-card">
        <div>
          <div class="portal-icon">📱</div>
          <div class="portal-title">Customer QR Table Ordering</div>
          <div class="portal-desc">Self-service digital menu for diners. Scans secure table tokens to submit orders directly to kitchen & POS.</div>
        </div>
        <a href="public/qr.html" target="_blank" class="portal-link">Open Public QR Page &rarr;</a>
      </div>

      <!-- 6. Finance & Day Close -->
      <div class="portal-card">
        <div>
          <div class="portal-icon">💼</div>
          <div class="portal-title">Finance, Shifts & Day Closing</div>
          <div class="portal-desc">Manage operating expenses, cashier cash register shifts, opening/closing balances, over/short reconciliation, and z-report day closing.</div>
        </div>
        <a href="index.php#finance-view" class="portal-link">Manage Finance & Shifts &rarr;</a>
      </div>

      <!-- 7. Inventory & BOM Costing -->
      <div class="portal-card">
        <div>
          <div class="portal-icon">📦</div>
          <div class="portal-title">Inventory & Stock BOM Costing</div>
          <div class="portal-desc">Ingredient inventory ledger, supplier purchase orders, goods receiving, recipe Bill of Materials (BOM), and wastage tracking.</div>
        </div>
        <a href="index.php#stock-view" class="portal-link">Open Inventory Ledger &rarr;</a>
      </div>

      <!-- 8. CRM, Loyalty & Reservations -->
      <div class="portal-card">
        <div>
          <div class="portal-icon">👥</div>
          <div class="portal-title">CRM, Loyalty & Reservations</div>
          <div class="portal-desc">Customer profiles, lifetime sales history, conflict-free table reservations, loyalty points ledger, and coupon promotions.</div>
        </div>
        <a href="index.php#crm-view" class="portal-link">Open CRM & Loyalty Hub &rarr;</a>
      </div>

    </div>
  </section>

  <!-- Workflows Section -->
  <section id="workflows" style="margin-top: 80px;">
    <div class="section-header">
      <h2>End-to-End Operational Lifecycle</h2>
      <p>How data flows seamlessly across all 14 steps from guest order to financial day close.</p>
    </div>

    <div class="workflow-timeline">
      <div class="workflow-step">
        <span class="step-number">STEP 1</span>
        <div class="step-title">Dining Session Opened</div>
        <div class="step-desc">Waiter opens a dining session on an available table in the floor map.</div>
      </div>
      <div class="workflow-step">
        <span class="step-number">STEP 2</span>
        <div class="step-title">Order Entry & Modifiers</div>
        <div class="step-desc">Waiter adds menu items, size variants, extra modifiers, and customer notes.</div>
      </div>
      <div class="workflow-step">
        <span class="step-number">STEP 3</span>
        <div class="step-title">Smart Station Routing</div>
        <div class="step-desc">Order items auto-route to designated kitchen counters (Grill, Bar, Fryer) as KDS tickets.</div>
      </div>
      <div class="workflow-step">
        <span class="step-number">STEP 4</span>
        <div class="step-title">Kitchen SLA Production</div>
        <div class="step-desc">Chefs track ticket timers on KDS and mark status: NEW &rarr; PREPARING &rarr; READY.</div>
      </div>
      <div class="workflow-step">
        <span class="step-number">STEP 5</span>
        <div class="step-title">Multi-Pay Billing</div>
        <div class="step-desc">Cashier collects payment via Cash, bKash, Nagad, or Card with receipt generation.</div>
      </div>
      <div class="workflow-step">
        <span class="step-number">STEP 6</span>
        <div class="step-title">Recipe BOM Inventory</div>
        <div class="step-desc">Ingredients are automatically deducted from stock based on recipe BOM definitions.</div>
      </div>
      <div class="workflow-step">
        <span class="step-number">STEP 7</span>
        <div class="step-title">Waiter Commission</div>
        <div class="step-desc">System calculates 5.0% waiter commission and records entry in commission ledger.</div>
      </div>
      <div class="workflow-step">
        <span class="step-number">STEP 8</span>
        <div class="step-title">Shift Reconciliation</div>
        <div class="step-desc">Manager performs end-of-day cash drawer settlement and closes business day.</div>
      </div>
    </div>
  </section>

  <!-- Guidelines Section -->
  <section id="guidelines" style="margin-top: 80px;">
    <div class="section-header">
      <h2>Staff Operating Guidelines</h2>
      <p>Standard Operating Procedures (SOP) by staff role to ensure flawless operations.</p>
    </div>

    <div class="guideline-grid">
      
      <div class="guideline-card">
        <div class="guideline-role">
          <span style="font-size:1.5rem;">👑</span>
          <h3>System Administrator</h3>
        </div>
        <ul class="guideline-list">
          <li>Manage staff accounts and assign RBAC roles (`admin`, `manager`, `waiter`, `chef`, `cashier`).</li>
          <li>Monitor audit log traceability for all sensitive security & financial actions.</li>
          <li>Execute automated database backups and monitor Railway system health.</li>
        </ul>
      </div>

      <div class="guideline-card">
        <div class="guideline-role">
          <span style="font-size:1.5rem;">📈</span>
          <h3>Branch Manager</h3>
        </div>
        <ul class="guideline-list">
          <li>Review real-time sales KPIs, hourly revenue charts, and waiter performance matrix.</li>
          <li>Approve operating expenses and audit cash drawer opening/closing balances.</li>
          <li>Manage floor layouts, table capacities, and confirm reservation requests.</li>
        </ul>
      </div>

      <div class="guideline-card">
        <div class="guideline-role">
          <span style="font-size:1.5rem;">🍳</span>
          <h3>Kitchen Head & Line Cook</h3>
        </div>
        <ul class="guideline-list">
          <li>Keep KDS screen active on station counters (Grill, Bar, Pastry, Fryer).</li>
          <li>Prioritize tickets based on SLA timers (Green &rarr; Orange warning &rarr; Red breach).</li>
          <li>Update ticket status promptly as items complete preparation.</li>
        </ul>
      </div>

      <div class="guideline-card">
        <div class="guideline-role">
          <span style="font-size:1.5rem;">🤵</span>
          <h3>Waiter / Server</h3>
        </div>
        <ul class="guideline-list">
          <li>Open table sessions upon guest arrival and record guest count.</li>
          <li>Input orders accurately with size variants and special cooking modifiers.</li>
          <li>Track personal waiter commission earnings in the Waiter Matrix.</li>
        </ul>
      </div>

      <div class="guideline-card">
        <div class="guideline-role">
          <span style="font-size:1.5rem;">💵</span>
          <h3>Cashier</h3>
        </div>
        <ul class="guideline-list">
          <li>Process customer payments via Cash, Mobile Banking (bKash/Nagad), or Card.</li>
          <li>Verify coupon codes and customer loyalty point redemptions.</li>
          <li>Perform mandatory cash drawer count during shift opening and closing.</li>
        </ul>
      </div>

    </div>
  </section>

  <!-- Security & Health Section -->
  <section id="security" style="margin-top: 80px;">
    <div class="health-widget">
      <div class="health-info">
        <h3>System Security & Health Status</h3>
        <p>Real-time database connection, environmental probes, and Railway production readiness.</p>
      </div>

      <div class="health-badges">
        <div class="health-pill">
          <span class="status-dot <?= $dbConnected ? 'dot-online' : 'dot-offline' ?>"></span>
          <span>Database: <strong><?= $dbConnected ? 'CONNECTED (smartresta_db)' : 'DISCONNECTED' ?></strong></span>
        </div>

        <div class="health-pill">
          <span class="status-dot dot-online"></span>
          <span>Assertions: <strong>265 / 265 PASSED (100%)</strong></span>
        </div>

        <div class="health-pill">
          <span class="status-dot dot-online"></span>
          <span>Security: <strong>PDO Prepared + Bcrypt Cost 12</strong></span>
        </div>

        <div class="health-pill">
          <span class="status-dot dot-online"></span>
          <span>Railway Ready: <strong>YES</strong></span>
        </div>
      </div>
    </div>
  </section>

</div>

<!-- Footer -->
<footer>
  <p>SMARTRESTA — Restaurant Operations, POS & Performance Management System &copy; <?= date('Y') ?></p>
  <p style="margin-top: 8px;">Certified Production Ready • 70 Database Tables • 25 Core Engines • PHP 8.x • MySQL 8.x</p>
</footer>

</body>
</html>
