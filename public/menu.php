<?php
/**
 * SMARTRESTA — Public Online Restaurant Menu & Customer Ordering Portal
 * Allows guests to browse dishes, select size variants & modifiers,
 * add items to cart, and submit real orders directly to the restaurant POS & KDS.
 */

require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/CSRF.php';

CSRF::init();
$csrfToken = CSRF::getToken();
$isLoggedIn = Auth::check();
$currentUser = Auth::user();
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
  <title>Online Menu & Ordering — SMARTRESTA</title>

  <!-- Google Fonts: Poppins -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <!-- Modular CSS -->
  <link rel="stylesheet" href="../assets/css/variables.css">
  <link rel="stylesheet" href="../assets/css/reset.css">
  <link rel="stylesheet" href="../assets/css/typography.css">
  <link rel="stylesheet" href="../assets/css/components.css">
  <link rel="stylesheet" href="../assets/css/forms.css">
  <link rel="stylesheet" href="../assets/css/pos.css">
  <link rel="stylesheet" href="../assets/css/dark-mode.css">

  <style>
    body {
      background-color: var(--bg-primary);
      color: var(--text-primary);
      font-family: 'Poppins', sans-serif;
      margin: 0;
      padding: 0;
    }
    .pub-header {
      background-color: #0F172A;
      color: #fff;
      padding: 16px 32px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      position: sticky;
      top: 0;
      z-index: 100;
      border-bottom: 1px solid #1E293B;
    }
    .pub-brand {
      display: flex;
      align-items: center;
      gap: 10px;
      text-decoration: none;
      color: #fff;
      font-size: 1.3rem;
      font-weight: 700;
    }
    .pub-brand span.badge {
      background: var(--accent);
      font-size: 0.75rem;
      padding: 2px 8px;
      border-radius: 4px;
    }
    .pub-nav {
      display: flex;
      align-items: center;
      gap: 16px;
    }
    .pub-cart-btn {
      position: relative;
      background: var(--accent);
      color: #fff;
      border: none;
      padding: 8px 18px;
      border-radius: 20px;
      font-weight: 600;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .pub-cart-count {
      background: #EF4444;
      color: #fff;
      font-size: 0.75rem;
      padding: 2px 6px;
      border-radius: 10px;
    }
    .hero-banner {
      background: linear-gradient(135deg, #1E1B4B 0%, #0F172A 100%);
      color: #fff;
      padding: 40px 32px;
      text-align: center;
    }
    .hero-banner h1 {
      font-size: 2.2rem;
      margin-bottom: 8px;
    }
    .hero-banner p {
      color: #94A3B8;
      font-size: 1rem;
      max-width: 600px;
      margin: 0 auto;
    }
    .main-container {
      max-width: 1280px;
      margin: 0 auto;
      padding: 32px 24px;
      display: grid;
      grid-template-columns: 1fr 340px;
      gap: 32px;
    }
    @media (max-width: 900px) {
      .main-container { grid-template-columns: 1fr; }
    }
    .menu-section {
      display: flex;
      flex-direction: column;
      gap: 24px;
    }
    .search-cats-bar {
      display: flex;
      flex-direction: column;
      gap: 16px;
    }
    .category-pills {
      display: flex;
      gap: 10px;
      overflow-x: auto;
      padding-bottom: 8px;
    }
    .cat-pill {
      background: var(--surface);
      border: 1px solid var(--border);
      color: var(--text-primary);
      padding: 8px 18px;
      border-radius: 20px;
      font-size: 0.9rem;
      font-weight: 500;
      cursor: pointer;
      white-space: nowrap;
      transition: all 0.2s;
    }
    .cat-pill.active, .cat-pill:hover {
      background: var(--accent);
      color: #fff;
      border-color: var(--accent);
    }
    .product-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
      gap: 20px;
    }
    .prod-card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius-lg);
      overflow: hidden;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      transition: transform 0.2s, box-shadow 0.2s;
    }
    .prod-card:hover {
      transform: translateY(-4px);
      box-shadow: var(--shadow-md);
    }
    .prod-img {
      width: 100%;
      height: 140px;
      object-fit: cover;
      background: #1E293B;
    }
    .prod-details {
      padding: 16px;
      display: flex;
      flex-direction: column;
      gap: 8px;
      flex-grow: 1;
    }
    .prod-title {
      font-weight: 600;
      font-size: 1rem;
    }
    .prod-desc {
      font-size: 0.8rem;
      color: var(--text-muted);
      line-height: 1.4;
    }
    .prod-bottom {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-top: auto;
      padding-top: 12px;
    }
    .prod-price {
      font-weight: 700;
      color: var(--accent);
      font-size: 1.1rem;
    }
    .cart-drawer {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius-lg);
      padding: 24px;
      position: sticky;
      top: 90px;
      height: fit-content;
      display: flex;
      flex-direction: column;
      gap: 20px;
      box-shadow: var(--shadow-md);
    }
    .cart-title {
      font-size: 1.2rem;
      font-weight: 700;
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-bottom: 1px solid var(--border);
      padding-bottom: 12px;
      margin: 0;
    }
    .cart-items-list {
      display: flex;
      flex-direction: column;
      gap: 12px;
      max-height: 320px;
      overflow-y: auto;
    }
    .cart-item-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding-bottom: 8px;
      border-bottom: 1px dashed var(--border);
    }
    .cart-item-info {
      display: flex;
      flex-direction: column;
    }
    .cart-item-name { font-size: 0.9rem; font-weight: 600; }
    .cart-item-sub { font-size: 0.8rem; color: var(--text-muted); }
    .cart-qty-btn {
      width: 26px; height: 26px; border-radius: 50%; border: 1px solid var(--border);
      background: var(--bg-secondary); cursor: pointer; font-weight: 700;
    }
    .cart-summary {
      display: flex;
      flex-direction: column;
      gap: 6px;
      font-size: 0.9rem;
      border-top: 1px solid var(--border);
      padding-top: 12px;
    }
    .cart-summary-row {
      display: flex; justify-content: space-between;
    }
    .cart-summary-total {
      font-weight: 700; font-size: 1.15rem; color: var(--accent);
      border-top: 1px dashed var(--border); padding-top: 8px; margin-top: 4px;
    }
  </style>
</head>
<body>

<!-- Header -->
<header class="pub-header">
  <a href="../landing.php" class="pub-brand">
    <span>SMARTRESTA</span>
    <span class="badge">MENU</span>
  </a>

  <div class="pub-nav">
    <a href="../landing.php" style="color:#94A3B8; text-decoration:none; font-size:0.9rem;">Home</a>
    <a href="#reservations" onclick="document.getElementById('modal-public-reservation').classList.add('active')" style="color:#94A3B8; text-decoration:none; font-size:0.9rem;">Reserve Table</a>
    <?php if ($isLoggedIn): ?>
      <a href="../admin/" class="btn btn-secondary btn-sm">My Portal (<?= htmlspecialchars($currentUser['name']) ?>)</a>
    <?php else: ?>
      <a href="login.php" class="btn btn-primary btn-sm">Staff Login</a>
    <?php endif; ?>
  </div>
</header>

<!-- Hero Banner -->
<section class="hero-banner">
  <h1>Gourmet Dining & Online Ordering</h1>
  <p>Select your favorite dishes, customize variants and extra modifiers, and place orders directly to our kitchen.</p>
</section>

<!-- Main Canvas -->
<div class="main-container">
  
  <!-- Menu Products Section -->
  <div class="menu-section">
    <div class="search-cats-bar">
      <div style="display:flex; gap:12px;">
        <input type="text" id="pub-search-input" class="form-control" placeholder="🔍 Search menu items..." oninput="filterPublicMenu()">
        <select id="pub-table-selector" class="form-control" style="max-width:200px;">
          <option value="">Order Type: Takeaway / Online</option>
          <option value="1">Table 01 (Dine-In)</option>
          <option value="2">Table 02 (Dine-In)</option>
          <option value="3">Table 03 (Dine-In)</option>
          <option value="4">Table 04 (Dine-In)</option>
        </select>
      </div>

      <div class="category-pills" id="pub-cat-pills">
        <button class="cat-pill active" onclick="filterPublicCategory('', this)">All Items</button>
      </div>
    </div>

    <!-- Dish Grid -->
    <div class="product-grid" id="pub-product-grid">
      <div style="grid-column: 1/-1; text-align:center; padding: 40px; color: var(--text-muted);">
        Loading restaurant dishes from database...
      </div>
    </div>
  </div>

  <!-- Cart Drawer -->
  <div class="cart-drawer">
    <h3 class="cart-title">
      <span>🛒 My Order Cart</span>
      <span class="pub-cart-count" id="pub-cart-count">0</span>
    </h3>

    <div class="cart-items-list" id="pub-cart-items">
      <div style="text-align:center; padding: 30px; color: var(--text-muted); font-size: 0.85rem;">
        Your cart is empty. Click "+ Add to Cart" on any dish!
      </div>
    </div>

    <div class="cart-summary">
      <div class="cart-summary-row"><span>Subtotal</span><span id="pub-cart-subtotal">৳0.00</span></div>
      <div id="pub-cart-discount-row" class="cart-summary-row" style="display:none; color:#10B981; font-weight:600;"><span>Coupon Discount</span><span id="pub-cart-discount">-৳0.00</span></div>
      <div class="cart-summary-row"><span>VAT (5%)</span><span id="pub-cart-tax">৳0.00</span></div>
      <div class="cart-summary-row cart-summary-total"><span>Grand Total</span><span id="pub-cart-total">৳0.00</span></div>
    </div>

    <!-- Promotional Coupon Section -->
    <div style="background:var(--bg-secondary); border:1px solid var(--border); border-radius:var(--radius-md); padding:10px;">
      <label style="font-size:0.75rem; font-weight:700; color:var(--accent); display:block; margin-bottom:4px;">🎟️ Coupon Code</label>
      <div style="display:flex; gap:6px;">
        <input type="text" id="pub-coupon-input" class="form-control" placeholder="Code (e.g. SAVE100)" style="font-weight:600; text-transform:uppercase;">
        <button class="btn btn-secondary btn-sm" onclick="applyPublicCoupon()">Apply</button>
      </div>
      <div id="pub-coupon-status" style="display:none; font-size:0.8rem; color:#10B981; font-weight:600; margin-top:6px;"></div>
    </div>

    <!-- Customer Details Form -->
    <div style="display:flex; flex-direction:column; gap:10px;">
      <input type="text" id="pub-cust-name" class="form-control" placeholder="Customer Name *" required>
      <input type="tel" id="pub-cust-phone" class="form-control" placeholder="Phone Number *" required>
      <input type="text" id="pub-cust-notes" class="form-control" placeholder="Special Instructions / Allergies">
    </div>

    <button class="btn btn-primary btn-lg" style="width:100%; border-radius:10px;" onclick="submitPublicOrder()">
      🚀 Place Order Now
    </button>
  </div>
</div>

<!-- Modal: Public Table Reservation -->
<div id="modal-public-reservation" class="modal-backdrop">
  <div class="modal-content" style="max-width:500px;">
    <div class="modal-header">
      <h3>Book a Table Reservation</h3>
      <button class="btn btn-secondary btn-sm" onclick="document.getElementById('modal-public-reservation').classList.remove('active')">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label class="form-label">Full Name *</label>
        <input type="text" id="pub-res-name" class="form-control" placeholder="e.g. Tanvir Ahmed">
      </div>
      <div class="form-group">
        <label class="form-label">Phone Number *</label>
        <input type="tel" id="pub-res-phone" class="form-control" placeholder="01700000000">
      </div>
      <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
        <div class="form-group">
          <label class="form-label">Reservation Date *</label>
          <input type="date" id="pub-res-date" class="form-control">
        </div>
        <div class="form-group">
          <label class="form-label">Reservation Time *</label>
          <input type="time" id="pub-res-time" class="form-control" value="19:00">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Guest Count *</label>
        <input type="number" id="pub-res-guests" class="form-control" value="2" min="1" max="20">
      </div>
      <div class="form-group">
        <label class="form-label">Special Requests</label>
        <input type="text" id="pub-res-notes" class="form-control" placeholder="Window table, High chair...">
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="document.getElementById('modal-public-reservation').classList.remove('active')">Cancel</button>
      <button class="btn btn-primary" onclick="submitPublicReservation()">Confirm Reservation</button>
    </div>
  </div>
</div>

<script src="../assets/js/ajax.js"></script>
<script src="../assets/js/notifications.js"></script>
<script>
let pubProducts = [];
let pubCart = [];
let pubSelectedCat = '';

document.addEventListener('DOMContentLoaded', async () => {
  await loadPublicCatalog();
});

async function loadPublicCatalog() {
  try {
    const [pRes, cRes] = await Promise.all([
      SmartAPI.get('../api/v1/products/index.php?is_available=1'),
      SmartAPI.get('../api/v1/categories/index.php')
    ]);

    pubProducts = pRes.data || [];
    const cats = cRes.data || [];

    const catPills = document.getElementById('pub-cat-pills');
    catPills.innerHTML = `<button class="cat-pill active" onclick="filterPublicCategory('', this)">All Items</button>`
      + cats.map(c => `<button class="cat-pill" onclick="filterPublicCategory('${c.id}', this)">${c.name}</button>`).join('');

    renderPublicGrid(pubProducts);
  } catch (err) {
    document.getElementById('pub-product-grid').innerHTML = `<div style="grid-column:1/-1; text-align:center; color:var(--danger); padding:40px;">Failed to load dishes from server.</div>`;
  }
}

function filterPublicCategory(catId, btn) {
  pubSelectedCat = catId;
  document.querySelectorAll('.cat-pill').forEach(b => b.classList.remove('active'));
  if (btn) btn.classList.add('active');
  filterPublicMenu();
}

function filterPublicMenu() {
  const q = (document.getElementById('pub-search-input').value || '').toLowerCase().trim();
  let list = pubProducts;
  if (pubSelectedCat) {
    list = list.filter(p => String(p.category_id) === String(pubSelectedCat));
  }
  if (q) {
    list = list.filter(p => p.name.toLowerCase().includes(q) || (p.short_description || '').toLowerCase().includes(q));
  }
  renderPublicGrid(list);
}

function renderPublicGrid(items) {
  const grid = document.getElementById('pub-product-grid');
  if (!items || items.length === 0) {
    grid.innerHTML = `<div style="grid-column:1/-1; text-align:center; padding:40px; color:var(--text-muted);"><div style="font-size:2.5rem;">🍽️</div><p>No dishes found.</p></div>`;
    return;
  }

  grid.innerHTML = items.map(p => {
    const thumb = p.image_url || '';
    const imgHtml = thumb ? `<img src="${thumb}" alt="${p.name}" class="prod-img" onerror="this.style.display='none'">` : `<div class="prod-img" style="display:flex; align-items:center; justify-content:center; font-size:3rem; color:#64748B;">🍔</div>`;
    return `
      <div class="prod-card">
        ${imgHtml}
        <div class="prod-details">
          <div class="prod-title">${p.name}</div>
          <div class="prod-desc">${p.short_description || 'Fresh gourmet restaurant dish prepared to order.'}</div>
          <div class="prod-bottom">
            <span class="prod-price">৳${parseFloat(p.price).toFixed(2)}</span>
            <button class="btn btn-primary btn-sm" onclick="addToPublicCart(${p.id}, '${(p.name||'').replace(/'/g, "&apos;")}', ${p.price})">+ Add</button>
          </div>
        </div>
      </div>
    `;
  }).join('');
}

function addToPublicCart(id, name, price) {
  const existing = pubCart.find(i => i.id === id);
  if (existing) {
    existing.qty += 1;
  } else {
    pubCart.push({ id, name, price: parseFloat(price), qty: 1 });
  }
  renderPublicCart();
  if (window.SmartNotifications) {
    SmartNotifications.show(`Added "${name}" to cart`, 'success', 1500);
  }
}

function updatePublicQty(id, delta) {
  const item = pubCart.find(i => i.id === id);
  if (item) {
    item.qty += delta;
    if (item.qty <= 0) {
      pubCart = pubCart.filter(i => i.id !== id);
    }
  }
  renderPublicCart();
}

let pubAppliedCoupon = null;

async function applyPublicCoupon() {
  const code = (document.getElementById('pub-coupon-input').value || '').trim();
  if (!code) {
    if (window.SmartNotifications) SmartNotifications.show('Please enter a coupon code first', 'warning');
    return;
  }
  const subtotal = pubCart.reduce((sum, i) => sum + (i.price * i.qty), 0);
  try {
    const res = await SmartAPI.post('../api/v1/crm/coupons.php', { action: 'validate', code: code, order_amount: subtotal });
    if (res.success && res.data) {
      pubAppliedCoupon = res.data;
      const statusEl = document.getElementById('pub-coupon-status');
      if (statusEl) {
        statusEl.style.display = 'block';
        statusEl.innerHTML = `🎟️ Coupon <strong>${res.data.code}</strong> Applied (-৳${parseFloat(res.data.discount_amount).toFixed(2)}) ✓`;
      }
      renderPublicCart();
      if (window.SmartNotifications) SmartNotifications.show(`Coupon ${res.data.code} applied successfully!`, 'success');
    } else {
      alert("Coupon Error: " + (res.message || 'Invalid coupon code'));
    }
  } catch (err) {
    alert("Coupon Error: " + err.message);
  }
}

function renderPublicCart() {
  const container = document.getElementById('pub-cart-items');
  const countBadge = document.getElementById('pub-cart-count');
  const totalCount = pubCart.reduce((sum, i) => sum + i.qty, 0);
  countBadge.textContent = totalCount;

  if (pubCart.length === 0) {
    container.innerHTML = `<div style="text-align:center; padding: 30px; color: var(--text-muted); font-size: 0.85rem;">Your cart is empty. Click "+ Add to Cart" on any dish!</div>`;
  } else {
    container.innerHTML = pubCart.map(item => `
      <div class="cart-item-row">
        <div class="cart-item-info">
          <span class="cart-item-name">${item.name}</span>
          <span class="cart-item-sub">৳${item.price.toFixed(2)} × ${item.qty}</span>
        </div>
        <div style="display:flex; align-items:center; gap:6px;">
          <button class="cart-qty-btn" onclick="updatePublicQty(${item.id}, -1)">-</button>
          <span style="font-weight:600; font-size:0.85rem;">${item.qty}</span>
          <button class="cart-qty-btn" onclick="updatePublicQty(${item.id}, 1)">+</button>
        </div>
      </div>
    `).join('');
  }

  const subtotal = pubCart.reduce((sum, i) => sum + (i.price * i.qty), 0);
  let discount = 0;
  if (pubAppliedCoupon) {
    discount = parseFloat(pubAppliedCoupon.discount_amount || 0);
  }

  const taxable = Math.max(0, subtotal - discount);
  const tax = Math.round((taxable * 0.05 + Number.EPSILON) * 100) / 100;
  const total = taxable + tax;

  document.getElementById('pub-cart-subtotal').textContent = `৳${subtotal.toFixed(2)}`;
  const discRow = document.getElementById('pub-cart-discount-row');
  if (discRow) {
    discRow.style.display = discount > 0 ? 'flex' : 'none';
    document.getElementById('pub-cart-discount').textContent = `-৳${discount.toFixed(2)}`;
  }
  document.getElementById('pub-cart-tax').textContent = `৳${tax.toFixed(2)}`;
  document.getElementById('pub-cart-total').textContent = `৳${total.toFixed(2)}`;
}

async function submitPublicOrder() {
  if (pubCart.length === 0) {
    SmartNotifications.show('Please add dishes to your cart first', 'warning');
    return;
  }
  const name = document.getElementById('pub-cust-name').value.trim();
  const phone = document.getElementById('pub-cust-phone').value.trim();
  const notes = document.getElementById('pub-cust-notes').value.trim();
  const tableId = document.getElementById('pub-table-selector').value || null;

  if (!name || !phone) {
    SmartNotifications.show('Please enter customer name and phone number', 'warning');
    return;
  }

  try {
    const draftRes = await SmartAPI.post('../api/v1/orders/index.php', {
      order_type: tableId ? 'DINE_IN' : 'TAKEAWAY',
      table_id: tableId,
      customer_name: name,
      customer_phone: phone,
      notes: notes
    });

    if (!draftRes.success || !draftRes.data || !draftRes.data.order_id) {
      throw new Error(draftRes.message || 'Order creation failed');
    }

    const orderId = draftRes.data.order_id;
    for (const item of pubCart) {
      await SmartAPI.post('../api/v1/orders/items.php', {
        order_id: orderId,
        product_id: item.id,
        quantity: item.qty
      });
    }

    if (pubAppliedCoupon) {
      await SmartAPI.post('../api/v1/orders/coupon.php', {
        action: 'apply',
        order_id: orderId,
        coupon_code: pubAppliedCoupon.code
      });
    }

    const submitRes = await SmartAPI.post('../api/v1/orders/submit.php', { order_id: orderId });
    if (submitRes.success) {
      const orderNum = submitRes.data ? submitRes.data.order_number : orderId;
      alert(`🎉 Thank you ${name}! Your order #${orderNum} has been received and sent to the kitchen.`);
      pubCart = [];
      pubAppliedCoupon = null;
      renderPublicCart();
      document.getElementById('pub-cust-name').value = '';
      document.getElementById('pub-cust-phone').value = '';
      document.getElementById('pub-cust-notes').value = '';
      if (document.getElementById('pub-coupon-input')) document.getElementById('pub-coupon-input').value = '';
      if (document.getElementById('pub-coupon-status')) document.getElementById('pub-coupon-status').style.display = 'none';
    }
      document.getElementById('pub-cust-notes').value = '';
    }
  } catch (err) {
    SmartNotifications.show(err.message || 'Order placement failed. Please try again.', 'danger');
  }
}

async function submitPublicReservation() {
  const name = document.getElementById('pub-res-name').value.trim();
  const phone = document.getElementById('pub-res-phone').value.trim();
  const date = document.getElementById('pub-res-date').value;
  const time = document.getElementById('pub-res-time').value;
  const guests = document.getElementById('pub-res-guests').value;
  const notes = document.getElementById('pub-res-notes').value.trim();

  if (!name || !phone || !date || !time) {
    SmartNotifications.show('Please fill in name, phone, date, and time', 'warning');
    return;
  }

  try {
    const res = await SmartAPI.post('../api/v1/crm/reservations.php', {
      customer_name: name,
      customer_phone: phone,
      reservation_date: date,
      reservation_time: time,
      guest_count: guests,
      notes: notes
    });

    if (res.success) {
      alert(`📅 Table Reservation Confirmed for ${name} on ${date} at ${time}!`);
      document.getElementById('modal-public-reservation').classList.remove('active');
    } else {
      SmartNotifications.show(res.message || 'Reservation failed', 'danger');
    }
  } catch (err) {
    SmartNotifications.show(err.message || 'Reservation failed', 'danger');
  }
}
</script>
</body>
</html>
