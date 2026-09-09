<!-- VIEW 5: USER & ROLE MANAGEMENT -->
<section id="users" class="role-view" style="display:none;">
  <div class="card">
    <div class="card-header">
      <div>
        <h3>User Accounts & Role Permissions Matrix</h3>
        <p class="text-sm">Manage restaurant staff accounts, Bcrypt authentication, and RBAC permissions</p>
      </div>
      <button class="btn btn-primary btn-sm" onclick="SmartModal.open('create-user-modal')">+ Create Staff Account</button>
    </div>

    <div class="table-container">
      <table class="data-table">
        <thead>
          <tr>
            <th>USER NAME</th>
            <th>EMAIL</th>
            <th>PHONE</th>
            <th>ROLE</th>
            <th>LAST LOGIN</th>
            <th>ACCOUNT STATUS</th>
            <th>ACTION</th>
          </tr>
        </thead>
        <tbody id="users-table-tbody">
          <tr><td colspan="7" style="text-align:center; padding: 40px; color: var(--text-muted);">Loading user accounts...</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</section>
