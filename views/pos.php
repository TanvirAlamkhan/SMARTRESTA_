<!-- VIEW 3: 🛒 POS ORDER PLACE -->
<section id="pos" class="role-view" style="display:none;">
  
  <!-- Header Bar -->
  <div class="card" style="margin-bottom:16px; padding:16px 20px;">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
      <div>
        <h2 style="margin:0; font-size:1.4rem; font-weight:700; display:flex; align-items:center; gap:8px;">
          <span>🛒</span>
          <span>POS ORDER PLACE</span>
        </h2>
        <span class="text-sm" style="color:var(--text-muted);">Select Table & Floor &rarr; Browse Categorized Dishes &rarr; Add Cooking Instructions &rarr; Apply Coupon &rarr; Submit Order to Kitchen</span>
      </div>

      <!-- Table & Floor Operations Bar -->
      <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
        <div>
          <label style="font-size:0.75rem; font-weight:600; display:block; color:var(--text-muted); margin-bottom:2px;">FLOOR ZONE</label>
          <select id="pos-floor-selector" class="form-control" style="max-width:150px; font-size:0.85rem;" onchange="SmartPOS.onFloorChanged()">
            <option value="">All Floors</option>
          </select>
        </div>

        <div>
          <label style="font-size:0.75rem; font-weight:600; display:block; color:var(--text-muted); margin-bottom:2px;">DINING TABLE *</label>
          <select id="pos-table-selector" class="form-control" style="max-width:180px; font-weight:600; font-size:0.85rem;" onchange="SmartPOS.onTableSelected()">
            <option value="">— Select Table —</option>
          </select>
        </div>

        <div style="margin-top:14px;">
          <button class="btn btn-secondary btn-sm" onclick="SmartPOS.selectTakeaway()">🛍️ Takeaway / Walk-in</button>
        </div>

        <div style="margin-top:14px;">
          <span id="pos-table-heading" class="badge badge-warning" style="font-size:0.85rem; padding:6px 12px;">No Table Selected</span>
        </div>
      </div>
    </div>
  </div>

  <div class="pos-layout">
    <!-- LEFT PANEL: CATEGORIES, SEARCH & PRODUCT GRID -->
    <div>
      
      <!-- Search & Category Filters Bar -->
      <div class="card" style="margin-bottom:16px; padding:14px;">
        <div style="display:flex; gap:12px; margin-bottom:12px;">
          <input type="text" id="pos-search-input" class="form-control" placeholder="🔍 Search dishes by name, description, SKU..." oninput="SmartPOS.filterMenu()">
          <button class="btn btn-secondary btn-sm" onclick="SmartPOS.loadProductCatalog()">🔄 Refresh Catalog</button>
        </div>

        <!-- Dynamic Category Pills -->
        <div class="category-slider" id="pos-category-pills">
          <button class="category-pill active" data-cat="" onclick="SmartPOS.filterCategory('', this)">All Items</button>
          <button class="category-pill" data-cat="main" onclick="SmartPOS.filterCategory('main', this)">🍽 Main Dishes</button>
          <button class="category-pill" data-cat="side" onclick="SmartPOS.filterCategory('side', this)">🍟 Side Dishes</button>
          <button class="category-pill" data-cat="dessert" onclick="SmartPOS.filterCategory('dessert', this)">🍰 Desserts</button>
          <button class="category-pill" data-cat="beverage" onclick="SmartPOS.filterCategory('beverage', this)">🥤 Beverages</button>
        </div>
      </div>

      <!-- Dynamic Product Grid (loaded from DB) -->
      <div class="product-grid" id="pos-product-grid">
        <div style="grid-column:1/-1; text-align:center; padding:50px; color:var(--text-muted);">
          <div style="font-size:2.5rem; margin-bottom:8px;">🍽️</div>
          <p>Loading database dishes...</p>
        </div>
      </div>
    </div>

    <!-- RIGHT PANEL: ORDER CHECKLIST / CART DRAWER -->
    <div class="pos-cart">
      <div class="cart-header" style="display:flex; justify-content:space-between; align-items:center;">
        <div>
          <h3 id="pos-table-heading-cart" style="margin:0;">🛒 ORDER CHECKLIST</h3>
          <span id="pos-session-badge" class="text-sm" style="color:var(--text-muted);">Waiter POS Session</span>
        </div>
        <button class="btn btn-secondary btn-sm" onclick="SmartPOS.clearCart()">Clear Checklist</button>
      </div>

      <!-- Cart Line Items Container -->
      <div id="pos-cart-items" class="cart-items-list" style="min-height:220px; max-height:360px; overflow-y:auto; padding:12px 0;">
        <div style="text-align:center; padding: 40px var(--space-4); color: var(--text-muted);">
          <div style="font-size: 2.2rem; margin-bottom: 8px;">🛒</div>
          <p style="font-size:0.875rem;">No items in order checklist.</p>
          <span class="text-xs" style="color:var(--text-muted);">Click "+ Add to Checklist" on any dish to build order.</span>
        </div>
      </div>

      <!-- Coupon & Promotional Section -->
      <div style="background:var(--bg-secondary); border:1px solid var(--border); border-radius:var(--radius-md); padding:12px; margin-top:12px;">
        <label style="font-size:0.75rem; font-weight:700; text-transform:uppercase; color:var(--accent); display:block; margin-bottom:6px;">
          🎟️ Promotional Coupon Code
        </label>
        
        <div style="display:flex; gap:8px;">
          <input type="text" id="pos-coupon-input" class="form-control" style="font-weight:600; text-transform:uppercase;" placeholder="e.g. SAVE100, SAVE20" onkeypress="if(event.key==='Enter') SmartPOS.applyCoupon()">
          <button id="pos-btn-apply-coupon" class="btn btn-secondary btn-sm" onclick="SmartPOS.applyCoupon()">Apply</button>
          <button id="pos-btn-clear-coupon" class="btn btn-outline-danger btn-sm" style="display:none;" onclick="SmartPOS.clearCoupon()">Clear</button>
        </div>

        <div id="pos-coupon-status-badge" style="display:none; margin-top:8px; font-size:0.8rem; font-weight:600; padding:6px 10px; border-radius:6px; background:rgba(16, 185, 129, 0.15); color:#10B981; border:1px solid rgba(16, 185, 129, 0.3);">
          <!-- Populated dynamically via JS -->
        </div>
      </div>

      <!-- Cart Financial Footer -->
      <div class="cart-footer" style="margin-top:12px;">
        <div class="cart-summary-row">
          <span>Items Subtotal</span>
          <span id="cart-subtotal" style="font-weight:600;">৳0.00</span>
        </div>

        <div id="cart-discount-row" class="cart-summary-row" style="display:none; color:#10B981;">
          <span>Coupon Discount</span>
          <span id="cart-discount" style="font-weight:700;">-৳0.00</span>
        </div>

        <div class="cart-summary-row">
          <span>VAT (5%)</span>
          <span id="cart-tax">৳0.00</span>
        </div>

        <div class="cart-summary-row total" style="border-top:1px dashed var(--border); padding-top:8px; margin-top:4px;">
          <span>Grand Total</span>
          <span id="cart-total" class="price-tag" style="font-size:1.3rem;">৳0.00</span>
        </div>

        <div style="margin-top: 10px;">
          <span class="text-uppercase-label" style="font-size:0.75rem;">Payment Preference (Settled at Reception)</span>
          <div class="payment-methods-grid" style="margin-top:4px;">
            <div class="payment-method-btn active" data-method="Cash" onclick="SmartPOS.setPaymentMethod('Cash')">Cash</div>
            <div class="payment-method-btn" data-method="bKash" onclick="SmartPOS.setPaymentMethod('bKash')">bKash</div>
            <div class="payment-method-btn" data-method="Nagad" onclick="SmartPOS.setPaymentMethod('Nagad')">Nagad</div>
            <div class="payment-method-btn" data-method="Card" onclick="SmartPOS.setPaymentMethod('Card')">Card</div>
          </div>
        </div>

        <button id="pos-submit-order-btn" class="btn btn-primary btn-lg" style="width:100%; margin-top:12px; padding:14px; font-weight:700; font-size:1rem; border-radius:10px;" onclick="SmartPOS.submitOrder()">
          🚀 PLACE ORDER & ROUTE TO KITCHEN
        </button>
      </div>
    </div>
  </div>
</section>

<!-- MODAL: ITEM CUSTOMIZATION & SPECIAL COOKING INSTRUCTION -->
<div id="modal-pos-item-customize" class="modal-backdrop">
  <div class="modal-content" style="max-width:480px;">
    <div class="modal-header">
      <h3 id="pos-modal-dish-name">Add Dish to Checklist</h3>
      <button class="btn btn-secondary btn-sm" onclick="document.getElementById('modal-pos-item-customize').classList.remove('active')">✕</button>
    </div>
    
    <div class="modal-body">
      <div style="display:flex; justify-content:space-between; align-items:center; background:var(--bg-secondary); padding:12px 16px; border-radius:var(--radius-md); margin-bottom:16px;">
        <span style="font-weight:600;">Base Price:</span>
        <span id="pos-modal-dish-price" style="font-weight:700; font-size:1.1rem; color:var(--accent);">৳0.00</span>
      </div>

      <!-- Quantity Controls -->
      <div class="form-group" style="margin-bottom:16px;">
        <label class="form-label" style="font-weight:600;">Quantity *</label>
        <div style="display:flex; align-items:center; gap:16px;">
          <button class="btn btn-secondary" style="width:40px; height:40px; font-size:1.2rem; font-weight:700;" onclick="SmartPOS.adjustModalQty(-1)">-</button>
          <span id="pos-modal-qty-display" style="font-size:1.3rem; font-weight:700; width:40px; text-align:center;">1</span>
          <button class="btn btn-secondary" style="width:40px; height:40px; font-size:1.2rem; font-weight:700;" onclick="SmartPOS.adjustModalQty(1)">+</button>
        </div>
      </div>

      <!-- Cooking Instructions / Special Requests -->
      <div class="form-group">
        <label class="form-label" style="font-weight:600;">Item Instructions / Special Cooking Request</label>
        <input type="text" id="pos-modal-notes-input" class="form-control" placeholder="e.g. No onion, extra sauce, mild spicy..." maxlength="255">
        <span class="text-xs" style="color:var(--text-muted); margin-top:4px; display:block;">These instructions will print on the Kitchen Display System (KDS) ticket.</span>
      </div>
    </div>

    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="document.getElementById('modal-pos-item-customize').classList.remove('active')">Cancel</button>
      <button id="pos-modal-confirm-btn" class="btn btn-primary" onclick="SmartPOS.confirmAddModalItem()">+ Add to Checklist</button>
    </div>
  </div>
</div>
