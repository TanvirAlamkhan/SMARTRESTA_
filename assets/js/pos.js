/**
 * SMARTRESTA Production POS & Waiter Cart Controller
 * Connects POS UI to real backend database, variants, modifiers, and transactional Order Engine
 */

const SmartPOS = {
  cart: [],
  selectedTableId: null,
  selectedSessionId: null,
  currentOrderId: null,
  paymentMethod: 'Cash',

  setTableAndSession(tableId, tableNum, sessionId) {
    this.selectedTableId = tableId;
    this.selectedSessionId = sessionId;
    const titleEl = document.getElementById('pos-table-heading');
    if (titleEl) {
      titleEl.textContent = `Table ${tableNum} Order ${sessionId ? `(#DS-${sessionId})` : '(No Session)'}`;
    }
  },

  addItem(productId, name, basePrice, stationName = 'Main Kitchen', variantId = null, variantName = null, selectedModifiers = []) {
    const modifierTotal = selectedModifiers.reduce((sum, m) => sum + (parseFloat(m.price) || 0), 0);
    const unitPrice = parseFloat(basePrice) + modifierTotal;

    const cartKey = `${productId}_${variantId || 0}_${selectedModifiers.map(m => m.id).sort().join('-')}`;
    const existing = this.cart.find(item => item.key === cartKey);

    if (existing) {
      existing.qty += 1;
    } else {
      this.cart.push({
        key: cartKey,
        product_id: productId,
        name: name,
        variant_id: variantId,
        variant_name: variantName,
        base_price: parseFloat(basePrice),
        unit_price: unitPrice,
        modifier_total: modifierTotal,
        modifiers: selectedModifiers,
        qty: 1,
        station: stationName
      });
    }

    this.renderCart();
    if (window.SmartNotifications) {
      SmartNotifications.show(`Added ${name} to cart`, 'success', 2000);
    }
  },

  updateQty(cartKey, delta) {
    const item = this.cart.find(i => i.key === cartKey);
    if (item) {
      item.qty += delta;
      if (item.qty <= 0) {
        this.cart = this.cart.filter(i => i.key !== cartKey);
      }
    }
    this.renderCart();
  },

  clearCart() {
    this.cart = [];
    this.currentOrderId = null;
    this.renderCart();
  },

  setPaymentMethod(method) {
    this.paymentMethod = method;
    document.querySelectorAll('.payment-method-btn').forEach(btn => {
      btn.classList.toggle('active', btn.dataset.method === method);
    });
  },

  calculateTotal() {
    const subtotal = this.cart.reduce((sum, item) => sum + (item.unit_price * item.qty), 0);
    const tax = roundMoney(subtotal * 0.05); // 5% VAT
    const total = roundMoney(subtotal + tax);
    return { subtotal, tax, total };
  },

  renderCart() {
    const cartContainer = document.getElementById('pos-cart-items');
    if (!cartContainer) return;

    if (this.cart.length === 0) {
      cartContainer.innerHTML = `
        <div style="text-align:center; padding: 40px var(--space-4); color: var(--text-muted);">
          <div style="font-size: 2rem; margin-bottom: 8px;">🛒</div>
          <p style="font-size:0.875rem;">No items in current order</p>
        </div>
      `;
    } else {
      cartContainer.innerHTML = this.cart.map(item => `
        <div class="cart-item">
          <div class="cart-item-info">
            <span class="cart-item-name">${item.name} ${item.variant_name ? `<small>(${item.variant_name})</small>` : ''}</span>
            <span class="cart-item-meta">
              ৳${item.unit_price.toFixed(2)} × ${item.qty} | <span class="station-badge ${item.station.toLowerCase().replace(/\s+/g, '-')}">${item.station}</span>
            </span>
            ${item.modifiers.length > 0 ? `
              <div style="font-size:0.75rem; color:var(--accent-dark); margin-top:2px;">
                + ${item.modifiers.map(m => `${m.name} (+৳${m.price})`).join(', ')}
              </div>
            ` : ''}
          </div>
          <div class="cart-qty-controls">
            <button class="cart-qty-btn" onclick="SmartPOS.updateQty('${item.key}', -1)">-</button>
            <span style="font-weight:600; font-size:0.875rem;">${item.qty}</span>
            <button class="cart-qty-btn" onclick="SmartPOS.updateQty('${item.key}', 1)">+</button>
          </div>
        </div>
      `).join('');
    }

    const { subtotal, tax, total } = this.calculateTotal();
    const subtotalEl = document.getElementById('cart-subtotal');
    const taxEl = document.getElementById('cart-tax');
    const totalEl = document.getElementById('cart-total');

    if (subtotalEl) subtotalEl.textContent = `৳${subtotal.toFixed(2)}`;
    if (taxEl) taxEl.textContent = `৳${tax.toFixed(2)}`;
    if (totalEl) totalEl.textContent = `৳${total.toFixed(2)}`;
  },

  async submitOrder() {
    if (this.cart.length === 0) {
      SmartNotifications.show('Please add items to cart before submitting', 'warning');
      return;
    }

    try {
      // 1. Create Draft Order
      const draftRes = await SmartAPI.post('api/v1/orders/index.php', {
        order_type: 'DINE_IN',
        table_id: this.selectedTableId,
        dining_session_id: this.selectedSessionId
      });

      if (!draftRes.success || !draftRes.data.order_id) {
        throw new Error(draftRes.message || 'Failed to initialize draft order');
      }

      const orderId = draftRes.data.order_id;

      // 2. Add Cart Line Items & Modifiers
      for (const item of this.cart) {
        await SmartAPI.post('api/v1/orders/items.php', {
          order_id: orderId,
          product_id: item.product_id,
          variant_id: item.variant_id,
          modifier_ids: item.modifiers.map(m => m.id),
          quantity: item.qty
        });
      }

      // 3. Submit Order
      const submitRes = await SmartAPI.post('api/v1/orders/submit.php', { order_id: orderId });

      if (submitRes.success) {
        SmartNotifications.show(`Order #${submitRes.data.order_number} successfully placed & routed!`, 'success');
        const chosenMethod = this.paymentMethod;
        this.clearCart();
        if (window.loadActiveOrders) loadActiveOrders();
        if (window.loadFloorTables) loadFloorTables();

        // If bKash or billing checkout selected, open Settlement Modal with bKash QR
        if (chosenMethod === 'bKash' && window.SmartBilling) {
          setTimeout(() => {
            SmartBilling.openBillingModal(orderId);
          }, 300);
        }
      }

    } catch (err) {
      SmartNotifications.show(err.message || 'Failed to submit order to database', 'danger');
    }
  }
};

function roundMoney(val) {
  return Math.round((val + Number.EPSILON) * 100) / 100;
}

window.SmartPOS = SmartPOS;
