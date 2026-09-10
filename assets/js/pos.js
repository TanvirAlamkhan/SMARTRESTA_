/**
 * SMARTRESTA Production POS ORDER PLACE Controller
 * Real MySQL 8.x PDO backend integration, table floor selector, dish category filters,
 * line-item cooking instructions, coupon validation engine, and multi-station ticket routing.
 */

const SmartPOS = {
  cart: [],
  selectedTableId: null,
  selectedTableNumber: null,
  selectedSessionId: null,
  selectedFloorId: null,
  appliedCoupon: null,
  allProducts: [],
  allCategories: [],
  allTables: [],
  allFloors: [],
  selectedCategory: '',
  paymentMethod: 'Cash',
  modalProduct: null,
  modalQty: 1,

  async init() {
    await Promise.all([
      this.loadFloorsAndTables(),
      this.loadProductCatalog()
    ]);
  },

  // ==========================================
  // 1. FLOOR & TABLE SELECTION OPERATIONS
  // ==========================================
  async loadFloorsAndTables() {
    try {
      const [fRes, tRes] = await Promise.all([
        SmartAPI.get('api/v1/floors/index.php'),
        SmartAPI.get('api/v1/tables/index.php')
      ]);

      this.allFloors = fRes.data || [];
      this.allTables = tRes.data || [];

      // Populate Floor Dropdown
      const floorSelect = document.getElementById('pos-floor-selector');
      if (floorSelect) {
        floorSelect.innerHTML = `<option value="">All Floors</option>` +
          this.allFloors.map(f => `<option value="${f.id}">${f.name}</option>`).join('');
      }

      this.renderTableDropdown();
    } catch (err) {
      console.error("Failed to load floors/tables:", err);
    }
  },

  onFloorChanged() {
    const floorSelect = document.getElementById('pos-floor-selector');
    this.selectedFloorId = floorSelect ? floorSelect.value : null;
    this.renderTableDropdown();
  },

  renderTableDropdown() {
    const tableSelect = document.getElementById('pos-table-selector');
    if (!tableSelect) return;

    let filtered = this.allTables;
    if (this.selectedFloorId) {
      filtered = filtered.filter(t => String(t.floor_id) === String(this.selectedFloorId));
    }

    tableSelect.innerHTML = `<option value="">— Select Table —</option>` +
      filtered.map(t => {
        const sessionInfo = t.current_session_id ? ` (Session #${t.current_session_id})` : '';
        return `<option value="${t.id}" data-table-num="${t.table_number}" data-status="${t.status}" data-session="${t.current_session_id || ''}">
          Table ${t.table_number} [${t.status}] (${t.capacity} Seats)${sessionInfo}
        </option>`;
      }).join('');
  },

  onTableSelected() {
    const select = document.getElementById('pos-table-selector');
    if (!select || !select.value) {
      this.selectedTableId = null;
      this.selectedTableNumber = null;
      this.selectedSessionId = null;
      this.updateTableBadge('No Table Selected', 'warning');
      return;
    }

    const opt = select.options[select.selectedIndex];
    this.selectedTableId = parseInt(select.value);
    this.selectedTableNumber = opt.getAttribute('data-table-num') || select.value;
    this.selectedSessionId = opt.getAttribute('data-session') || null;
    const status = opt.getAttribute('data-status') || 'AVAILABLE';

    const statusBadgeClass = status === 'OCCUPIED' ? 'danger' : (status === 'RESERVED' ? 'warning' : 'success');
    this.updateTableBadge(`Table ${this.selectedTableNumber} (${status})`, statusBadgeClass);

    const cartHeading = document.getElementById('pos-table-heading-cart');
    if (cartHeading) {
      cartHeading.textContent = `🛒 Table ${this.selectedTableNumber} Order`;
    }
    const sessionBadge = document.getElementById('pos-session-badge');
    if (sessionBadge) {
      sessionBadge.textContent = this.selectedSessionId ? `Dining Session #${this.selectedSessionId}` : 'New Dining Session';
    }
  },

  selectTakeaway() {
    this.selectedTableId = null;
    this.selectedTableNumber = null;
    this.selectedSessionId = null;

    const select = document.getElementById('pos-table-selector');
    if (select) select.value = '';

    this.updateTableBadge('Takeaway / Walk-in Order', 'info');

    const cartHeading = document.getElementById('pos-table-heading-cart');
    if (cartHeading) cartHeading.textContent = '🛍️ Takeaway Order';

    const sessionBadge = document.getElementById('pos-session-badge');
    if (sessionBadge) sessionBadge.textContent = 'Takeaway Order Checklist';

    if (window.SmartNotifications) {
      SmartNotifications.show('Order mode set to Takeaway / Walk-in', 'info', 2000);
    }
  },

  updateTableBadge(text, type = 'info') {
    const badge = document.getElementById('pos-table-heading');
    if (badge) {
      badge.className = `badge badge-${type}`;
      badge.textContent = text;
    }
  },

  // ==========================================
  // 2. DISH CATALOG & CATEGORY FILTERS
  // ==========================================
  async loadProductCatalog() {
    try {
      const grid = document.getElementById('pos-product-grid');
      if (grid) grid.innerHTML = `<div style="grid-column:1/-1; text-align:center; padding:40px; color:var(--text-muted);"><p>Loading menu items from database...</p></div>`;

      const [pRes, cRes] = await Promise.all([
        SmartAPI.get('api/v1/products/index.php'),
        SmartAPI.get('api/v1/categories/index.php')
      ]);

      this.allProducts = pRes.data || [];
      this.allCategories = cRes.data || [];

      // Render Dynamic Category Pills
      const pillsContainer = document.getElementById('pos-category-pills');
      if (pillsContainer) {
        pillsContainer.innerHTML = `<button class="category-pill active" onclick="SmartPOS.filterCategory('', this)">All Items</button>` +
          this.allCategories.map(c => `
            <button class="category-pill" onclick="SmartPOS.filterCategory('${c.id}', this)">${c.name}</button>
          `).join('');
      }

      this.filterMenu();
    } catch (err) {
      console.error("Failed to load catalog:", err);
      const grid = document.getElementById('pos-product-grid');
      if (grid) grid.innerHTML = `<div style="grid-column:1/-1; text-align:center; color:var(--danger); padding:40px;">Failed to connect to database catalog.</div>`;
    }
  },

  filterCategory(catId, btn) {
    this.selectedCategory = catId;
    document.querySelectorAll('#pos-category-pills .category-pill').forEach(b => b.classList.remove('active'));
    if (btn) btn.classList.add('active');
    this.filterMenu();
  },

  filterMenu() {
    const q = (document.getElementById('pos-search-input')?.value || '').toLowerCase().trim();
    let list = this.allProducts;

    if (this.selectedCategory) {
      if (this.selectedCategory === 'main') {
        list = list.filter(p => (p.category_name || '').toLowerCase().includes('main') || (p.category_name || '').toLowerCase().includes('burger') || (p.category_name || '').toLowerCase().includes('pizza') || (p.category_name || '').toLowerCase().includes('rice') || (p.category_name || '').toLowerCase().includes('pasta'));
      } else if (this.selectedCategory === 'side') {
        list = list.filter(p => (p.category_name || '').toLowerCase().includes('side') || (p.category_name || '').toLowerCase().includes('fries') || (p.category_name || '').toLowerCase().includes('salad') || (p.category_name || '').toLowerCase().includes('soup'));
      } else if (this.selectedCategory === 'dessert') {
        list = list.filter(p => (p.category_name || '').toLowerCase().includes('dessert') || (p.category_name || '').toLowerCase().includes('cake') || (p.category_name || '').toLowerCase().includes('ice'));
      } else if (this.selectedCategory === 'beverage') {
        list = list.filter(p => (p.category_name || '').toLowerCase().includes('beverage') || (p.category_name || '').toLowerCase().includes('drink') || (p.category_name || '').toLowerCase().includes('juice') || (p.category_name || '').toLowerCase().includes('coffee'));
      } else {
        list = list.filter(p => String(p.category_id) === String(this.selectedCategory));
      }
    }

    if (q) {
      list = list.filter(p =>
        p.name.toLowerCase().includes(q) ||
        (p.short_description || '').toLowerCase().includes(q) ||
        (p.sku || '').toLowerCase().includes(q)
      );
    }

    this.renderGrid(list);
  },

  renderGrid(items) {
    const grid = document.getElementById('pos-product-grid');
    if (!grid) return;

    if (!items || items.length === 0) {
      grid.innerHTML = `<div style="grid-column:1/-1; text-align:center; padding:40px; color:var(--text-muted);"><div style="font-size:2.5rem;">🍽️</div><p>No dishes matching filter.</p></div>`;
      return;
    }

    grid.innerHTML = items.map(p => {
      const isAvailable = (p.is_available == 1);
      const priceFormatted = parseFloat(p.price || 0).toFixed(2);
      const categoryName = p.category_name || 'General';

      return `
        <div class="product-card ${!isAvailable ? 'disabled' : ''}" style="${!isAvailable ? 'opacity:0.6;' : ''}">
          <div style="display:flex; justify-size:space-between; align-items:center; margin-bottom:4px;">
            <span class="text-xs" style="color:var(--accent); font-weight:600; text-transform:uppercase;">${categoryName}</span>
            ${!isAvailable ? `<span class="badge badge-danger text-xs">OUT OF STOCK</span>` : ''}
          </div>

          <div class="product-title" style="font-weight:700; font-size:1rem; margin-bottom:4px;">${p.name}</div>
          <div class="text-sm text-muted" style="font-size:0.8rem; margin-bottom:8px; line-height:1.3; min-height:2.6em;">
            ${p.short_description || 'Fresh gourmet restaurant dish.'}
          </div>

          <div class="product-meta" style="margin-top:auto; display:flex; justify-content:space-between; align-items:center;">
            <span class="price-tag" style="font-size:1.1rem; font-weight:700; color:var(--accent);">৳${priceFormatted}</span>
            
            ${isAvailable ? `
              <button class="btn btn-primary btn-sm" onclick="SmartPOS.openCustomizeModalById(${p.id})">
                + Add
              </button>
            ` : `
              <button class="btn btn-secondary btn-sm" disabled>Unavailable</button>
            `}
          </div>
        </div>
      `;
    }).join('');
  },

  // ==========================================
  // 3. ITEM CUSTOMIZATION & INSTRUCTION MODAL
  // ==========================================
  openCustomizeModalById(productId) {
    const prod = this.allProducts.find(p => p.id === productId);
    if (!prod) return;
    this.openCustomizeModal(prod);
  },

  openCustomizeModal(product) {
    this.modalProduct = product;
    this.modalQty = 1;

    document.getElementById('pos-modal-dish-name').textContent = `Add "${product.name}"`;
    document.getElementById('pos-modal-dish-price').textContent = `৳${parseFloat(product.price).toFixed(2)}`;
    document.getElementById('pos-modal-qty-display').textContent = '1';
    document.getElementById('pos-modal-notes-input').value = '';

    document.getElementById('modal-pos-item-customize').classList.add('active');
  },

  adjustModalQty(delta) {
    this.modalQty += delta;
    if (this.modalQty < 1) this.modalQty = 1;
    document.getElementById('pos-modal-qty-display').textContent = this.modalQty;
  },

  confirmAddModalItem() {
    if (!this.modalProduct) return;
    const notes = (document.getElementById('pos-modal-notes-input').value || '').trim();

    this.addItem(
      this.modalProduct.id,
      this.modalProduct.name,
      this.modalProduct.price,
      notes,
      this.modalProduct.station_name || 'Main Kitchen'
    );

    document.getElementById('modal-pos-item-customize').classList.remove('active');
  },

  // ==========================================
  // 4. CART & ORDER CHECKLIST OPERATIONS
  // ==========================================
  addItem(productId, name, basePrice, notes = '', stationName = 'Main Kitchen') {
    const unitPrice = parseFloat(basePrice);
    const cartKey = `${productId}_${notes.replace(/\s+/g, '-').toLowerCase()}`;
    const existing = this.cart.find(item => item.key === cartKey);

    if (existing) {
      existing.qty += this.modalQty;
    } else {
      this.cart.push({
        key: cartKey,
        product_id: productId,
        name: name,
        unit_price: unitPrice,
        qty: this.modalQty,
        notes: notes,
        station: stationName
      });
    }

    this.renderCart();
    if (window.SmartNotifications) {
      SmartNotifications.show(`Added "${name}" (x${this.modalQty}) to checklist`, 'success', 2000);
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
    this.appliedCoupon = null;
    const couponInput = document.getElementById('pos-coupon-input');
    if (couponInput) couponInput.value = '';
    const couponBadge = document.getElementById('pos-coupon-status-badge');
    if (couponBadge) couponBadge.style.display = 'none';
    const clearBtn = document.getElementById('pos-btn-clear-coupon');
    if (clearBtn) clearBtn.style.display = 'none';

    this.renderCart();
  },

  setPaymentMethod(method) {
    this.paymentMethod = method;
    document.querySelectorAll('.payment-method-btn').forEach(btn => {
      btn.classList.toggle('active', btn.dataset.method === method);
    });
  },

  // ==========================================
  // 5. COUPON ENGINE INTEGRATION
  // ==========================================
  async applyCoupon() {
    const input = document.getElementById('pos-coupon-input');
    const code = (input ? input.value : '').trim();

    if (!code) {
      if (window.SmartNotifications) SmartNotifications.show('Please enter a coupon code first', 'warning');
      return;
    }

    if (this.cart.length === 0) {
      if (window.SmartNotifications) SmartNotifications.show('Add dishes to cart before applying coupon', 'warning');
      return;
    }

    const { subtotal } = this.calculateTotal();

    try {
      const res = await SmartAPI.post('api/v1/crm/coupons.php', {
        action: 'validate',
        code: code,
        order_amount: subtotal
      });

      if (res.success && res.data) {
        this.appliedCoupon = {
          code: res.data.code,
          discount_type: res.data.discount_type,
          discount_value: res.data.discount_value,
          discount_amount: parseFloat(res.data.discount_amount || 0)
        };

        const badge = document.getElementById('pos-coupon-status-badge');
        if (badge) {
          badge.style.display = 'block';
          badge.innerHTML = `🎟️ Coupon <strong>${res.data.code}</strong> Applied (-৳${this.appliedCoupon.discount_amount.toFixed(2)}) ✓`;
        }

        const clearBtn = document.getElementById('pos-btn-clear-coupon');
        if (clearBtn) clearBtn.style.display = 'inline-block';

        if (window.SmartNotifications) {
          SmartNotifications.show(`Coupon "${res.data.code}" applied! Saved ৳${this.appliedCoupon.discount_amount.toFixed(2)}`, 'success');
        }
        this.renderCart();
      } else {
        throw new Error(res.message || 'Invalid coupon code');
      }
    } catch (err) {
      if (window.SmartNotifications) {
        SmartNotifications.show(err.message || 'Coupon validation failed', 'danger');
      }
      this.clearCoupon();
    }
  },

  clearCoupon() {
    this.appliedCoupon = null;
    const input = document.getElementById('pos-coupon-input');
    if (input) input.value = '';
    const badge = document.getElementById('pos-coupon-status-badge');
    if (badge) badge.style.display = 'none';
    const clearBtn = document.getElementById('pos-btn-clear-coupon');
    if (clearBtn) clearBtn.style.display = 'none';
    this.renderCart();
  },

  // ==========================================
  // 6. TOTALS CALCULATION
  // ==========================================
  calculateTotal() {
    const subtotal = this.cart.reduce((sum, item) => sum + (item.unit_price * item.qty), 0);
    let discount = 0;

    if (this.appliedCoupon) {
      if (this.appliedCoupon.discount_type === 'PERCENTAGE') {
        discount = roundMoney((subtotal * this.appliedCoupon.discount_value) / 100);
      } else {
        discount = Math.min(subtotal, parseFloat(this.appliedCoupon.discount_amount));
      }
    }

    const taxable = Math.max(0, subtotal - discount);
    const tax = roundMoney(taxable * 0.05); // 5% VAT
    const total = roundMoney(taxable + tax);

    return { subtotal, discount, tax, total };
  },

  renderCart() {
    const cartContainer = document.getElementById('pos-cart-items');
    if (!cartContainer) return;

    if (this.cart.length === 0) {
      cartContainer.innerHTML = `
        <div style="text-align:center; padding: 40px var(--space-4); color: var(--text-muted);">
          <div style="font-size: 2.2rem; margin-bottom: 8px;">🛒</div>
          <p style="font-size:0.875rem;">No items in order checklist.</p>
        </div>
      `;
    } else {
      cartContainer.innerHTML = this.cart.map(item => `
        <div class="cart-item" style="padding:10px 0; border-bottom:1px dashed var(--border);">
          <div class="cart-item-info">
            <span class="cart-item-name" style="font-weight:600;">${item.name}</span>
            <span class="cart-item-meta" style="font-size:0.8rem; color:var(--text-muted);">
              ৳${item.unit_price.toFixed(2)} × ${item.qty} = <strong>৳${(item.unit_price * item.qty).toFixed(2)}</strong>
            </span>
            ${item.notes ? `
              <div style="font-size:0.78rem; color:#F59E0B; font-weight:600; margin-top:2px;">
                📝 Instruction: "${item.notes}"
              </div>
            ` : ''}
          </div>

          <div class="cart-qty-controls" style="display:flex; align-items:center; gap:6px;">
            <button class="cart-qty-btn" onclick="SmartPOS.updateQty('${item.key}', -1)">-</button>
            <span style="font-weight:700; font-size:0.9rem; min-width:18px; text-align:center;">${item.qty}</span>
            <button class="cart-qty-btn" onclick="SmartPOS.updateQty('${item.key}', 1)">+</button>
          </div>
        </div>
      `).join('');
    }

    const { subtotal, discount, tax, total } = this.calculateTotal();

    const subtotalEl = document.getElementById('cart-subtotal');
    const discountRow = document.getElementById('cart-discount-row');
    const discountEl = document.getElementById('cart-discount');
    const taxEl = document.getElementById('cart-tax');
    const totalEl = document.getElementById('cart-total');

    if (subtotalEl) subtotalEl.textContent = `৳${subtotal.toFixed(2)}`;

    if (discountRow) {
      if (discount > 0) {
        discountRow.style.display = 'flex';
        if (discountEl) discountEl.textContent = `-৳${discount.toFixed(2)}`;
      } else {
        discountRow.style.display = 'none';
      }
    }

    if (taxEl) taxEl.textContent = `৳${tax.toFixed(2)}`;
    if (totalEl) totalEl.textContent = `৳${total.toFixed(2)}`;
  },

  // ==========================================
  // 7. TRANSACTIONAL ORDER SUBMISSION
  // ==========================================
  async submitOrder() {
    if (this.cart.length === 0) {
      SmartNotifications.show('Please add dishes to order checklist before submitting', 'warning');
      return;
    }

    let orderType = 'DINE_IN';
    if (!this.selectedTableId) {
      const tableSelect = document.getElementById('pos-table-selector');
      if (tableSelect && tableSelect.value) {
        this.selectedTableId = parseInt(tableSelect.value);
        const opt = tableSelect.options[tableSelect.selectedIndex];
        this.selectedSessionId = opt.getAttribute('data-session') || null;
      } else {
        orderType = 'TAKEAWAY';
      }
    }

    const submitBtn = document.getElementById('pos-submit-order-btn');
    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.innerHTML = '⏳ Submitting Order...';
    }

    try {
      // 1. Create Draft Order
      const draftRes = await SmartAPI.post('api/v1/orders/index.php', {
        order_type: orderType,
        table_id: this.selectedTableId || null,
        dining_session_id: this.selectedSessionId || null
      });

      if (!draftRes.success || !draftRes.data || !draftRes.data.order_id) {
        throw new Error(draftRes.message || 'Failed to initialize draft order');
      }

      const orderId = draftRes.data.order_id;

      // 2. Add Line Items & Special Instructions to Order
      for (const item of this.cart) {
        await SmartAPI.post('api/v1/orders/items.php', {
          order_id: orderId,
          product_id: item.product_id,
          quantity: item.qty,
          notes: item.notes || null
        });
      }

      // 3. Apply Coupon if present
      if (this.appliedCoupon) {
        await SmartAPI.post('api/v1/orders/coupon.php', {
          action: 'apply',
          order_id: orderId,
          coupon_code: this.appliedCoupon.code
        });
      }

      // 4. Submit & Route Order to Kitchen KDS
      const submitRes = await SmartAPI.post('api/v1/orders/submit.php', { order_id: orderId });

      if (submitRes.success) {
        const orderNum = submitRes.data ? submitRes.data.order_number : orderId;
        const targetDesc = this.selectedTableId ? `Table ${this.selectedTableNumber}` : 'Takeaway';

        SmartNotifications.show(`🎉 Order #${orderNum} for ${targetDesc} successfully placed & sent to kitchen!`, 'success', 4000);

        this.clearCart();
        await this.loadFloorsAndTables();

        if (window.loadActiveOrders) window.loadActiveOrders();
        if (window.loadFloorTables) window.loadFloorTables();
      } else {
        throw new Error(submitRes.message || 'Failed to submit order');
      }

    } catch (err) {
      SmartNotifications.show(err.message || 'Failed to place order in database', 'danger');
    } finally {
      if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '🚀 PLACE ORDER & ROUTE TO KITCHEN';
      }
    }
  }
};

function roundMoney(val) {
  return Math.round((val + Number.EPSILON) * 100) / 100;
}

window.SmartPOS = SmartPOS;

document.addEventListener('DOMContentLoaded', () => {
  if (document.getElementById('pos') || document.getElementById('pos-view')) {
    SmartPOS.init();
  }
});
