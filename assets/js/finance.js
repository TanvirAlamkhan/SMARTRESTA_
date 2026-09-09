/**
 * SMARTRESTA - Finance, Shifts, Cash & Day Closing ES6 Module
 * Prompt 13 Compliance
 */

const SmartFinance = {
  currentTab: 'overview',
  activeShift: null,

  init() {
    this.bindEvents();
    this.loadCurrentTab();
    this.checkActiveShift();
  },

  bindEvents() {
    document.querySelectorAll('.finance-tab-btn').forEach(btn => {
      btn.addEventListener('click', (e) => {
        const tab = e.currentTarget.dataset.tab;
        this.switchTab(tab);
      });
    });
  },

  switchTab(tabName) {
    this.currentTab = tabName;
    document.querySelectorAll('.finance-tab-btn').forEach(b => {
      b.classList.toggle('active', b.dataset.tab === tabName);
      b.classList.toggle('btn-primary', b.dataset.tab === tabName);
      b.classList.toggle('btn-secondary', b.dataset.tab !== tabName);
    });

    document.querySelectorAll('.finance-tab-pane').forEach(p => p.style.display = 'none');
    const targetPane = document.getElementById(`tab-fin-${tabName}-content`);
    if (targetPane) targetPane.style.display = 'block';

    this.loadCurrentTab();
  },

  async loadCurrentTab() {
    if (this.currentTab === 'overview') {
      await this.loadFinanceSummary();
    } else if (this.currentTab === 'expenses') {
      await this.loadExpenses();
    } else if (this.currentTab === 'shifts') {
      await this.loadShiftHistory();
    } else if (this.currentTab === 'dayclosing') {
      await this.loadDayClosingReview();
    }
  },

  async loadFinanceSummary() {
    try {
      const res = await SmartAPI.get('api/v1/finance/summary.php');
      if (res.success && res.data) {
        const kpis = res.data.kpis;
        document.getElementById('fin-kpi-gross').textContent = `৳${parseFloat(kpis.gross_sales || 0).toFixed(2)}`;
        document.getElementById('fin-kpi-net').textContent = `৳${parseFloat(kpis.net_sales || 0).toFixed(2)}`;
        document.getElementById('fin-kpi-cash').textContent = `৳${parseFloat(kpis.cash_collected || 0).toFixed(2)}`;
        document.getElementById('fin-kpi-noncash').textContent = `৳${parseFloat(kpis.non_cash_collected || 0).toFixed(2)}`;
        document.getElementById('fin-kpi-exp').textContent = `৳${parseFloat(kpis.total_expenses || 0).toFixed(2)}`;
        document.getElementById('fin-kpi-shifts').textContent = `${kpis.open_shifts_count} Open`;

        const statusBadge = document.getElementById('fin-day-status-badge');
        if (statusBadge) {
          statusBadge.textContent = res.data.day_status;
          statusBadge.className = `badge ${res.data.day_status === 'CLOSED' ? 'badge-danger' : 'badge-success'}`;
        }
      }
    } catch (e) {
      console.error('Failed to load finance summary', e);
    }
  },

  async checkActiveShift() {
    try {
      const res = await SmartAPI.get('api/v1/shifts/active.php');
      const banner = document.getElementById('active-shift-banner');
      if (res.success && res.data) {
        this.activeShift = res.data;
        if (banner) {
          banner.innerHTML = `
            <div style="background:var(--success-light); border:1px solid var(--success); padding:12px 16px; border-radius:8px; display:flex; justify-content:space-between; align-items:center;">
              <div>
                <strong>🟢 ACTIVE SHIFT OPEN</strong> | Drawer: <strong>${this.activeShift.drawer_name}</strong> | Opened: ${this.activeShift.opened_at}
                <br><small>Opening Cash: ৳${parseFloat(this.activeShift.opening_cash).toFixed(2)} | Expected Live Cash: <strong>৳${parseFloat(this.activeShift.expected_cash).toFixed(2)}</strong></small>
              </div>
              <div style="display:flex; gap:8px;">
                <button class="btn btn-sm btn-secondary" onclick="SmartFinance.openCashMovementModal()">💵 Cash In / Out</button>
                <button class="btn btn-sm btn-danger" onclick="SmartFinance.openCloseShiftModal()">🔒 Close Shift</button>
              </div>
            </div>
          `;
        }
      } else {
        this.activeShift = null;
        if (banner) {
          banner.innerHTML = `
            <div style="background:var(--warning-light); border:1px solid var(--warning); padding:12px 16px; border-radius:8px; display:flex; justify-content:space-between; align-items:center;">
              <div>
                <strong>⚠️ NO ACTIVE CASHIER SHIFT</strong> — You currently do not have an open cash drawer shift.
              </div>
              <button class="btn btn-sm btn-primary" onclick="SmartFinance.openOpenShiftModal()">🔑 Open Shift</button>
            </div>
          `;
        }
      }
    } catch (e) {
      console.error('Failed to check active shift', e);
    }
  },

  openOpenShiftModal() {
    document.getElementById('shift-opening-cash').value = '10000.00';
    document.getElementById('shift-open-notes').value = '';
    SmartModal.open('open-shift-modal');
  },

  async submitOpenShift() {
    const openingCash = parseFloat(document.getElementById('shift-opening-cash').value || 0);
    const notes = document.getElementById('shift-open-notes').value.trim();

    if (isNaN(openingCash) || openingCash < 0) {
      SmartNotifications.show('Please enter valid non-negative opening cash amount', 'warning');
      return;
    }

    try {
      const res = await SmartAPI.post('api/v1/shifts/open.php', { opening_cash: openingCash, notes });
      if (res.success) {
        SmartNotifications.show('Cashier shift opened successfully!', 'success');
        SmartModal.close('open-shift-modal');
        await this.checkActiveShift();
        await this.loadFinanceSummary();
      }
    } catch (e) {
      SmartNotifications.show(e.message || 'Failed to open shift', 'danger');
    }
  },

  openCloseShiftModal() {
    if (!this.activeShift) {
      SmartNotifications.show('No active shift to close', 'warning');
      return;
    }
    document.getElementById('close-shift-expected-display').textContent = `৳${parseFloat(this.activeShift.expected_cash).toFixed(2)}`;
    document.getElementById('close-shift-actual').value = parseFloat(this.activeShift.expected_cash).toFixed(2);
    document.getElementById('close-shift-notes').value = '';
    this.calculateDenominations();
    SmartModal.open('close-shift-modal');
  },

  calculateDenominations() {
    const denomInputs = document.querySelectorAll('.denom-qty');
    let total = 0;
    denomInputs.forEach(input => {
      const val = parseInt(input.value || 0);
      const multiplier = parseFloat(input.dataset.value);
      total += (val * multiplier);
    });
    if (total > 0) {
      document.getElementById('close-shift-actual').value = total.toFixed(2);
    }
    const expected = parseFloat(this.activeShift ? this.activeShift.expected_cash : 0);
    const actual = parseFloat(document.getElementById('close-shift-actual').value || 0);
    const diff = actual - expected;
    const diffEl = document.getElementById('close-shift-diff-display');

    if (diffEl) {
      diffEl.textContent = `৳${diff.toFixed(2)}`;
      diffEl.style.color = diff === 0 ? 'var(--success)' : (diff < 0 ? 'var(--danger)' : 'var(--info)');
    }
  },

  async submitCloseShift() {
    if (!this.activeShift) return;

    const actualCash = parseFloat(document.getElementById('close-shift-actual').value || 0);
    const notes = document.getElementById('close-shift-notes').value.trim();

    const denoms = {};
    document.querySelectorAll('.denom-qty').forEach(input => {
      denoms[input.dataset.value] = parseInt(input.value || 0);
    });

    try {
      const res = await SmartAPI.post('api/v1/shifts/close.php', {
        shift_id: this.activeShift.id,
        actual_cash: actualCash,
        denominations: denoms,
        notes
      });
      if (res.success) {
        SmartNotifications.show(`Shift closed! Cash Difference: ৳${parseFloat(res.data.difference).toFixed(2)}`, 'success');
        SmartModal.close('close-shift-modal');
        await this.checkActiveShift();
        await this.loadFinanceSummary();
      }
    } catch (e) {
      SmartNotifications.show(e.message || 'Failed to close shift', 'danger');
    }
  },

  openCashMovementModal() {
    document.getElementById('cash-move-amount').value = '500.00';
    document.getElementById('cash-move-reason').value = '';
    SmartModal.open('cash-movement-modal');
  },

  async submitCashMovement() {
    const type = document.getElementById('cash-move-type').value;
    const amount = parseFloat(document.getElementById('cash-move-amount').value || 0);
    const reason = document.getElementById('cash-move-reason').value.trim();

    if (amount <= 0 || !reason) {
      SmartNotifications.show('Please enter amount and reason', 'warning');
      return;
    }

    try {
      const res = await SmartAPI.post('api/v1/cash/in_out.php', { movement_type: type, amount, reason });
      if (res.success) {
        SmartNotifications.show(`Cash ${type} logged!`, 'success');
        SmartModal.close('cash-movement-modal');
        await this.checkActiveShift();
        await this.loadFinanceSummary();
      }
    } catch (e) {
      SmartNotifications.show(e.message || 'Failed to log cash movement', 'danger');
    }
  },

  async loadExpenses() {
    const tbody = document.getElementById('table-expenses-body');
    try {
      const res = await SmartAPI.get('api/v1/expenses/index.php');
      if (res.success && res.data.items && res.data.items.length > 0) {
        tbody.innerHTML = res.data.items.map(e => `
          <tr>
            <td>#EXP-${e.id}</td>
            <td><strong>${e.title}</strong>${e.description ? `<br><small class="text-muted">${e.description}</small>` : ''}</td>
            <td><span class="badge badge-info">${e.category_name || 'Operating'}</span></td>
            <td class="price-tag">৳${parseFloat(e.amount).toFixed(2)}</td>
            <td>${e.payment_method_name || 'Cash'}</td>
            <td><span class="badge ${e.status === 'PAID' ? 'badge-success' : 'badge-warning'}">${e.status}</span></td>
            <td>${e.recorded_by_name || 'Staff'}</td>
            <td>${e.expense_date || e.created_at.substring(0, 10)}</td>
          </tr>
        `).join('');
      } else {
        tbody.innerHTML = `<tr><td colspan="8" style="text-align:center; padding:40px; color:var(--text-muted);">No operating expenses recorded.</td></tr>`;
      }
    } catch (e) {
      tbody.innerHTML = `<tr><td colspan="8" style="text-align:center; padding:40px; color:var(--danger);">Failed to load expenses.</td></tr>`;
    }
  },

  openCreateExpenseModal() {
    document.getElementById('exp-title').value = '';
    document.getElementById('exp-amount').value = '';
    document.getElementById('exp-desc').value = '';
    SmartModal.open('create-expense-modal');
  },

  async submitCreateExpense() {
    const title = document.getElementById('exp-title').value.trim();
    const amount = parseFloat(document.getElementById('exp-amount').value || 0);
    const category_id = document.getElementById('exp-category').value;
    const payment_method_id = document.getElementById('exp-method').value;
    const description = document.getElementById('exp-desc').value.trim();

    if (!title || amount <= 0) {
      SmartNotifications.show('Please fill in title and valid expense amount', 'warning');
      return;
    }

    try {
      const res = await SmartAPI.post('api/v1/expenses/index.php', { title, amount, category_id, payment_method_id, description });
      if (res.success) {
        SmartNotifications.show('Operating expense recorded!', 'success');
        SmartModal.close('create-expense-modal');
        await this.loadExpenses();
        await this.loadFinanceSummary();
      }
    } catch (e) {
      SmartNotifications.show(e.message || 'Failed to create expense', 'danger');
    }
  },

  async loadDayClosingReview() {
    const box = document.getElementById('dayclosing-review-box');
    try {
      const res = await SmartAPI.get('api/v1/day_closing/review.php');
      if (res.success && res.data) {
        const d = res.data;
        const kpis = d.financial_summary;
        box.innerHTML = `
          <div style="background:var(--surface); border:1px solid var(--border); padding:20px; border-radius:8px; margin-bottom:20px;">
            <h4>📅 Business Day Closing Audit — ${d.business_date}</h4>
            <div style="margin-top:14px; display:grid; grid-template-columns:repeat(auto-fit, minmax(180px,1fr)); gap:12px;">
              <div><span class="text-sm text-muted">Net Sales:</span> <div class="font-mono font-bold" style="font-size:1.2rem;">৳${parseFloat(kpis.net_sales).toFixed(2)}</div></div>
              <div><span class="text-sm text-muted">Cash Collected:</span> <div class="font-mono font-bold" style="font-size:1.2rem; color:var(--success);">৳${parseFloat(kpis.cash_collected).toFixed(2)}</div></div>
              <div><span class="text-sm text-muted">Non-Cash Collected:</span> <div class="font-mono font-bold" style="font-size:1.2rem; color:var(--info);">৳${parseFloat(kpis.non_cash_collected).toFixed(2)}</div></div>
              <div><span class="text-sm text-muted">Operating Expenses:</span> <div class="font-mono font-bold" style="font-size:1.2rem; color:var(--danger);">৳${parseFloat(kpis.total_expenses).toFixed(2)}</div></div>
            </div>
            
            <div style="margin-top:20px; padding-top:16px; border-top:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
              <div>
                ${d.can_close ? 
                  `<span class="badge badge-success">✓ PRE-CHECK PASSED — Ready to Close Day</span>` : 
                  `<span class="badge badge-danger">⚠️ BLOCKED: ${d.blocking_reasons.open_shifts} Active Shifts Still Open</span>`}
              </div>
              <button class="btn btn-danger" ${!d.can_close ? 'disabled' : ''} onclick="SmartFinance.executeCloseBusinessDay('${d.business_date}')">🔒 Finalize & Freeze Business Day</button>
            </div>
          </div>
        `;
      }
    } catch (e) {
      box.innerHTML = `<div style="text-align:center; padding:30px; color:var(--danger);">Failed to load day closing audit review.</div>`;
    }
  },

  async executeCloseBusinessDay(businessDate) {
    if (!confirm(`Are you sure you want to CLOSE business day ${businessDate}? This will generate an immutable financial snapshot.`)) return;

    try {
      const res = await SmartAPI.post('api/v1/day_closing/close.php', { business_date: businessDate });
      if (res.success) {
        SmartNotifications.show(`Business day ${businessDate} successfully CLOSED! Snapshot frozen.`, 'success');
        await this.loadDayClosingReview();
        await this.loadFinanceSummary();
      }
    } catch (e) {
      SmartNotifications.show(e.message || 'Failed to close business day', 'danger');
    }
  }
};

document.addEventListener('DOMContentLoaded', () => {
  if (document.getElementById('reports-view') || document.getElementById('finance-view')) {
    SmartFinance.init();
  }
});
