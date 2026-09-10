/**
 * SMARTRESTA - Waiters & Commission Engine Frontend Controller
 * Prompt 09 Compliance: Waiter Performance, Rules Engine, Approval Workflow & Payout Settlements
 */

var SmartCommissions = window.SmartCommissions || {
    selectedCommissionIds: new Set(),

    /**
     * Load Waiter Performance Matrix into Manager View
     */
    async loadWaiterMatrix() {
        const tbody = document.getElementById('waiter-matrix-tbody');
        if (!tbody) return;

        try {
            const res = await SmartAPI.get('api/v1/waiters/get_matrix.php');

            if (res.data && res.data.length > 0) {
                let totalSales = 0, totalComm = 0;

                tbody.innerHTML = res.data.map(w => {
                    totalSales += parseFloat(w.total_sales || 0);
                    totalComm += parseFloat(w.total_commission || 0);

                    return `
                        <tr>
                            <td>
                                <strong>${w.waiter_name}</strong>
                                <br><small class="font-mono text-muted">${w.employee_code || 'WTR-000'}</small>
                            </td>
                            <td>${w.total_orders || 0}</td>
                            <td>${w.tables_served || 0}</td>
                            <td class="price-tag">৳${parseFloat(w.total_sales || 0).toLocaleString(undefined, {minimumFractionDigits:2})}</td>
                            <td><span class="badge badge-success">${w.paid_orders || 0} Paid</span></td>
                            <td><span class="badge badge-warning">${w.pending_orders || 0} Pending</span></td>
                            <td>
                                <strong>৳${parseFloat(w.total_commission || 0).toFixed(2)}</strong>
                                ${w.pending_commission > 0 ? `<br><small style="color:var(--warning);">Pending: ৳${parseFloat(w.pending_commission).toFixed(2)}</small>` : ''}
                                ${w.approved_commission > 0 ? `<br><small style="color:var(--success);">Approved: ৳${parseFloat(w.approved_commission).toFixed(2)}</small>` : ''}
                            </td>
                            <td><span class="badge ${w.status === 'ACTIVE' ? 'badge-success' : 'badge-danger'}">${w.status}</span></td>
                            <td>
                                <button class="btn btn-secondary btn-sm" onclick="SmartCommissions.openWaiterPerformanceModal(${w.waiter_id})">Drill-Down</button>
                                ${w.approved_commission > 0 ? `
                                    <button class="btn btn-primary btn-sm" onclick="SmartCommissions.openPayoutModal(${w.waiter_id}, '${w.waiter_name}', ${w.approved_commission})">Pay Commission</button>
                                ` : ''}
                            </td>
                        </tr>
                    `;
                }).join('');

                const salesKpi = document.getElementById('kpi-sales');
                const commKpi = document.getElementById('kpi-commission');
                if (salesKpi) salesKpi.textContent = `৳${totalSales.toLocaleString(undefined, {minimumFractionDigits:2})}`;
                if (commKpi) commKpi.textContent = `৳${totalComm.toLocaleString(undefined, {minimumFractionDigits:2})}`;

            } else {
                tbody.innerHTML = `<tr><td colspan="9" style="text-align:center; padding: 40px; color: var(--text-muted);">No waiter performance records available in database.</td></tr>`;
            }
        } catch (err) {
            tbody.innerHTML = `<tr><td colspan="9" style="text-align:center; padding: 40px; color: var(--danger);">Unable to connect to database for waiter performance.</td></tr>`;
        }
    },

    /**
     * Load Commission Rules Catalog
     */
    async loadCommissionRules() {
        const tbody = document.getElementById('rules-table-tbody');
        if (!tbody) return;

        try {
            const res = await SmartAPI.get('api/v1/commission_rules/index.php');
            if (res.data && res.data.length > 0) {
                tbody.innerHTML = res.data.map(r => `
                    <tr>
                        <td><strong>${r.name}</strong><br><small class="font-mono text-muted">${r.code || 'N/A'}</small></td>
                        <td><span class="badge badge-info">${r.calculation_base}</span></td>
                        <td>${parseFloat(r.rate).toFixed(2)}%</td>
                        <td class="price-tag">৳${parseFloat(r.fixed_amount || 0).toFixed(2)}</td>
                        <td>৳${parseFloat(r.minimum_sales || 0).toFixed(2)}</td>
                        <td><span class="badge badge-neutral">${r.commission_eligible}</span></td>
                        <td><span class="badge ${r.is_active ? 'badge-success' : 'badge-danger'}">${r.is_active ? 'ACTIVE' : 'INACTIVE'}</span></td>
                    </tr>
                `).join('');
            } else {
                tbody.innerHTML = `<tr><td colspan="7" style="text-align:center; padding: 40px; color: var(--text-muted);">No commission rules configured.</td></tr>`;
            }
        } catch (err) {
            tbody.innerHTML = `<tr><td colspan="7" style="text-align:center; padding: 40px; color: var(--danger);">Failed to load commission rules.</td></tr>`;
        }
    },

    /**
     * Submit Create Commission Rule
     */
    async submitCreateCommissionRule() {
        const name = document.getElementById('rule-name').value.trim();
        const calculation_base = document.getElementById('rule-base').value;
        const rate = document.getElementById('rule-rate').value;
        const fixed_amount = document.getElementById('rule-fixed').value;
        const minimum_sales = document.getElementById('rule-minsales').value;

        if (!name) {
            SmartNotifications.show('Rule name is required', 'warning');
            return;
        }

        try {
            const res = await SmartAPI.post('api/v1/commission_rules/index.php', {
                name, calculation_base, rate, fixed_amount, minimum_sales
            });
            if (res.success) {
                SmartNotifications.show('Commission rule created successfully!', 'success');
                SmartModal.close('create-commission-rule-modal');
                this.loadCommissionRules();
            }
        } catch (err) {
            SmartNotifications.show(err.message || 'Failed to create commission rule', 'danger');
        }
    },

    /**
     * Load Commission Transactions for Review
     */
    async loadCommissionsReview() {
        const tbody = document.getElementById('commissions-review-tbody');
        if (!tbody) return;

        this.selectedCommissionIds.clear();

        try {
            const statusFilter = document.getElementById('comm-status-filter') ? document.getElementById('comm-status-filter').value : '';
            let url = 'api/v1/commissions/index.php';
            if (statusFilter) url += `?status=${statusFilter}`;

            const res = await SmartAPI.get(url);

            if (res.data && res.data.length > 0) {
                tbody.innerHTML = res.data.map(c => {
                    let badgeClass = 'badge-warning';
                    if (c.status === 'APPROVED') badgeClass = 'badge-info';
                    if (c.status === 'PAID') badgeClass = 'badge-success';
                    if (c.status === 'REJECTED' || c.status === 'REVERSED') badgeClass = 'badge-danger';

                    return `
                        <tr>
                            <td>
                                ${c.status === 'PENDING' ? `<input type="checkbox" onchange="SmartCommissions.toggleSelectCommission(${c.id}, this.checked)">` : ''}
                            </td>
                            <td><span class="font-mono">#${c.id}</span></td>
                            <td>
                                <strong>${c.waiter_name}</strong>
                                <br><small class="text-muted font-mono">${c.employee_code || 'WTR'}</small>
                            </td>
                            <td><span class="font-mono">${c.order_number}</span></td>
                            <td>৳${parseFloat(c.base_amount).toFixed(2)}</td>
                            <td>${c.rule_name || '5% Standard'}</td>
                            <td class="price-tag">৳${parseFloat(c.commission_amount).toFixed(2)}</td>
                            <td><span class="badge ${badgeClass}">${c.status}</span></td>
                            <td><small class="text-muted">${c.created_at}</small></td>
                        </tr>
                    `;
                }).join('');
            } else {
                tbody.innerHTML = `<tr><td colspan="9" style="text-align:center; padding: 40px; color: var(--text-muted);">No commission records available.</td></tr>`;
            }
        } catch (err) {
            tbody.innerHTML = `<tr><td colspan="9" style="text-align:center; padding: 40px; color: var(--danger);">Failed to load commission review list.</td></tr>`;
        }
    },

    toggleSelectCommission(id, isChecked) {
        if (isChecked) this.selectedCommissionIds.add(id);
        else this.selectedCommissionIds.delete(id);
    },

    async approveSelectedCommissions() {
        const ids = Array.from(this.selectedCommissionIds);
        if (ids.length === 0) {
            SmartNotifications.show('Please select at least one pending commission to approve', 'warning');
            return;
        }

        try {
            const res = await SmartAPI.post('api/v1/commissions/approve.php', { commission_ids: ids });
            if (res.success) {
                SmartNotifications.show(`Successfully approved ${res.data.approved_count} commission transactions!`, 'success');
                this.loadCommissionsReview();
                this.loadWaiterMatrix();
            }
        } catch (err) {
            SmartNotifications.show(err.message || 'Failed to approve commissions', 'danger');
        }
    },

    async rejectSelectedCommissions() {
        const ids = Array.from(this.selectedCommissionIds);
        if (ids.length === 0) {
            SmartNotifications.show('Please select at least one pending commission to reject', 'warning');
            return;
        }

        const reason = prompt('Please enter rejection reason:');
        if (!reason) return;

        try {
            const res = await SmartAPI.post('api/v1/commissions/reject.php', { commission_ids: ids, reason });
            if (res.success) {
                SmartNotifications.show(`Successfully rejected ${res.data.rejected_count} commission transactions!`, 'info');
                this.loadCommissionsReview();
                this.loadWaiterMatrix();
            }
        } catch (err) {
            SmartNotifications.show(err.message || 'Failed to reject commissions', 'danger');
        }
    },

    /**
     * Open Payout Settlement Modal
     */
    openPayoutModal(waiterId, waiterName, approvedBalance) {
        document.getElementById('payout-waiter-id').value = waiterId;
        document.getElementById('payout-modal-title').textContent = `Commission Payout Settlement — ${waiterName}`;
        document.getElementById('payout-amount-display').textContent = `৳${parseFloat(approvedBalance).toFixed(2)}`;
        SmartModal.open('commission-payout-modal');
    },

    async submitProcessPayout() {
        const waiter_id = document.getElementById('payout-waiter-id').value;
        const payment_method = document.getElementById('payout-method').value;
        const reference_number = document.getElementById('payout-ref').value.trim();
        const notes = document.getElementById('payout-notes').value.trim();

        try {
            const res = await SmartAPI.post('api/v1/commissions/payout.php', {
                waiter_id, payment_method, reference_number, notes
            });
            if (res.success) {
                SmartNotifications.show(`Payout ${res.data.payout_number} processed! Total ৳${parseFloat(res.data.amount_paid).toFixed(2)} paid.`, 'success');
                SmartModal.close('commission-payout-modal');
                this.loadWaiterMatrix();
                this.loadPayoutHistory();
            }
        } catch (err) {
            SmartNotifications.show(err.message || 'Failed to process payout', 'danger');
        }
    },

    /**
     * Load Processed Payouts History
     */
    async loadPayoutHistory() {
        const tbody = document.getElementById('payouts-table-tbody');
        if (!tbody) return;

        try {
            const res = await SmartAPI.get('api/v1/commissions/payout.php');
            if (res.data && res.data.length > 0) {
                tbody.innerHTML = res.data.map(p => `
                    <tr>
                        <td><span class="font-mono">${p.payout_number}</span></td>
                        <td><strong>${p.waiter_name}</strong><br><small class="text-muted font-mono">${p.employee_code || 'WTR'}</small></td>
                        <td class="price-tag">৳${parseFloat(p.amount).toFixed(2)}</td>
                        <td><span class="badge badge-info">${p.payment_method}</span></td>
                        <td><span class="font-mono">${p.reference_number || 'N/A'}</span></td>
                        <td>${p.processed_by_name || 'Manager'}</td>
                        <td><span class="badge badge-success">${p.status}</span></td>
                        <td><small class="text-muted">${p.processed_at}</small></td>
                    </tr>
                `).join('');
            } else {
                tbody.innerHTML = `<tr><td colspan="8" style="text-align:center; padding: 40px; color: var(--text-muted);">No commission payout records available.</td></tr>`;
            }
        } catch (err) {
            tbody.innerHTML = `<tr><td colspan="8" style="text-align:center; padding: 40px; color: var(--danger);">Failed to load payout history.</td></tr>`;
        }
    },

    /**
     * Open Waiter Drill-Down Performance Modal
     */
    async openWaiterPerformanceModal(waiterId) {
        try {
            const res = await SmartAPI.get(`api/v1/waiters/performance.php?waiter_id=${waiterId}`);
            if (!res.success || !res.data) {
                SmartNotifications.show('Failed to load waiter details', 'danger');
                return;
            }

            const w = res.data.waiter;
            const orders = res.data.orders || [];
            const comms = res.data.commissions || [];

            document.getElementById('waiter-drilldown-title').textContent = `${w.name} (${w.employee_code})`;
            
            const body = document.getElementById('waiter-drilldown-body');
            body.innerHTML = `
                <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:12px; margin-bottom:16px;">
                    <div style="background:var(--surface); border:1px solid var(--border); padding:12px; border-radius:6px;">
                        <span class="text-sm text-muted">Status</span>
                        <div><strong style="color:var(--success);">${w.waiter_status}</strong></div>
                    </div>
                    <div style="background:var(--surface); border:1px solid var(--border); padding:12px; border-radius:6px;">
                        <span class="text-sm text-muted">Commission Enabled</span>
                        <div><strong>${w.commission_enabled ? 'Yes' : 'No'}</strong></div>
                    </div>
                    <div style="background:var(--surface); border:1px solid var(--border); padding:12px; border-radius:6px;">
                        <span class="text-sm text-muted">Role Privilege</span>
                        <div><strong>${w.role_name}</strong></div>
                    </div>
                </div>

                <h4 style="margin-bottom:8px;">Recent Handled Orders (${orders.length})</h4>
                <div class="table-container" style="max-height:200px; overflow-y:auto; margin-bottom:16px;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ORDER #</th>
                                <th>TABLE</th>
                                <th>TOTAL</th>
                                <th>PAYMENT</th>
                                <th>STATUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${orders.length > 0 ? orders.map(o => `
                                <tr>
                                    <td class="font-mono">${o.order_number}</td>
                                    <td>Table ${o.table_number || 'N/A'}</td>
                                    <td class="price-tag">৳${parseFloat(o.total).toFixed(2)}</td>
                                    <td><span class="badge ${o.payment_status === 'PAID' ? 'badge-success' : 'badge-warning'}">${o.payment_status}</span></td>
                                    <td><span class="badge badge-info">${o.order_status}</span></td>
                                </tr>
                            `).join('') : `<tr><td colspan="5" style="text-align:center; color:var(--text-muted);">No order history</td></tr>`}
                        </tbody>
                    </table>
                </div>

                <h4 style="margin-bottom:8px;">Commission History (${comms.length})</h4>
                <div class="table-container" style="max-height:200px; overflow-y:auto;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>COMMISSION #</th>
                                <th>ORDER #</th>
                                <th>BASE</th>
                                <th>COMMISSION</th>
                                <th>STATUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${comms.length > 0 ? comms.map(c => `
                                <tr>
                                    <td class="font-mono">#${c.id}</td>
                                    <td class="font-mono">${c.order_number}</td>
                                    <td>৳${parseFloat(c.base_amount).toFixed(2)}</td>
                                    <td class="price-tag">৳${parseFloat(c.commission_amount).toFixed(2)}</td>
                                    <td><span class="badge badge-info">${c.status}</span></td>
                                </tr>
                            `).join('') : `<tr><td colspan="5" style="text-align:center; color:var(--text-muted);">No commission history</td></tr>`}
                        </tbody>
                    </table>
                </div>
            `;

            SmartModal.open('waiter-drilldown-modal');
        } catch (err) {
            SmartNotifications.show(err.message || 'Failed to load waiter drill-down', 'danger');
        }
    }
};

window.SmartCommissions = SmartCommissions;
