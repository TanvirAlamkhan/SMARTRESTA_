/**
 * SMARTRESTA — Frontend Reporting & Dashboard Controller
 * Prompt 12: Admin & Manager Dashboard, Advanced Reporting, Analytics & Drill-Down Engine
 */

var SmartReports = window.SmartReports || {
    currentTab: 'overview',
    currentPreset: 'today',
    dateFrom: '',
    dateTo: '',
    ordersFilter: {
        page: 1,
        perPage: 15,
        status: '',
        payment_status: '',
        search: ''
    },

    init() {
        console.log("Initializing SMARTRESTA Reports & Dashboard Engine...");
        this.bindEvents();
        this.loadCurrentTab();
    },

    bindEvents() {
        // Global Date Preset Selector
        const presetSelect = document.getElementById('report-date-preset');
        if (presetSelect) {
            presetSelect.addEventListener('change', (e) => {
                this.currentPreset = e.target.value;
                const customBox = document.getElementById('report-custom-date-box');
                if (customBox) {
                    customBox.style.display = (this.currentPreset === 'custom') ? 'flex' : 'none';
                }
                if (this.currentPreset !== 'custom') {
                    this.dateFrom = '';
                    this.dateTo = '';
                    this.loadCurrentTab();
                }
            });
        }

        // Apply Custom Date Range Button
        const btnApplyCustom = document.getElementById('btn-apply-custom-date');
        if (btnApplyCustom) {
            btnApplyCustom.addEventListener('click', () => {
                this.dateFrom = document.getElementById('report-date-from').value;
                this.dateTo = document.getElementById('report-date-to').value;
                if (!this.dateFrom || !this.dateTo) {
                    alert("Please select both start and end dates.");
                    return;
                }
                this.loadCurrentTab();
            });
        }

        // Tab Navigation Buttons
        const tabs = document.querySelectorAll('.report-tab-btn');
        tabs.forEach(tab => {
            tab.addEventListener('click', (e) => {
                tabs.forEach(t => t.classList.remove('active'));
                e.target.classList.add('active');
                this.currentTab = e.target.dataset.tab;
                this.loadCurrentTab();
            });
        });

        // Orders Table Filter Inputs
        const orderStatusFilter = document.getElementById('report-order-status-filter');
        if (orderStatusFilter) {
            orderStatusFilter.addEventListener('change', (e) => {
                this.ordersFilter.status = e.target.value;
                this.ordersFilter.page = 1;
                this.loadOrdersReport();
            });
        }

        const orderSearchInput = document.getElementById('report-order-search');
        if (orderSearchInput) {
            let searchTimer;
            orderSearchInput.addEventListener('input', (e) => {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(() => {
                    this.ordersFilter.search = e.target.value;
                    this.ordersFilter.page = 1;
                    this.loadOrdersReport();
                }, 400);
            });
        }
    },

    getQueryString() {
        let params = `preset=${this.currentPreset}`;
        if (this.currentPreset === 'custom' && this.dateFrom && this.dateTo) {
            params += `&date_from=${this.dateFrom}&date_to=${this.dateTo}`;
        }
        return params;
    },

    loadCurrentTab() {
        switch (this.currentTab) {
            case 'overview':
                this.loadOverviewDashboard();
                break;
            case 'sales':
                this.loadSalesReport();
                break;
            case 'orders':
                this.loadOrdersReport();
                break;
            case 'waiters':
                this.loadWaitersReport();
                break;
            case 'tables':
                this.loadTablesReport();
                break;
            case 'products':
                this.loadProductsReport();
                break;
            case 'payments':
                this.loadPaymentsReport();
                break;
            case 'kds':
                this.loadKDSReport();
                break;
            case 'inventory':
                this.loadInventoryReport();
                break;
            default:
                this.loadOverviewDashboard();
                break;
        }
    },

    // 1. Overview Dashboard
    async loadOverviewDashboard() {
        this.showTabContainer('tab-overview-content');
        try {
            const res = await SmartAPI.get(`api/v1/dashboard/overview.php?${this.getQueryString()}`);

            if (res.success && res.data) {
                const kpis = res.data.kpis;
                const alerts = res.data.alerts;

                document.getElementById('kpi-gross-sales').innerText = `৳${parseFloat(kpis.gross_sales).toFixed(2)}`;
                document.getElementById('kpi-net-sales').innerText = `৳${parseFloat(kpis.net_sales).toFixed(2)}`;
                document.getElementById('kpi-total-orders').innerText = kpis.total_orders;
                document.getElementById('kpi-aov').innerText = `৳${parseFloat(kpis.aov).toFixed(2)}`;
                document.getElementById('kpi-paid-amount').innerText = `৳${parseFloat(kpis.paid_amount).toFixed(2)}`;
                document.getElementById('kpi-pending-amount').innerText = `৳${parseFloat(kpis.pending_amount).toFixed(2)}`;

                // Alerts Update
                document.getElementById('alert-low-stock').innerText = `${alerts.low_stock_ingredients} Items`;
                document.getElementById('alert-unpaid-orders').innerText = `${alerts.unpaid_orders} Orders`;
                document.getElementById('alert-delayed-kds').innerText = `${alerts.delayed_kds_tickets} Tickets`;
                document.getElementById('alert-pending-comm').innerText = `${alerts.pending_commissions} Pending`;
            }
        } catch (err) {
            console.error("Failed to load dashboard overview:", err);
        }
    },

    // 2. Sales Report
    async loadSalesReport() {
        this.showTabContainer('tab-sales-content');
        const container = document.getElementById('table-sales-body');
        if (!container) return;

        container.innerHTML = `<tr><td colspan="6" class="text-center">Loading sales analytics...</td></tr>`;
        try {
            const res = await SmartAPI.get(`api/v1/reports/sales.php?${this.getQueryString()}`);

            if (res.success && res.data.sales_data.length > 0) {
                container.innerHTML = res.data.sales_data.map(r => `
                    <tr>
                        <td><strong>${r.period}</strong></td>
                        <td class="text-center">${r.order_count}</td>
                        <td class="text-right">৳${parseFloat(r.gross_sales).toFixed(2)}</td>
                        <td class="text-right text-danger">৳${parseFloat(r.discounts).toFixed(2)}</td>
                        <td class="text-right">৳${parseFloat(r.tax).toFixed(2)}</td>
                        <td class="text-right text-success font-weight-bold">৳${parseFloat(r.net_sales).toFixed(2)}</td>
                    </tr>
                `).join('');
            } else {
                container.innerHTML = `<tr><td colspan="6" class="text-center text-muted">No sales records found for this period.</td></tr>`;
            }
        } catch (err) {
            container.innerHTML = `<tr><td colspan="6" class="text-center text-danger">Failed to load sales report.</td></tr>`;
        }
    },

    // 3. Orders Performance & Server-side Paginated Table
    async loadOrdersReport() {
        this.showTabContainer('tab-orders-content');
        const container = document.getElementById('table-orders-body');
        if (!container) return;

        let url = `api/v1/reports/orders.php?${this.getQueryString()}&page=${this.ordersFilter.page}&per_page=${this.ordersFilter.perPage}`;
        if (this.ordersFilter.status) url += `&order_status=${this.ordersFilter.status}`;
        if (this.ordersFilter.search) url += `&search=${encodeURIComponent(this.ordersFilter.search)}`;

        container.innerHTML = `<tr><td colspan="9" class="text-center">Loading orders data...</td></tr>`;

        try {
            const res = await SmartAPI.get(url);

            if (res.success && res.data.items.length > 0) {
                container.innerHTML = res.data.items.map(o => `
                    <tr>
                        <td><a href="#" onclick="SmartReports.openOrderDrilldown(${o.id}); return false;" class="font-weight-bold text-primary">${o.order_number}</a></td>
                        <td>${o.created_at}</td>
                        <td><span class="badge badge-info">${o.order_type}</span></td>
                        <td>${o.table_number ? 'Table #' + o.table_number : 'Takeaway/Delivery'}</td>
                        <td>${o.waiter_name || o.order_taker_name || 'N/A'}</td>
                        <td class="text-right font-weight-bold">৳${parseFloat(o.total).toFixed(2)}</td>
                        <td><span class="badge badge-${o.payment_status === 'PAID' ? 'success' : 'warning'}">${o.payment_status}</span></td>
                        <td><span class="badge badge-secondary">${o.order_status}</span></td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-primary" onclick="SmartReports.openOrderDrilldown(${o.id})">🔍 Details</button>
                        </td>
                    </tr>
                `).join('');

                this.renderPagination('orders-pagination-controls', res.data.pagination, (p) => {
                    this.ordersFilter.page = p;
                    this.loadOrdersReport();
                });
            } else {
                container.innerHTML = `<tr><td colspan="9" class="text-center text-muted">No orders found matching criteria.</td></tr>`;
            }
        } catch (err) {
            container.innerHTML = `<tr><td colspan="9" class="text-center text-danger">Failed to load orders report.</td></tr>`;
        }
    },

    // 4. Waiter Performance Report
    async loadWaitersReport() {
        this.showTabContainer('tab-waiters-content');
        const container = document.getElementById('table-waiters-body');
        if (!container) return;

        container.innerHTML = `<tr><td colspan="8" class="text-center">Loading staff performance data...</td></tr>`;
        try {
            const res = await SmartAPI.get(`api/v1/reports/waiters.php?${this.getQueryString()}`);

            if (res.success && res.data.items.length > 0) {
                container.innerHTML = res.data.items.map(w => `
                    <tr>
                        <td><strong>${w.waiter_name}</strong><br><small class="text-muted">${w.email}</small></td>
                        <td class="text-center">${w.orders_count}</td>
                        <td class="text-center">${w.tables_served}</td>
                        <td class="text-right font-weight-bold text-success">৳${w.total_sales.toFixed(2)}</td>
                        <td class="text-right">৳${w.average_order_value.toFixed(2)}</td>
                        <td class="text-right text-warning">৳${w.pending_commission.toFixed(2)}</td>
                        <td class="text-right text-primary">৳${w.approved_commission.toFixed(2)}</td>
                        <td class="text-right text-success font-weight-bold">৳${w.paid_commission.toFixed(2)}</td>
                    </tr>
                `).join('');
            } else {
                container.innerHTML = `<tr><td colspan="8" class="text-center text-muted">No waiter performance records found.</td></tr>`;
            }
        } catch (err) {
            container.innerHTML = `<tr><td colspan="8" class="text-center text-danger">Failed to load waiter performance report.</td></tr>`;
        }
    },

    // 5. Table & Session Report
    async loadTablesReport() {
        this.showTabContainer('tab-tables-content');
        const container = document.getElementById('table-tables-body');
        if (!container) return;

        container.innerHTML = `<tr><td colspan="7" class="text-center">Loading table performance...</td></tr>`;
        try {
            const res = await SmartAPI.get(`api/v1/reports/tables.php?${this.getQueryString()}`);

            if (res.success && res.data.items.length > 0) {
                container.innerHTML = res.data.items.map(t => `
                    <tr>
                        <td><strong>Table #${t.table_number}</strong> (${t.floor_name})</td>
                        <td class="text-center">${t.capacity} Seats</td>
                        <td class="text-center">${t.total_sessions}</td>
                        <td class="text-center">${t.total_guests}</td>
                        <td class="text-right font-weight-bold text-success">৳${t.total_sales.toFixed(2)}</td>
                        <td class="text-right">৳${t.avg_spend_per_session.toFixed(2)}</td>
                        <td class="text-center">${t.avg_session_duration_minutes} min</td>
                    </tr>
                `).join('');
            } else {
                container.innerHTML = `<tr><td colspan="7" class="text-center text-muted">No table session records found.</td></tr>`;
            }
        } catch (err) {
            container.innerHTML = `<tr><td colspan="7" class="text-center text-danger">Failed to load table performance report.</td></tr>`;
        }
    },

    // 6. Product Sales & Gross Margin Report
    async loadProductsReport() {
        this.showTabContainer('tab-products-content');
        const container = document.getElementById('table-products-body');
        if (!container) return;

        container.innerHTML = `<tr><td colspan="8" class="text-center">Loading product margin analysis...</td></tr>`;
        try {
            const res = await SmartAPI.get(`api/v1/reports/products.php?${this.getQueryString()}`);

            if (res.success && res.data.items.length > 0) {
                container.innerHTML = res.data.items.map(p => `
                    <tr>
                        <td><strong>${p.product_name}</strong></td>
                        <td>${p.category_name || 'Uncategorized'}</td>
                        <td class="text-center font-weight-bold">${p.total_units_sold}</td>
                        <td class="text-right">৳${p.gross_sales.toFixed(2)}</td>
                        <td class="text-right text-muted">৳${p.recipe_unit_cost.toFixed(2)}</td>
                        <td class="text-right text-danger">৳${p.total_cost.toFixed(2)}</td>
                        <td class="text-right text-success font-weight-bold">৳${p.gross_profit.toFixed(2)}</td>
                        <td class="text-right"><span class="badge badge-${p.margin_percentage > 50 ? 'success' : 'info'}">${p.margin_percentage}%</span></td>
                    </tr>
                `).join('');
            } else {
                container.innerHTML = `<tr><td colspan="8" class="text-center text-muted">No product performance records found.</td></tr>`;
            }
        } catch (err) {
            container.innerHTML = `<tr><td colspan="8" class="text-center text-danger">Failed to load product report.</td></tr>`;
        }
    },

    // 7. Payment Reconciliation Report
    async loadPaymentsReport() {
        this.showTabContainer('tab-payments-content');
        const containerMethods = document.getElementById('table-payment-methods-body');
        const containerUnpaid = document.getElementById('table-unpaid-orders-body');
        if (!containerMethods) return;

        containerMethods.innerHTML = `<tr><td colspan="5" class="text-center">Loading payment totals...</td></tr>`;
        try {
            const res = await SmartAPI.get(`api/v1/reports/payments.php?${this.getQueryString()}`);

            if (res.success && res.data.payment_methods.length > 0) {
                containerMethods.innerHTML = res.data.payment_methods.map(pm => `
                    <tr>
                        <td><strong>${pm.payment_method_name}</strong> (${pm.payment_method_code})</td>
                        <td class="text-center">${pm.transaction_count}</td>
                        <td class="text-right text-success">৳${pm.total_collected.toFixed(2)}</td>
                        <td class="text-right text-danger">৳${pm.total_refunded.toFixed(2)}</td>
                        <td class="text-right font-weight-bold text-primary">৳${pm.net_collected.toFixed(2)}</td>
                    </tr>
                `).join('');
            } else {
                containerMethods.innerHTML = `<tr><td colspan="5" class="text-center text-muted">No payment records found.</td></tr>`;
            }

            if (containerUnpaid && res.data.outstanding_orders) {
                if (res.data.outstanding_orders.length > 0) {
                    containerUnpaid.innerHTML = res.data.outstanding_orders.map(u => `
                        <tr>
                            <td><a href="#" onclick="SmartReports.openOrderDrilldown(${u.order_id}); return false;">${u.order_number}</a></td>
                            <td>${u.table_number ? 'Table #' + u.table_number : 'Takeaway'}</td>
                            <td class="text-right">৳${parseFloat(u.total).toFixed(2)}</td>
                            <td class="text-right text-success">৳${parseFloat(u.paid_amount).toFixed(2)}</td>
                            <td class="text-right text-danger font-weight-bold">৳${parseFloat(u.outstanding_balance).toFixed(2)}</td>
                            <td><span class="badge badge-warning">${u.payment_status}</span></td>
                        </tr>
                    `).join('');
                } else {
                    containerUnpaid.innerHTML = `<tr><td colspan="6" class="text-center text-muted">No unpaid outstanding orders.</td></tr>`;
                }
            }
        } catch (err) {
            containerMethods.innerHTML = `<tr><td colspan="5" class="text-center text-danger">Failed to load payment report.</td></tr>`;
        }
    },

    // 8. KDS Station Report
    async loadKDSReport() {
        this.showTabContainer('tab-kds-content');
        const container = document.getElementById('table-kds-body');
        if (!container) return;

        container.innerHTML = `<tr><td colspan="6" class="text-center">Loading station SLAs...</td></tr>`;
        try {
            const res = await SmartAPI.get(`api/v1/reports/kds.php?${this.getQueryString()}`);

            if (res.success && res.data.stations.length > 0) {
                container.innerHTML = res.data.stations.map(st => `
                    <tr>
                        <td><strong>${st.station_name}</strong> (${st.station_code})</td>
                        <td class="text-center font-weight-bold">${st.total_tickets}</td>
                        <td class="text-center text-success">${st.completed_tickets}</td>
                        <td class="text-center text-danger">${st.cancelled_tickets}</td>
                        <td class="text-center">${st.avg_prep_time_minutes} min</td>
                        <td class="text-center">${st.delayed_tickets_count > 0 ? `<span class="badge badge-danger">${st.delayed_tickets_count} Delayed</span>` : '<span class="badge badge-success">On Track</span>'}</td>
                    </tr>
                `).join('');
            } else {
                container.innerHTML = `<tr><td colspan="6" class="text-center text-muted">No KDS station records found.</td></tr>`;
            }
        } catch (err) {
            container.innerHTML = `<tr><td colspan="6" class="text-center text-danger">Failed to load KDS station report.</td></tr>`;
        }
    },

    // 9. Inventory Report
    async loadInventoryReport() {
        this.showTabContainer('tab-inventory-content');
        const containerWastage = document.getElementById('table-inv-wastage-body');
        const containerCons = document.getElementById('table-inv-cons-body');

        try {
            const res = await SmartAPI.get(`api/v1/reports/inventory.php?${this.getQueryString()}`);

            if (res.success && res.data) {
                document.getElementById('report-inv-val-count').innerText = res.data.stock_summary.total_ingredients;
                document.getElementById('report-inv-val-total').innerText = `৳${res.data.stock_summary.total_stock_value.toFixed(2)}`;

                if (containerWastage) {
                    if (res.data.wastage_summary.length > 0) {
                        containerWastage.innerHTML = res.data.wastage_summary.map(w => `
                            <tr>
                                <td><span class="badge badge-warning">${w.reason}</span></td>
                                <td class="text-center">${w.records_count}</td>
                                <td class="text-right font-weight-bold text-danger">৳${parseFloat(w.total_cost).toFixed(2)}</td>
                            </tr>
                        `).join('');
                    } else {
                        containerWastage.innerHTML = `<tr><td colspan="3" class="text-center text-muted">No wastage logged in this period.</td></tr>`;
                    }
                }

                if (containerCons) {
                    if (res.data.consumption_summary.length > 0) {
                        containerCons.innerHTML = res.data.consumption_summary.map(c => `
                            <tr>
                                <td><strong>${c.ingredient_name}</strong></td>
                                <td class="text-center">${parseFloat(c.total_consumed_qty).toFixed(2)} ${c.base_unit}</td>
                                <td class="text-right font-weight-bold text-primary">৳${parseFloat(c.total_consumed_cost).toFixed(2)}</td>
                            </tr>
                        `).join('');
                    } else {
                        containerCons.innerHTML = `<tr><td colspan="3" class="text-center text-muted">No order consumption recorded.</td></tr>`;
                    }
                }
            }
        } catch (err) {
            console.error("Failed to load inventory report:", err);
        }
    },

    // 10. Order Drill-Down Drawer / Modal
    async openOrderDrilldown(orderId) {
        try {
            const res = await SmartAPI.get(`api/v1/reports/orders_drilldown.php?order_id=${orderId}`);

            if (res.success && res.data) {
                const data = res.data;
                const o = data.order;

                document.getElementById('drilldown-order-number').innerText = o.order_number;
                document.getElementById('drilldown-order-header').innerHTML = `
                    <div class="row text-sm">
                        <div class="col-md-3"><strong>Status:</strong> <span class="badge badge-primary">${o.order_status}</span></div>
                        <div class="col-md-3"><strong>Payment:</strong> <span class="badge badge-success">${o.payment_status}</span></div>
                        <div class="col-md-3"><strong>Table:</strong> ${o.table_number ? 'Table #' + o.table_number : 'Takeaway'}</div>
                        <div class="col-md-3"><strong>Waiter:</strong> ${o.waiter_name || 'N/A'}</div>
                    </div>
                `;

                // Order Items
                document.getElementById('drilldown-items-list').innerHTML = data.items.map(i => `
                    <tr>
                        <td>${i.product_name}</td>
                        <td class="text-center">${i.quantity}</td>
                        <td class="text-right">৳${parseFloat(i.unit_price).toFixed(2)}</td>
                        <td class="text-right font-weight-bold">৳${parseFloat(i.subtotal).toFixed(2)}</td>
                    </tr>
                `).join('');

                // KDS Tickets
                document.getElementById('drilldown-tickets-list').innerHTML = data.tickets.length > 0 ? data.tickets.map(t => `
                    <tr>
                        <td>${t.ticket_number}</td>
                        <td>${t.station_name}</td>
                        <td><span class="badge badge-info">${t.status}</span></td>
                        <td>${t.created_at}</td>
                    </tr>
                `).join('') : '<tr><td colspan="4" class="text-center text-muted">No station tickets</td></tr>';

                // Payments
                document.getElementById('drilldown-payments-list').innerHTML = data.payments.length > 0 ? data.payments.map(p => `
                    <tr>
                        <td>${p.payment_number || 'PAY'}</td>
                        <td>${p.payment_method_name}</td>
                        <td class="text-right font-weight-bold text-success">৳${parseFloat(p.amount).toFixed(2)}</td>
                        <td><span class="badge badge-success">${p.status}</span></td>
                    </tr>
                `).join('') : '<tr><td colspan="4" class="text-center text-muted">No payments recorded</td></tr>';

                // Inventory Consumption Logs
                document.getElementById('drilldown-inventory-list').innerHTML = data.inventory_logs.length > 0 ? data.inventory_logs.map(inv => `
                    <tr>
                        <td>${inv.ingredient_name}</td>
                        <td class="text-center">${Math.abs(parseFloat(inv.quantity)).toFixed(3)} ${inv.base_unit}</td>
                        <td class="text-right">৳${parseFloat(inv.total_cost).toFixed(2)}</td>
                    </tr>
                `).join('') : '<tr><td colspan="3" class="text-center text-muted">No inventory consumption logs</td></tr>';

                if (window.SmartModal) {
                    SmartModal.open('modal-order-drilldown');
                } else {
                    document.getElementById('modal-order-drilldown').style.display = 'block';
                }
            }
        } catch (err) {
            alert("Failed to load order drill-down: " + err.message);
        }
    },

    // CSV Exporter Trigger
    exportReport(type) {
        const isSubdir = window.location.pathname.includes('/admin/') || window.location.pathname.includes('/manager/') ||
                         window.location.pathname.includes('/reception/') || window.location.pathname.includes('/waiter/') ||
                         window.location.pathname.includes('/kitchen/');
        const prefix = isSubdir ? '../' : './';
        const url = `${prefix}api/v1/reports/export.php?type=${type}&format=csv&${this.getQueryString()}`;
        window.location.href = url;
    },

    showTabContainer(id) {
        document.querySelectorAll('.report-tab-pane').forEach(el => el.style.display = 'none');
        const activePane = document.getElementById(id);
        if (activePane) activePane.style.display = 'block';
    },

    renderPagination(containerId, pagination, callback) {
        const el = document.getElementById(containerId);
        if (!el) return;

        if (pagination.totalPages <= 1) {
            el.innerHTML = '';
            return;
        }

        let html = `<ul class="pagination pagination-sm justify-content-end mb-0">`;
        html += `<li class="page-item ${pagination.page === 1 ? 'disabled' : ''}"><a class="page-link" href="#" onclick="event.preventDefault(); callback(${pagination.page - 1})">Prev</a></li>`;

        for (let i = 1; i <= pagination.totalPages; i++) {
            html += `<li class="page-item ${i === pagination.page ? 'active' : ''}"><a class="page-link" href="#" onclick="event.preventDefault(); callback(${i})">${i}</a></li>`;
        }

        html += `<li class="page-item ${pagination.page === pagination.totalPages ? 'disabled' : ''}"><a class="page-link" href="#" onclick="event.preventDefault(); callback(${pagination.page + 1})">Next</a></li>`;
        html += `</ul>`;

        el.innerHTML = html;
        window.callback = callback;
    }
};

document.addEventListener('DOMContentLoaded', () => {
    SmartReports.init();
});

window.SmartReports = SmartReports;

