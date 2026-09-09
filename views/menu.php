<!-- VIEW: RESTAURANT MENU & PRODUCT CATALOG -->
<section id="menu" class="role-view" style="display:none;">
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
