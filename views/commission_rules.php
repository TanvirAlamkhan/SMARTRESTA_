<!-- VIEW: COMMISSION RULES CONFIGURATION -->
<section id="commission-rules-view" class="role-view" style="display:none;">
  <div class="card">
    <div class="card-header">
      <div>
        <h3>Configurable Commission Rules Engine</h3>
        <p class="text-sm">Manage waiter commission bases (Percentage of Net Sales, Per Order, Per Item, Fixed Amount)</p>
      </div>
      <button class="btn btn-primary btn-sm" onclick="SmartModal.open('create-commission-rule-modal')">+ Create Commission Rule</button>
    </div>

    <div class="table-container">
      <table class="data-table">
        <thead>
          <tr>
            <th>RULE NAME</th>
            <th>CALCULATION BASE</th>
            <th>RATE (%)</th>
            <th>FIXED AMOUNT</th>
            <th>MINIMUM SALES</th>
            <th>ELIGIBILITY</th>
            <th>STATUS</th>
          </tr>
        </thead>
        <tbody id="rules-table-tbody">
          <tr><td colspan="7" style="text-align:center; padding: 40px; color: var(--text-muted);">Loading commission rules...</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</section>
