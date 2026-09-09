<!-- VIEW: COMMISSION TRANSACTION REVIEW & APPROVALS -->
<section id="commissions-review" class="role-view" style="display:none;">
  <div class="card">
    <div class="card-header">
      <div>
        <h3>Commission Transaction Review & Approvals</h3>
        <p class="text-sm">Manager review, approval, rejection, and historical audit for waiter commissions</p>
      </div>
      <div style="display:flex; gap:8px;">
        <select id="comm-status-filter" class="form-control" style="max-width:160px;" onchange="SmartCommissions.loadCommissionsReview()">
          <option value="">All Statuses</option>
          <option value="PENDING" selected>PENDING Only</option>
          <option value="APPROVED">APPROVED Only</option>
          <option value="PAID">PAID Only</option>
          <option value="REJECTED">REJECTED Only</option>
        </select>
        <button class="btn btn-success btn-sm" onclick="SmartCommissions.approveSelectedCommissions()">Approve Selected</button>
        <button class="btn btn-danger btn-sm" onclick="SmartCommissions.rejectSelectedCommissions()">Reject Selected</button>
      </div>
    </div>

    <div class="table-container">
      <table class="data-table">
        <thead>
          <tr>
            <th><input type="checkbox" disabled></th>
            <th>COMMISSION ID</th>
            <th>WAITER</th>
            <th>ORDER NUMBER</th>
            <th>BASE AMOUNT</th>
            <th>APPLIED RULE</th>
            <th>COMMISSION</th>
            <th>STATUS</th>
            <th>DATE</th>
          </tr>
        </thead>
        <tbody id="commissions-review-tbody">
          <tr><td colspan="9" style="text-align:center; padding: 40px; color: var(--text-muted);">Loading commission review list...</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</section>
