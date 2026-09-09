<!-- VIEW: INVENTORY & STOCK CONTROL -->
<section id="inventory" class="role-view" style="display:none;">
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
