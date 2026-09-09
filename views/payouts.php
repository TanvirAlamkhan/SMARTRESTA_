<!-- VIEW: COMMISSION PAYOUT SETTLEMENTS -->
<section id="payouts-view" class="role-view" style="display:none;">
  <div class="card">
    <div class="card-header">
      <div>
        <h3>Commission Payout Settlements History</h3>
        <p class="text-sm">Manager processed waiter payroll payout settlements with itemized transaction links</p>
      </div>
      <button class="btn btn-secondary btn-sm" onclick="SmartCommissions.loadPayoutHistory()">Refresh Payouts</button>
    </div>

    <div class="table-container">
      <table class="data-table">
        <thead>
          <tr>
            <th>PAYOUT NUMBER</th>
            <th>WAITER</th>
            <th>AMOUNT PAID</th>
            <th>PAYMENT METHOD</th>
            <th>REFERENCE</th>
            <th>PROCESSED BY</th>
            <th>STATUS</th>
            <th>DATE</th>
          </tr>
        </thead>
        <tbody id="payouts-table-tbody">
          <tr><td colspan="8" style="text-align:center; padding: 40px; color: var(--text-muted);">Loading payout settlement history...</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</section>
