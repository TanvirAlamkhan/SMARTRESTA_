<!-- VIEW 3: POS & WAITER ORDERING -->
<section id="pos-view" class="role-view" style="display:none;">
  <div class="pos-layout">
    <div>
      <!-- POS Table Selector -->
      <div style="margin-bottom:10px; display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
        <label style="font-weight:600; font-size:0.875rem;">Select Table:</label>
        <select id="pos-table-selector" class="form-control" style="max-width:200px;" onchange="onPOSTableSelected()">
          <option value="">— Pick a table —</option>
        </select>
        <button class="btn btn-secondary btn-sm" onclick="SmartPOS.selectTakeaway()">🛍️ Takeaway / Walk-in</button>
        <span id="pos-table-heading" class="badge badge-info">No Table Selected</span>
      </div>

      <!-- Dynamic Category Pills -->
      <div class="category-slider" id="pos-category-pills">
        <button class="category-pill active" data-cat="" onclick="filterPOSCategory('', this)">All Items</button>
      </div>

      <!-- Dynamic Product Grid (loaded from DB) -->
      <div class="product-grid" id="pos-product-grid">
        <div style="grid-column:1/-1; text-align:center; padding:40px; color:var(--text-muted);">
          <div style="font-size:2rem; margin-bottom:8px;">🍽️</div>
          <p>Loading menu items...</p>
        </div>
      </div>
    </div>

    <div class="pos-cart">
      <div class="cart-header">
        <h3 id="pos-table-heading-cart">Order</h3>
        <span class="badge badge-info">Waiter Ordering</span>
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
