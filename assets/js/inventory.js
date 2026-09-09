/**
 * SMARTRESTA Inventory, Purchasing, Recipe Costing & Stock Management Controller
 * Prompt 11: Inventory Engine & Stock Control
 */

const SmartInventory = {
  ingredients: [],
  categories: [],
  locations: [],
  suppliers: [],
  purchaseOrders: [],
  recipes: [],
  activeSubTab: 'stock',

  async init() {
    await this.loadInitialData();
  },

  async loadInitialData() {
    await Promise.all([
      this.loadStockBalances(),
      this.loadSuppliersList(),
      this.loadRecipesList(),
      this.loadPurchaseOrdersList()
    ]);
  },

  switchSubTab(tabName) {
    this.activeSubTab = tabName;
    const tabs = ['stock', 'recipes', 'purchases', 'suppliers', 'wastage', 'ledger'];
    tabs.forEach(t => {
      const section = document.getElementById(`inv-section-${t}`);
      const btn = document.getElementById(`inv-tab-btn-${t}`);
      if (section) section.style.display = (t === tabName) ? 'block' : 'none';
      if (btn) {
        if (t === tabName) {
          btn.classList.add('active-filter', 'btn-primary');
          btn.classList.remove('btn-secondary');
        } else {
          btn.classList.remove('active-filter', 'btn-primary');
          btn.classList.add('btn-secondary');
        }
      }
    });

    if (tabName === 'stock') this.loadStockBalances();
    if (tabName === 'recipes') this.loadRecipesList();
    if (tabName === 'purchases') this.loadPurchaseOrdersList();
    if (tabName === 'suppliers') this.loadSuppliersList();
    if (tabName === 'wastage') this.loadWastageLogs();
    if (tabName === 'ledger') this.loadLedgerTransactions();
  },

  // -------------------------------------------------------------
  // INGREDIENTS & STOCK BALANCES
  // -------------------------------------------------------------
  async loadStockBalances() {
    try {
      const res = await SmartAPI.get('api/v1/ingredients/index.php');
      if (res.success && res.data) {
        this.ingredients = res.data.ingredients || [];
        this.categories = res.data.categories || [];
        this.locations = res.data.locations || [];
        this.renderIngredientsTable();
      }
    } catch (err) {
      console.warn('Failed to load ingredients/stock:', err.message);
    }
  },

  renderIngredientsTable() {
    const tbody = document.getElementById('ingredients-table-body');
    if (!tbody) return;

    if (this.ingredients.length === 0) {
      tbody.innerHTML = `<tr><td colspan="7" class="text-center text-muted" style="padding:24px;">No ingredients configured yet. Click "Add Ingredient" to create one.</td></tr>`;
      return;
    }

    tbody.innerHTML = this.ingredients.map(ing => {
      const currentQty = parseFloat(ing.total_stock || 0);
      const minAlert = parseFloat(ing.min_stock_alert || 0);
      const isLowStock = currentQty <= minAlert;
      const statusBadge = isLowStock 
        ? `<span class="badge badge-danger">Low Stock (${currentQty} ${ing.unit_of_measure})</span>`
        : `<span class="badge badge-success">In Stock (${currentQty} ${ing.unit_of_measure})</span>`;

      return `
        <tr>
          <td>
            <div style="font-weight:600; color:var(--text-primary);">${ing.name}</div>
            <div class="text-sm text-muted font-mono">${ing.code}</div>
          </td>
          <td><span class="badge badge-info">${ing.category_name || 'General'}</span></td>
          <td><strong>${currentQty.toFixed(2)} ${ing.unit_of_measure}</strong></td>
          <td>৳${parseFloat(ing.average_cost || 0).toFixed(2)} / ${ing.unit_of_measure}</td>
          <td>${minAlert.toFixed(2)} ${ing.unit_of_measure}</td>
          <td>${statusBadge}</td>
          <td>
            <button class="btn btn-sm btn-secondary" onclick="SmartInventory.openEditIngredientModal(${ing.id})">Edit</button>
            <button class="btn btn-sm btn-primary" onclick="SmartInventory.openAdjustModal(${ing.id}, '${ing.name}')">Adjust Stock</button>
          </td>
        </tr>
      `;
    }).join('');
  },

  openAddIngredientModal() {
    document.getElementById('ing-modal-title').textContent = 'Add New Ingredient';
    document.getElementById('ing-id').value = '';
    document.getElementById('ing-code').value = 'ING-' + Math.floor(1000 + Math.random() * 9000);
    document.getElementById('ing-name').value = '';
    document.getElementById('ing-uom').value = 'kg';
    document.getElementById('ing-cost').value = '0.00';
    document.getElementById('ing-min-alert').value = '10.00';
    
    this.populateCategoryOptions();
    SmartModal.open('ingredient-modal');
  },

  openEditIngredientModal(id) {
    const ing = this.ingredients.find(i => parseInt(i.id) === parseInt(id));
    if (!ing) return;

    document.getElementById('ing-modal-title').textContent = `Edit Ingredient — ${ing.name}`;
    document.getElementById('ing-id').value = ing.id;
    document.getElementById('ing-code').value = ing.code;
    document.getElementById('ing-name').value = ing.name;
    document.getElementById('ing-uom').value = ing.unit_of_measure;
    document.getElementById('ing-cost').value = ing.average_cost;
    document.getElementById('ing-min-alert').value = ing.min_stock_alert;

    this.populateCategoryOptions(ing.category_id);
    SmartModal.open('ingredient-modal');
  },

  populateCategoryOptions(selectedId = null) {
    const select = document.getElementById('ing-category-id');
    if (!select) return;
    select.innerHTML = `<option value="">Select Category...</option>` + 
      this.categories.map(c => `<option value="${c.id}" ${parseInt(c.id) === parseInt(selectedId) ? 'selected' : ''}>${c.name}</option>`).join('');
  },

  async saveIngredient() {
    const data = {
      id: document.getElementById('ing-id').value,
      code: document.getElementById('ing-code').value.trim(),
      name: document.getElementById('ing-name').value.trim(),
      category_id: document.getElementById('ing-category-id').value,
      unit_of_measure: document.getElementById('ing-uom').value.trim(),
      average_cost: parseFloat(document.getElementById('ing-cost').value || 0),
      min_stock_alert: parseFloat(document.getElementById('ing-min-alert').value || 0)
    };

    if (!data.name || !data.unit_of_measure) {
      SmartNotifications.show('Name and unit of measure are required', 'warning');
      return;
    }

    try {
      const res = await SmartAPI.post('api/v1/ingredients/index.php', data);
      if (res.success) {
        SmartNotifications.show('Ingredient saved successfully', 'success');
        SmartModal.close('ingredient-modal');
        await this.loadStockBalances();
      }
    } catch (err) {
      SmartNotifications.show(err.message || 'Failed to save ingredient', 'danger');
    }
  },

  openAdjustModal(ingId, ingName) {
    document.getElementById('adj-ing-id').value = ingId;
    document.getElementById('adj-modal-title').textContent = `Adjust Stock — ${ingName}`;
    document.getElementById('adj-qty').value = '1.00';
    document.getElementById('adj-reason').value = 'Stock audit discrepancy';
    
    const locSelect = document.getElementById('adj-location-id');
    if (locSelect) {
      locSelect.innerHTML = this.locations.map(l => `<option value="${l.id}">${l.name} (${l.code})</option>`).join('');
    }

    SmartModal.open('adjust-stock-modal');
  },

  async submitAdjustStock() {
    const data = {
      ingredient_id: parseInt(document.getElementById('adj-ing-id').value),
      location_id: parseInt(document.getElementById('adj-location-id').value),
      type: document.getElementById('adj-type').value,
      quantity: parseFloat(document.getElementById('adj-qty').value || 0),
      reason: document.getElementById('adj-reason').value.trim()
    };

    if (!data.quantity || data.quantity <= 0) {
      SmartNotifications.show('Quantity must be greater than zero', 'warning');
      return;
    }

    try {
      const res = await SmartAPI.post('api/v1/inventory/adjust.php', data);
      if (res.success) {
        SmartNotifications.show(res.message || 'Stock adjusted successfully', 'success');
        SmartModal.close('adjust-stock-modal');
        await this.loadStockBalances();
      }
    } catch (err) {
      SmartNotifications.show(err.message || 'Failed to adjust stock', 'danger');
    }
  },

  // -------------------------------------------------------------
  // RECIPES & BOM BUILDER
  // -------------------------------------------------------------
  async loadRecipesList() {
    try {
      const res = await SmartAPI.get('api/v1/recipes/index.php');
      if (res.success && res.data) {
        this.recipes = res.data.recipes || [];
        this.renderRecipesTable();
      }
    } catch (err) {
      console.warn('Failed to load recipes list:', err.message);
    }
  },

  renderRecipesTable() {
    const tbody = document.getElementById('recipes-table-body');
    if (!tbody) return;

    if (this.recipes.length === 0) {
      tbody.innerHTML = `<tr><td colspan="7" class="text-center text-muted" style="padding:24px;">No recipe BOMs configured yet. Click "Configure Recipe" to link menu items with raw ingredients.</td></tr>`;
      return;
    }

    tbody.innerHTML = this.recipes.map(r => {
      const cost = parseFloat(r.costing.recipe_cost || 0);
      const price = parseFloat(r.costing.selling_price || 0);
      const margin = parseFloat(r.costing.profit_margin_percent || 0);
      const marginBadge = margin >= 60 
        ? `<span class="badge badge-success">${margin.toFixed(1)}% Margin</span>`
        : (margin >= 30 ? `<span class="badge badge-warning">${margin.toFixed(1)}% Margin</span>` : `<span class="badge badge-danger">${margin.toFixed(1)}% Low Margin</span>`);

      return `
        <tr>
          <td>
            <div style="font-weight:600; color:var(--text-primary);">${r.product_name}</div>
            ${r.variant_name ? `<div class="text-sm text-muted">Variant: ${r.variant_name}</div>` : ''}
          </td>
          <td><span class="badge badge-neutral">${r.ingredient_count} Ingredients</span></td>
          <td>Yield: <strong>${r.yield_quantity}</strong></td>
          <td>৳${cost.toFixed(2)}</td>
          <td>৳${price.toFixed(2)}</td>
          <td>${marginBadge}</td>
          <td>
            <button class="btn btn-sm btn-primary" onclick="SmartInventory.openRecipeModal(${r.product_id}, ${r.variant_id || 'null'})">Edit Recipe BOM</button>
          </td>
        </tr>
      `;
    }).join('');
  },

  async openRecipeModal(productId, variantId = null) {
    try {
      let url = `api/v1/recipes/index.php?product_id=${productId}`;
      if (variantId) url += `&variant_id=${variantId}`;

      const res = await SmartAPI.get(url);
      if (res.success && res.data) {
        const recipe = res.data.recipe || { product_id: productId, variant_id: variantId, yield_quantity: 1.0, items: [] };
        const costing = res.data.costing || {};

        document.getElementById('rec-product-id').value = productId;
        document.getElementById('rec-variant-id').value = variantId || '';
        document.getElementById('rec-yield-qty').value = recipe.yield_quantity || 1.0;
        document.getElementById('rec-notes').value = recipe.notes || '';

        this.renderRecipeItemsBuilder(recipe.items || []);
        this.renderRecipeCostingSummary(costing);

        SmartModal.open('recipe-builder-modal');
      }
    } catch (err) {
      SmartNotifications.show('Failed to open recipe modal: ' + err.message, 'danger');
    }
  },

  renderRecipeItemsBuilder(items = []) {
    const container = document.getElementById('rec-items-container');
    if (!container) return;

    if (items.length === 0) {
      container.innerHTML = `<div id="no-rec-items" class="text-center text-muted" style="padding:16px;">No ingredients added to this recipe. Click "+ Add Ingredient Row".</div>`;
      return;
    }

    container.innerHTML = items.map((item, idx) => `
      <div class="recipe-item-row" style="display:flex; gap:10px; align-items:center; margin-bottom:8px; background:var(--surface); padding:8px 12px; border-radius:6px; border:1px solid var(--border);">
        <select class="form-select rec-ing-select" style="flex:2;" onchange="SmartInventory.calculateLiveRecipeCost()">
          ${this.ingredients.map(i => `<option value="${i.id}" data-cost="${i.average_cost}" data-uom="${i.unit_of_measure}" ${parseInt(i.id) === parseInt(item.ingredient_id) ? 'selected' : ''}>${i.name} (${i.unit_of_measure}) — ৳${parseFloat(i.average_cost).toFixed(2)}</option>`).join('')}
        </select>
        <input type="number" step="0.001" class="form-control rec-qty-input" style="flex:1;" value="${item.quantity}" placeholder="Qty" oninput="SmartInventory.calculateLiveRecipeCost()">
        <input type="number" step="0.01" class="form-control rec-factor-input" style="flex:1;" value="${item.conversion_factor || 1.0}" placeholder="Conv Factor" oninput="SmartInventory.calculateLiveRecipeCost()">
        <button class="btn btn-sm btn-secondary" onclick="this.closest('.recipe-item-row').remove(); SmartInventory.calculateLiveRecipeCost();">✕</button>
      </div>
    `).join('');
  },

  addRecipeItemRow() {
    const noItems = document.getElementById('no-rec-items');
    if (noItems) noItems.remove();

    const container = document.getElementById('rec-items-container');
    const newRow = document.createElement('div');
    newRow.className = 'recipe-item-row';
    newRow.style.cssText = 'display:flex; gap:10px; align-items:center; margin-bottom:8px; background:var(--surface); padding:8px 12px; border-radius:6px; border:1px solid var(--border);';
    newRow.innerHTML = `
      <select class="form-select rec-ing-select" style="flex:2;" onchange="SmartInventory.calculateLiveRecipeCost()">
        ${this.ingredients.map(i => `<option value="${i.id}" data-cost="${i.average_cost}" data-uom="${i.unit_of_measure}">${i.name} (${i.unit_of_measure}) — ৳${parseFloat(i.average_cost).toFixed(2)}</option>`).join('')}
      </select>
      <input type="number" step="0.001" class="form-control rec-qty-input" style="flex:1;" value="0.100" placeholder="Qty" oninput="SmartInventory.calculateLiveRecipeCost()">
      <input type="number" step="0.01" class="form-control rec-factor-input" style="flex:1;" value="1.00" placeholder="Conv Factor" oninput="SmartInventory.calculateLiveRecipeCost()">
      <button class="btn btn-sm btn-secondary" onclick="this.closest('.recipe-item-row').remove(); SmartInventory.calculateLiveRecipeCost();">✕</button>
    `;
    container.appendChild(newRow);
    this.calculateLiveRecipeCost();
  },

  calculateLiveRecipeCost() {
    let totalCost = 0.0;
    document.querySelectorAll('.recipe-item-row').forEach(row => {
      const select = row.querySelector('.rec-ing-select');
      const qtyInput = row.querySelector('.rec-qty-input');
      const factorInput = row.querySelector('.rec-factor-input');

      if (select && qtyInput) {
        const option = select.options[select.selectedIndex];
        const cost = parseFloat(option ? option.getAttribute('data-cost') || 0 : 0);
        const qty = parseFloat(qtyInput.value || 0);
        const factor = parseFloat(factorInput ? factorInput.value || 1.0 : 1.0);

        totalCost += (qty * factor * cost);
      }
    });

    const yieldQty = parseFloat(document.getElementById('rec-yield-qty').value || 1.0);
    const unitCost = yieldQty > 0 ? totalCost / yieldQty : totalCost;

    const elCost = document.getElementById('rec-live-cost');
    if (elCost) elCost.textContent = `৳${unitCost.toFixed(2)}`;
  },

  renderRecipeCostingSummary(c) {
    const elCost = document.getElementById('rec-live-cost');
    if (elCost) elCost.textContent = `৳${parseFloat(c.recipe_cost || 0).toFixed(2)}`;
  },

  async saveRecipe() {
    const items = [];
    document.querySelectorAll('.recipe-item-row').forEach(row => {
      const select = row.querySelector('.rec-ing-select');
      const qtyInput = row.querySelector('.rec-qty-input');
      const factorInput = row.querySelector('.rec-factor-input');

      if (select && qtyInput) {
        items.push({
          ingredient_id: parseInt(select.value),
          quantity: parseFloat(qtyInput.value || 0),
          conversion_factor: parseFloat(factorInput ? factorInput.value || 1.0 : 1.0)
        });
      }
    });

    const data = {
      product_id: parseInt(document.getElementById('rec-product-id').value || 0),
      variant_id: document.getElementById('rec-variant-id').value ? parseInt(document.getElementById('rec-variant-id').value) : null,
      yield_quantity: parseFloat(document.getElementById('rec-yield-qty').value || 1.0),
      notes: document.getElementById('rec-notes').value.trim(),
      items: items
    };

    try {
      const res = await SmartAPI.post('api/v1/recipes/index.php', data);
      if (res.success) {
        SmartNotifications.show('Recipe BOM saved successfully', 'success');
        SmartModal.close('recipe-builder-modal');
        await this.loadRecipesList();
      }
    } catch (err) {
      SmartNotifications.show(err.message || 'Failed to save recipe BOM', 'danger');
    }
  },

  // -------------------------------------------------------------
  // SUPPLIERS & PURCHASING
  // -------------------------------------------------------------
  async loadSuppliersList() {
    try {
      const res = await SmartAPI.get('api/v1/suppliers/index.php');
      if (res.success && res.data) {
        this.suppliers = res.data.suppliers || [];
        this.renderSuppliersTable();
      }
    } catch (err) {
      console.warn('Failed to load suppliers:', err.message);
    }
  },

  renderSuppliersTable() {
    const tbody = document.getElementById('suppliers-table-body');
    if (!tbody) return;

    if (this.suppliers.length === 0) {
      tbody.innerHTML = `<tr><td colspan="6" class="text-center text-muted" style="padding:24px;">No suppliers configured yet. Click "Add Supplier" to add one.</td></tr>`;
      return;
    }

    tbody.innerHTML = this.suppliers.map(s => `
      <tr>
        <td>
          <div style="font-weight:600; color:var(--text-primary);">${s.name}</div>
          <div class="text-sm text-muted">${s.contact_person || ''}</div>
        </td>
        <td>${s.phone || 'N/A'}</td>
        <td>${s.email || 'N/A'}</td>
        <td>${s.city ? `${s.city}, ${s.address || ''}` : (s.address || 'N/A')}</td>
        <td><span class="badge ${s.status === 'ACTIVE' ? 'badge-success' : 'badge-neutral'}">${s.status}</span></td>
        <td>
          <button class="btn btn-sm btn-secondary" onclick="SmartInventory.openEditSupplierModal(${s.id})">Edit</button>
        </td>
      </tr>
    `).join('');
  },

  openAddSupplierModal() {
    document.getElementById('sup-id').value = '';
    document.getElementById('sup-name').value = '';
    document.getElementById('sup-contact').value = '';
    document.getElementById('sup-phone').value = '';
    document.getElementById('sup-email').value = '';
    document.getElementById('sup-address').value = '';
    SmartModal.open('supplier-modal');
  },

  openEditSupplierModal(id) {
    const s = this.suppliers.find(sup => parseInt(sup.id) === parseInt(id));
    if (!s) return;

    document.getElementById('sup-id').value = s.id;
    document.getElementById('sup-name').value = s.name;
    document.getElementById('sup-contact').value = s.contact_person || '';
    document.getElementById('sup-phone').value = s.phone || '';
    document.getElementById('sup-email').value = s.email || '';
    document.getElementById('sup-address').value = s.address || '';
    SmartModal.open('supplier-modal');
  },

  async saveSupplier() {
    const data = {
      id: document.getElementById('sup-id').value,
      name: document.getElementById('sup-name').value.trim(),
      contact_person: document.getElementById('sup-contact').value.trim(),
      phone: document.getElementById('sup-phone').value.trim(),
      email: document.getElementById('sup-email').value.trim(),
      address: document.getElementById('sup-address').value.trim()
    };

    if (!data.name) {
      SmartNotifications.show('Supplier name is required', 'warning');
      return;
    }

    try {
      const res = await SmartAPI.post('api/v1/suppliers/index.php', data);
      if (res.success) {
        SmartNotifications.show('Supplier saved successfully', 'success');
        SmartModal.close('supplier-modal');
        await this.loadSuppliersList();
      }
    } catch (err) {
      SmartNotifications.show(err.message || 'Failed to save supplier', 'danger');
    }
  },

  // -------------------------------------------------------------
  // PURCHASE ORDERS
  // -------------------------------------------------------------
  async loadPurchaseOrdersList() {
    try {
      const res = await SmartAPI.get('api/v1/purchases/index.php');
      if (res.success && res.data) {
        this.purchaseOrders = res.data.purchase_orders || [];
        this.renderPurchaseOrdersTable();
      }
    } catch (err) {
      console.warn('Failed to load purchase orders:', err.message);
    }
  },

  renderPurchaseOrdersTable() {
    const tbody = document.getElementById('purchases-table-body');
    if (!tbody) return;

    if (this.purchaseOrders.length === 0) {
      tbody.innerHTML = `<tr><td colspan="7" class="text-center text-muted" style="padding:24px;">No purchase orders found. Click "Create Purchase Order" to raise one.</td></tr>`;
      return;
    }

    tbody.innerHTML = this.purchaseOrders.map(po => {
      let statusBadge = 'badge-neutral';
      if (po.status === 'SUBMITTED') statusBadge = 'badge-info';
      if (po.status === 'APPROVED') statusBadge = 'badge-primary';
      if (po.status === 'PARTIALLY_RECEIVED') statusBadge = 'badge-warning';
      if (po.status === 'RECEIVED') statusBadge = 'badge-success';

      return `
        <tr>
          <td><strong class="font-mono">${po.po_number}</strong></td>
          <td>${po.supplier_name}</td>
          <td>${po.location_name}</td>
          <td>৳${parseFloat(po.total_amount || 0).toFixed(2)}</td>
          <td><span class="badge ${statusBadge}">${po.status}</span></td>
          <td>${po.created_at}</td>
          <td>
            ${po.status === 'DRAFT' || po.status === 'SUBMITTED' ? `
              <button class="btn btn-sm btn-success" onclick="SmartInventory.approvePO(${po.id})">Approve PO</button>
            ` : ''}
            ${po.status === 'APPROVED' || po.status === 'PARTIALLY_RECEIVED' ? `
              <button class="btn btn-sm btn-primary" onclick="SmartInventory.openReceiveGoodsModal(${po.id})">Receive Goods</button>
            ` : ''}
            <button class="btn btn-sm btn-secondary" onclick="SmartInventory.viewPODetails(${po.id})">View</button>
          </td>
        </tr>
      `;
    }).join('');
  },

  openCreatePOModal() {
    document.getElementById('po-sup-select').innerHTML = `<option value="">Select Supplier...</option>` +
      this.suppliers.map(s => `<option value="${s.id}">${s.name}</option>`).join('');
    
    document.getElementById('po-loc-select').innerHTML = this.locations.map(l => `<option value="${l.id}">${l.name} (${l.code})</option>`).join('');
    
    const container = document.getElementById('po-items-container');
    if (container) container.innerHTML = '';
    
    this.addPOItemRow();
    SmartModal.open('create-po-modal');
  },

  addPOItemRow() {
    const container = document.getElementById('po-items-container');
    const newRow = document.createElement('div');
    newRow.className = 'po-item-row';
    newRow.style.cssText = 'display:flex; gap:10px; align-items:center; margin-bottom:8px; background:var(--surface); padding:8px 12px; border-radius:6px; border:1px solid var(--border);';
    newRow.innerHTML = `
      <select class="form-select po-ing-select" style="flex:2;">
        ${this.ingredients.map(i => `<option value="${i.id}">${i.name} (${i.unit_of_measure})</option>`).join('')}
      </select>
      <input type="number" step="0.01" class="form-control po-qty-input" style="flex:1;" value="10.00" placeholder="Qty">
      <input type="number" step="0.01" class="form-control po-cost-input" style="flex:1;" value="100.00" placeholder="Unit Price">
      <button class="btn btn-sm btn-secondary" onclick="this.closest('.po-item-row').remove();">✕</button>
    `;
    container.appendChild(newRow);
  },

  async submitCreatePO() {
    const supplierId = parseInt(document.getElementById('po-sup-select').value || 0);
    const locationId = parseInt(document.getElementById('po-loc-select').value || 0);

    if (!supplierId || !locationId) {
      SmartNotifications.show('Supplier and destination location are required', 'warning');
      return;
    }

    const items = [];
    document.querySelectorAll('.po-item-row').forEach(row => {
      const select = row.querySelector('.po-ing-select');
      const qty = parseFloat(row.querySelector('.po-qty-input').value || 0);
      const cost = parseFloat(row.querySelector('.po-cost-input').value || 0);

      if (select && qty > 0) {
        items.push({
          ingredient_id: parseInt(select.value),
          ordered_quantity: qty,
          unit_price: cost
        });
      }
    });

    if (items.length === 0) {
      SmartNotifications.show('At least one item is required for the PO', 'warning');
      return;
    }

    const data = {
      supplier_id: supplierId,
      location_id: locationId,
      notes: document.getElementById('po-notes').value.trim(),
      items: items
    };

    try {
      const res = await SmartAPI.post('api/v1/purchases/index.php', data);
      if (res.success) {
        SmartNotifications.show('Purchase Order created successfully', 'success');
        SmartModal.close('create-po-modal');
        await this.loadPurchaseOrdersList();
      }
    } catch (err) {
      SmartNotifications.show(err.message || 'Failed to create PO', 'danger');
    }
  },

  async approvePO(poId) {
    try {
      const res = await SmartAPI.post('api/v1/purchases/approve.php', { po_id: poId });
      if (res.success) {
        SmartNotifications.show('Purchase Order approved!', 'success');
        await this.loadPurchaseOrdersList();
      }
    } catch (err) {
      SmartNotifications.show(err.message || 'Failed to approve PO', 'danger');
    }
  },

  async openReceiveGoodsModal(poId) {
    try {
      const res = await SmartAPI.get(`api/v1/purchases/index.php?id=${poId}`);
      if (res.success && res.data && res.data.purchase_order) {
        const po = res.data.purchase_order;
        
        document.getElementById('rec-goods-po-id').value = po.id;
        document.getElementById('rec-goods-title').textContent = `Receive Goods — PO #${po.po_number}`;
        document.getElementById('rec-goods-loc-id').value = po.location_id;

        const container = document.getElementById('rec-goods-items-container');
        container.innerHTML = (po.items || []).map(item => {
          const remaining = Math.max(0, item.ordered_quantity - item.received_quantity);
          return `
            <div class="rec-goods-item-row" style="display:flex; gap:10px; align-items:center; margin-bottom:8px; background:var(--surface); padding:8px 12px; border-radius:6px; border:1px solid var(--border);" data-po-item-id="${item.id}" data-ing-id="${item.ingredient_id}">
              <div style="flex:2;">
                <strong>${item.ingredient_name}</strong>
                <div class="text-sm text-muted">Ordered: ${item.ordered_quantity} ${item.unit_of_measure} | Prev Received: ${item.received_quantity}</div>
              </div>
              <input type="number" step="0.01" class="form-control rec-receiving-qty" style="flex:1;" value="${remaining}" placeholder="Receiving Qty">
              <input type="number" step="0.01" class="form-control rec-unit-cost" style="flex:1;" value="${item.unit_price}" placeholder="Unit Price">
            </div>
          `;
        }).join('');

        SmartModal.open('receive-goods-modal');
      }
    } catch (err) {
      SmartNotifications.show('Failed to load PO details: ' + err.message, 'danger');
    }
  },

  async submitReceiveGoods() {
    const poId = parseInt(document.getElementById('rec-goods-po-id').value);
    const locationId = parseInt(document.getElementById('rec-goods-loc-id').value);
    const invoiceNum = document.getElementById('rec-goods-invoice').value.trim();

    const items = [];
    document.querySelectorAll('.rec-goods-item-row').forEach(row => {
      const poItemId = parseInt(row.getAttribute('data-po-item-id'));
      const ingId = parseInt(row.getAttribute('data-ing-id'));
      const qty = parseFloat(row.querySelector('.rec-receiving-qty').value || 0);
      const cost = parseFloat(row.querySelector('.rec-unit-cost').value || 0);

      if (qty > 0) {
        items.push({
          po_item_id: poItemId,
          ingredient_id: ingId,
          received_quantity: qty,
          unit_price: cost
        });
      }
    });

    if (items.length === 0) {
      SmartNotifications.show('Please enter receiving quantity greater than 0', 'warning');
      return;
    }

    try {
      const res = await SmartAPI.post('api/v1/purchases/receive.php', {
        po_id: poId,
        location_id: locationId,
        invoice_number: invoiceNum,
        items: items
      });

      if (res.success) {
        SmartNotifications.show('Goods received into inventory!', 'success');
        SmartModal.close('receive-goods-modal');
        await this.loadPurchaseOrdersList();
        await this.loadStockBalances();
      }
    } catch (err) {
      SmartNotifications.show(err.message || 'Failed to receive goods', 'danger');
    }
  },

  // -------------------------------------------------------------
  // WASTAGE & TRANSFERS & LEDGER
  // -------------------------------------------------------------
  async loadWastageLogs() {
    try {
      const res = await SmartAPI.get('api/v1/inventory/wastage.php');
      if (res.success && res.data) {
        const tbody = document.getElementById('wastage-table-body');
        if (!tbody) return;

        const records = res.data.wastage_records || [];
        if (records.length === 0) {
          tbody.innerHTML = `<tr><td colspan="6" class="text-center text-muted" style="padding:24px;">No wastage records logged.</td></tr>`;
          return;
        }

        tbody.innerHTML = records.map(w => `
          <tr>
            <td><strong>${w.ingredient_name || w.product_name}</strong></td>
            <td>${w.location_name}</td>
            <td>${w.quantity} ${w.unit_of_measure || 'units'}</td>
            <td>৳${parseFloat(w.total_cost || 0).toFixed(2)}</td>
            <td><span class="badge badge-warning">${w.reason}</span></td>
            <td>${w.recorded_at}</td>
          </tr>
        `).join('');
      }
    } catch (err) {
      console.warn('Failed to load wastage logs:', err.message);
    }
  },

  async loadLedgerTransactions() {
    try {
      const res = await SmartAPI.get('api/v1/inventory/transactions.php');
      if (res.success && res.data) {
        const tbody = document.getElementById('ledger-table-body');
        if (!tbody) return;

        const txs = res.data.transactions || [];
        if (txs.length === 0) {
          tbody.innerHTML = `<tr><td colspan="7" class="text-center text-muted" style="padding:24px;">No ledger transactions recorded.</td></tr>`;
          return;
        }

        tbody.innerHTML = txs.map(t => {
          const qty = parseFloat(t.quantity);
          const isPos = qty > 0;
          return `
            <tr>
              <td><span class="font-mono text-sm">${t.created_at}</span></td>
              <td><span class="badge badge-neutral">${t.transaction_type}</span></td>
              <td><strong>${t.ingredient_name}</strong></td>
              <td>${t.location_name}</td>
              <td style="color:${isPos ? 'var(--success)' : 'var(--danger)'}; font-weight:600;">
                ${isPos ? '+' : ''}${qty.toFixed(2)} ${t.unit_of_measure}
              </td>
              <td>${t.balance_after.toFixed(2)}</td>
              <td><small class="text-muted">${t.reference_type} #${t.reference_id || '-'}</small></td>
            </tr>
          `;
        }).join('');
      }
    } catch (err) {
      console.warn('Failed to load inventory transactions ledger:', err.message);
    }
  }
};

document.addEventListener('DOMContentLoaded', () => {
  SmartInventory.init();
});
