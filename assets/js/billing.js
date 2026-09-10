/**
 * SMARTRESTA Frontend Billing, Settlement & Receipts Controller
 * Prompt 08 & Prompt 16
 * Includes Dynamic bKash Payment QR Generator, Merchant Number & Amount Auto-Fill, and 1-Click Copy Helpers.
 */

var SmartBilling = window.SmartBilling || {
  paymentMethods: [],
  currentBill: null,

  async init() {
    await this.loadPaymentMethods();
  },

  async loadPaymentMethods() {
    try {
      const res = await SmartAPI.get('api/v1/payment_methods/index.php');
      if (res.success && res.data) {
        this.paymentMethods = res.data;
      }
    } catch (err) {
      console.warn('Failed to load payment methods:', err.message);
    }
  },

  async openBillingModal(orderId) {
    try {
      if (this.paymentMethods.length === 0) await this.loadPaymentMethods();

      const res = await SmartAPI.get(`api/v1/billing/index.php?order_id=${orderId}`);
      if (!res.success || !res.data) {
        SmartNotifications.show('Failed to load bill calculation', 'danger');
        return;
      }

      const bill = res.data;
      this.currentBill = bill;

      document.getElementById('billing-modal-title').textContent = `Settlement & Billing — Order #${bill.order_number}`;
      document.getElementById('billing-modal-subtitle').textContent = `Table: ${bill.table_number || 'N/A'} | Waiter: ${bill.waiter_name} | Status: ${bill.payment_status}`;

      const body = document.getElementById('billing-modal-body');

      let itemsHtml = `
        <div style="margin-bottom:16px;">
          <h4 style="margin-bottom:8px;">Order Items Breakdown</h4>
          <table class="data-table">
            <thead>
              <tr>
                <th>ITEM</th>
                <th>QTY</th>
                <th>UNIT PRICE</th>
                <th>TOTAL</th>
              </tr>
            </thead>
            <tbody>
              ${(bill.items || []).map(i => `
                <tr>
                  <td><strong>${i.item_name}</strong> ${i.variant_name ? `<small>(${i.variant_name})</small>` : ''}</td>
                  <td>${i.quantity}</td>
                  <td>৳${parseFloat(i.unit_price).toFixed(2)}</td>
                  <td class="price-tag">৳${parseFloat(i.subtotal).toFixed(2)}</td>
                </tr>
              `).join('')}
            </tbody>
          </table>
        </div>
      `;

      let summaryHtml = `
        <div style="background:var(--surface); border:1px solid var(--border); padding:16px; border-radius:8px; margin-bottom:20px;">
          <div style="display:flex; justify-content:space-between; margin-bottom:6px;"><span>Item Subtotal</span><span>৳${bill.subtotal.toFixed(2)}</span></div>
          <div style="display:flex; justify-content:space-between; margin-bottom:6px; ${bill.discount > 0 ? 'color:#10B981; font-weight:600;' : ''}">
            <span>Discount ${bill.coupon_code ? `(Coupon: <strong>${bill.coupon_code}</strong>)` : ''}</span>
            <span>- ৳${bill.discount.toFixed(2)}</span>
          </div>
          <div style="display:flex; justify-content:space-between; margin-bottom:6px;"><span>VAT (5%)</span><span>+ ৳${bill.tax.toFixed(2)}</span></div>
          <div style="display:flex; justify-content:space-between; margin-bottom:6px;"><span>Service Charge</span><span>+ ৳${bill.service_charge.toFixed(2)}</span></div>
          <div style="display:flex; justify-content:space-between; font-weight:700; font-size:1.15rem; border-top:1px dashed var(--border); padding-top:8px; margin-top:8px; color:var(--primary);">
            <span>Grand Total</span>
            <span>৳${bill.grand_total.toFixed(2)}</span>
          </div>
          <div style="display:flex; justify-content:space-between; margin-top:6px; color:var(--success);"><span>Gross Paid</span><span>৳${bill.gross_paid.toFixed(2)}</span></div>
          ${bill.total_refunded > 0 ? `<div style="display:flex; justify-content:space-between; margin-top:4px; color:var(--danger);"><span>Refunded</span><span>- ৳${bill.total_refunded.toFixed(2)}</span></div>` : ''}
          <div style="display:flex; justify-content:space-between; font-weight:700; font-size:1.2rem; border-top:2px solid var(--border); padding-top:8px; margin-top:8px; color:${bill.outstanding_balance > 0 ? 'var(--warning)' : 'var(--success)'};">
            <span>Outstanding Balance</span>
            <span>৳${bill.outstanding_balance.toFixed(2)}</span>
          </div>
        </div>
      `;

      let paymentFormHtml = ``;
      if (bill.outstanding_balance > 0) {
        paymentFormHtml = `
          <div>
            <h4 style="margin-bottom:12px;">Payment Entry & Split Payment Builder</h4>
            <div id="split-payments-container" style="display:flex; flex-direction:column; gap:12px; margin-bottom:12px;">
              <div class="split-pay-row" style="display:grid; grid-template-columns: 1fr 1fr 1fr 40px; gap:8px; align-items:center;">
                <div>
                  <label class="form-label" style="font-size:0.8rem;">Method</label>
                  <select class="form-control pay-method-select" onchange="SmartBilling.onMethodChange(this)">
                    ${this.paymentMethods.map(m => `<option value="${m.id}" data-ref="${m.requires_reference}">${m.name}</option>`).join('')}
                  </select>
                </div>
                <div>
                  <label class="form-label" style="font-size:0.8rem;">Amount (৳)</label>
                  <input type="number" step="0.01" class="form-control pay-amount-input" value="${bill.outstanding_balance.toFixed(2)}">
                </div>
                <div>
                  <label class="form-label" style="font-size:0.8rem;">TRX Reference</label>
                  <input type="text" class="form-control pay-ref-input" placeholder="TRX ID / Card Ref">
                </div>
                <div style="padding-top:20px;">
                  <button class="btn btn-secondary btn-sm" onclick="this.closest('.split-pay-row').remove()" style="padding:6px 10px;">✕</button>
                </div>
              </div>
            </div>
            <div id="bkash-qr-container" style="display:none;"></div>
            <button class="btn btn-secondary btn-sm" onclick="SmartBilling.addSplitRow()" style="margin-bottom:16px;">+ Add Split Payment Row</button>
          </div>
        `;
      } else {
        paymentFormHtml = `<div class="badge badge-success" style="padding:12px; width:100%; text-align:center; font-size:1rem;">Order Fully Settled (Paid: ৳${bill.net_paid.toFixed(2)})</div>`;
      }

      body.innerHTML = itemsHtml + summaryHtml + paymentFormHtml;

      const footer = document.getElementById('billing-modal-footer');
      let footerBtns = `<button class="btn btn-secondary" onclick="SmartModal.close('order-billing-modal')">Close</button>`;

      if (bill.outstanding_balance > 0) {
        footerBtns += `<button class="btn btn-primary" onclick="SmartBilling.submitPayment(${bill.order_id})">Complete Settlement</button>`;
      } else if (bill.payments && bill.payments.length > 0) {
        footerBtns += `<button class="btn btn-primary" onclick="SmartBilling.openReceiptModal(${bill.payments[0].id}, ${bill.order_id})">View / Print Receipt</button>`;
      }

      footer.innerHTML = footerBtns;
      SmartModal.open('order-billing-modal');

      // Check if default selected method is bKash and trigger QR rendering
      const initialSelect = document.querySelector('.pay-method-select');
      if (initialSelect) {
        this.onMethodChange(initialSelect);
      }

    } catch (err) {
      SmartNotifications.show(err.message || 'Error opening billing details', 'danger');
    }
  },

  onMethodChange(selectEl) {
    if (!selectEl) return;
    const row = selectEl.closest('.split-pay-row');
    if (!row) return;

    const selectedOption = selectEl.options[selectEl.selectedIndex];
    const methodName = selectedOption ? selectedOption.text || '' : '';
    const isBKash = methodName.toLowerCase().includes('bkash');

    let bkashContainer = document.getElementById('bkash-qr-container');

    if (isBKash) {
      const amountInput = row.querySelector('.pay-amount-input');
      const amount = parseFloat(amountInput ? amountInput.value : 0) || (this.currentBill ? this.currentBill.outstanding_balance : 0);
      const orderNum = this.currentBill ? this.currentBill.order_number : 'ORD-RESTA';
      const bkashNumber = '01700000000'; // Restaurant bKash Merchant Number

      const qrPayload = `bKash-Merchant:${bkashNumber}|Amount:${amount.toFixed(2)}|Ref:${orderNum}`;
      const qrImageUrl = `https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=${encodeURIComponent(qrPayload)}`;

      if (!bkashContainer) {
        bkashContainer = document.createElement('div');
        bkashContainer.id = 'bkash-qr-container';
        const splitContainer = document.getElementById('split-payments-container');
        if (splitContainer) {
          splitContainer.after(bkashContainer);
        } else {
          row.after(bkashContainer);
        }
      }

      bkashContainer.style.display = 'block';
      bkashContainer.innerHTML = `
        <div style="background:#fff0f6; border:2px solid #e2136e; border-radius:12px; padding:16px; margin:12px 0; display:flex; gap:16px; align-items:center; box-shadow:0 4px 12px rgba(226, 19, 110, 0.15);">
          <div style="text-align:center; background:#fff; padding:8px; border-radius:8px; border:1px solid #fbcfe8; flex-shrink:0;">
            <img src="${qrImageUrl}" alt="bKash Payment QR" style="width:130px; height:130px; display:block; margin:0 auto;" />
            <div style="font-size:0.7rem; font-weight:700; color:#e2136e; margin-top:4px;">SCAN VIA BKASH APP</div>
          </div>

          <div style="flex:1;">
            <div style="display:flex; align-items:center; gap:8px; margin-bottom:8px; flex-wrap:wrap;">
              <span style="background:#e2136e; color:#fff; font-weight:700; font-size:0.75rem; padding:3px 10px; border-radius:4px;">bKash Merchant</span>
              <span style="font-weight:700; font-size:1.15rem; color:#e2136e;">${bkashNumber}</span>
              <button type="button" class="btn btn-sm btn-outline-secondary" onclick="SmartBilling.copyToClipboard('${bkashNumber}', 'bKash Merchant Number')" style="padding:2px 8px; font-size:0.75rem;">📋 Copy Number</button>
            </div>

            <div style="display:flex; gap:16px; margin-bottom:8px; font-size:0.9rem; flex-wrap:wrap;">
              <div>Amount: <strong style="color:#e2136e;">৳${amount.toFixed(2)}</strong> <button type="button" class="btn btn-sm btn-outline-secondary" onclick="SmartBilling.copyToClipboard('${amount.toFixed(2)}', 'Amount')" style="padding:1px 6px; font-size:0.7rem;">📋 Copy</button></div>
              <div>Reference: <strong style="color:#111;">${orderNum}</strong> <button type="button" class="btn btn-sm btn-outline-secondary" onclick="SmartBilling.copyToClipboard('${orderNum}', 'Reference')" style="padding:1px 6px; font-size:0.7rem;">📋 Copy</button></div>
            </div>

            <div style="font-size:0.75rem; color:#666; margin-bottom:8px;">
              Scan QR code with bKash app OR Send Payment to <strong>${bkashNumber}</strong> with reference <strong>${orderNum}</strong>. Enter received TrxID below:
            </div>

            <div style="display:flex; gap:8px; align-items:center;">
              <input type="text" id="bkash-trx-auto-input" class="form-control" placeholder="Enter bKash Transaction ID (TrxID)" style="border-color:#e2136e; font-weight:600; text-transform:uppercase;" oninput="SmartBilling.syncBKashTrxID(this)">
            </div>
          </div>
        </div>
      `;

      // Attach dynamic input listener to payment amount
      if (amountInput && !amountInput.dataset.bkashListener) {
        amountInput.dataset.bkashListener = 'true';
        amountInput.addEventListener('input', () => {
          if (selectEl.options[selectEl.selectedIndex].text.toLowerCase().includes('bkash')) {
            this.onMethodChange(selectEl);
          }
        });
      }

    } else if (bkashContainer) {
      bkashContainer.style.display = 'none';
    }
  },

  syncBKashTrxID(inputEl) {
    const trxVal = inputEl.value.trim();
    const payRefInput = document.querySelector('.split-pay-row .pay-ref-input');
    if (payRefInput) {
      payRefInput.value = trxVal;
    }
  },

  copyToClipboard(text, label) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(text).then(() => {
        if (window.SmartNotifications) {
          SmartNotifications.show(`${label} (${text}) copied to clipboard!`, 'success', 2000);
        }
      }).catch(err => {
        console.warn('Clipboard write failed:', err);
      });
    } else {
      // Fallback copy
      const tempInput = document.createElement('input');
      tempInput.value = text;
      document.body.appendChild(tempInput);
      tempInput.select();
      document.execCommand('copy');
      document.body.removeChild(tempInput);
      if (window.SmartNotifications) {
        SmartNotifications.show(`${label} (${text}) copied to clipboard!`, 'success', 2000);
      }
    }
  },

  addSplitRow() {
    const container = document.getElementById('split-payments-container');
    if (!container) return;

    const row = document.createElement('div');
    row.className = 'split-pay-row';
    row.style.cssText = 'display:grid; grid-template-columns: 1fr 1fr 1fr 40px; gap:8px; align-items:center;';
    row.innerHTML = `
      <div>
        <select class="form-control pay-method-select" onchange="SmartBilling.onMethodChange(this)">
          ${this.paymentMethods.map(m => `<option value="${m.id}" data-ref="${m.requires_reference}">${m.name}</option>`).join('')}
        </select>
      </div>
      <div>
        <input type="number" step="0.01" class="form-control pay-amount-input" value="0.00">
      </div>
      <div>
        <input type="text" class="form-control pay-ref-input" placeholder="TRX ID / Card Ref">
      </div>
      <div>
        <button class="btn btn-secondary btn-sm" onclick="this.closest('.split-pay-row').remove()" style="padding:6px 10px;">✕</button>
      </div>
    `;
    container.appendChild(row);
  },

  async submitPayment(orderId) {
    const submitBtn = document.querySelector('#billing-modal-footer .btn-primary');
    if (submitBtn) {
      if (submitBtn.disabled) return; // Prevent double submission
      submitBtn.disabled = true;
      submitBtn.dataset.originalText = submitBtn.innerHTML;
      submitBtn.innerHTML = '⏳ Processing Settlement...';
    }

    const rows = document.querySelectorAll('.split-pay-row');
    const splitPayments = [];
    let totalAmt = 0;

    rows.forEach(r => {
      const pmid = parseInt(r.querySelector('.pay-method-select').value, 10);
      const amt = parseFloat(r.querySelector('.pay-amount-input').value) || 0;
      const ref = r.querySelector('.pay-ref-input').value.trim();

      if (amt > 0) {
        splitPayments.push({
          payment_method_id: pmid,
          amount: amt,
          transaction_reference: ref
        });
        totalAmt += amt;
      }
    });

    if (splitPayments.length === 0) {
      SmartNotifications.show('Please enter a valid payment amount.', 'danger');
      if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.innerHTML = submitBtn.dataset.originalText || 'Complete Settlement';
      }
      return;
    }

    const idempotencyKey = 'IK-' + Date.now() + '-' + Math.floor(Math.random() * 100000);

    try {
      const res = await SmartAPI.post('api/v1/payments/index.php', {
        order_id: orderId,
        split_payments: splitPayments,
        idempotency_key: idempotencyKey
      });

      if (res.success) {
        SmartNotifications.show('Payment processed & settled successfully', 'success');
        SmartModal.close('order-billing-modal');
        if (typeof loadActiveOrders === 'function') loadActiveOrders();
        this.loadPaymentHistory();
        
        // Open Receipt Modal for processed payment
        if (res.data.payments && res.data.payments.length > 0) {
          this.openReceiptModal(res.data.payments[0].payment_id, orderId);
        }
      }
    } catch (err) {
      SmartNotifications.show(err.message || 'Payment processing failed', 'danger');
    } finally {
      if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.innerHTML = submitBtn.dataset.originalText || 'Complete Settlement';
      }
    }
  },

  async openReceiptModal(paymentId, orderId) {
    try {
      const res = await SmartAPI.get(`api/v1/receipts/index.php?payment_id=${paymentId}&order_id=${orderId}`);
      if (!res.success || !res.data) {
        SmartNotifications.show('Failed to load receipt', 'danger');
        return;
      }

      const r = res.data;
      const d = r.receipt_data;
      const f = d.financials;

      const titleEl = document.getElementById('receipt-modal-title');
      if (titleEl) {
        titleEl.textContent = `Official Receipt — ${r.receipt_number}`;
      }

      const body = document.getElementById('receipt-modal-body');
      if (body) {
        body.innerHTML = `
          <div id="printable-receipt-area" style="background:#fff; color:#111; padding:24px; border-radius:6px; font-family: monospace; max-width:400px; margin:0 auto; border:1px solid #ccc;">
            <div style="text-align:center; border-bottom:1px dashed #000; padding-bottom:12px; margin-bottom:12px;">
              <h2 style="margin:0; font-size:1.3rem;">SMARTRESTA</h2>
              <div style="font-size:0.85rem;">${d.branch_name || 'Main Outlet'}</div>
              <div style="font-size:0.8rem; margin-top:4px;">Receipt #${r.receipt_number}</div>
              <div style="font-size:0.8rem;">Date: ${d.timestamp}</div>
            </div>

            <div style="margin-bottom:12px; font-size:0.85rem;">
              <div>Order: #${d.order_number} (${d.order_type})</div>
              <div>Table: ${d.table_number || 'N/A'} | Waiter: ${d.waiter_name}</div>
            </div>

            <table style="width:100%; font-size:0.85rem; border-collapse:collapse; margin-bottom:12px;">
              <thead>
                <tr style="border-bottom:1px solid #000;">
                  <th style="text-align:left;">QTY/ITEM</th>
                  <th style="text-align:right;">PRICE</th>
                  <th style="text-align:right;">TOTAL</th>
                </tr>
              </thead>
              <tbody>
                ${(d.items || []).map(i => `
                  <tr>
                    <td>${i.quantity}× ${i.name}</td>
                    <td style="text-align:right;">৳${parseFloat(i.unit_price).toFixed(2)}</td>
                    <td style="text-align:right;">৳${parseFloat(i.subtotal).toFixed(2)}</td>
                  </tr>
                `).join('')}
              </tbody>
            </table>

            <div style="border-top:1px dashed #000; padding-top:8px; font-size:0.85rem;">
              <div style="display:flex; justify-content:space-between;"><span>Subtotal:</span><span>৳${f.subtotal.toFixed(2)}</span></div>
              <div style="display:flex; justify-content:space-between;"><span>Discount:</span><span>- ৳${f.discount.toFixed(2)}</span></div>
              <div style="display:flex; justify-content:space-between;"><span>VAT (5%):</span><span>+ ৳${f.tax.toFixed(2)}</span></div>
              <div style="display:flex; justify-content:space-between; font-weight:bold; font-size:1rem; border-top:1px solid #000; padding-top:4px; margin-top:4px;">
                <span>TOTAL:</span><span>৳${f.grand_total.toFixed(2)}</span>
              </div>
              <div style="display:flex; justify-content:space-between; margin-top:4px;"><span>Paid (${f.payment_method}):</span><span>৳${f.amount_paid_this_txn.toFixed(2)}</span></div>
              ${f.transaction_reference ? `<div style="font-size:0.75rem; text-align:right; color:#555;">Ref: ${f.transaction_reference}</div>` : ''}
              <div style="display:flex; justify-content:space-between; margin-top:4px; font-weight:bold;"><span>Balance Due:</span><span>৳${f.outstanding_balance.toFixed(2)}</span></div>
            </div>

            <div style="text-align:center; margin-top:16px; border-top:1px dashed #000; padding-top:10px; font-size:0.8rem;">
              Thank you for dining with us!
            </div>
          </div>
        `;
      }

      const footer = document.getElementById('receipt-modal-footer');
      if (footer) {
        footer.innerHTML = `
          <button class="btn btn-secondary" onclick="SmartModal.close('receipt-modal')">Close</button>
          <button class="btn btn-primary" onclick="SmartBilling.printReceipt()">🖨️ Print Receipt</button>
        `;
      }

      SmartModal.open('receipt-modal');

    } catch (err) {
      SmartNotifications.show(err.message || 'Error opening receipt', 'danger');
    }
  },

  printReceipt() {
    const content = document.getElementById('printable-receipt-area');
    if (!content) {
      if (window.SmartNotifications) {
        SmartNotifications.show('Receipt content area not found.', 'danger');
      }
      return;
    }

    const win = window.open('', '_blank', 'width=450,height=650');
    if (!win) {
      // Fallback if popups blocked: trigger window.print()
      window.print();
      return;
    }

    win.document.write(`
      <!DOCTYPE html>
      <html>
      <head>
        <title>Print Thermal Receipt</title>
        <style>
          body { font-family: monospace; margin: 0; padding: 20px; color: #111; }
          @media print {
            body { padding: 0; }
          }
        </style>
      </head>
      <body>
        ${content.outerHTML}
        <script>
          window.onload = function() {
            window.focus();
            window.print();
            setTimeout(function() { window.close(); }, 500);
          };
        </script>
      </body>
      </html>
    `);
    win.document.close();
  },

  async loadPaymentHistory() {
    const tbody = document.getElementById('payments-table-tbody');
    if (!tbody) return;

    try {
      const res = await SmartAPI.get('api/v1/payments/index.php');
      if (res.success && res.data) {
        this.allPayments = res.data;
        this.renderPaymentsTable(this.allPayments);
        this.updateReceptionKPIs(this.allPayments);
      } else {
        tbody.innerHTML = `<tr><td colspan="9" style="text-align:center; padding: 40px; color: var(--text-muted);">No payment records recorded in database.</td></tr>`;
      }
    } catch (err) {
      tbody.innerHTML = `<tr><td colspan="9" style="text-align:center; padding: 40px; color: var(--danger);">Failed to load payment history.</td></tr>`;
    }
  },

  async updateReceptionKPIs(payments) {
    let todayNet = 0;
    let cashTotal = 0;
    let digitalTotal = 0;
    let refundsTotal = 0;
    let refundsCount = 0;

    (payments || []).forEach(p => {
      const amt = parseFloat(p.amount || 0);
      const st = (p.status || '').toUpperCase();
      const method = (p.payment_method_name || '').toLowerCase();

      if (st === 'COMPLETED') {
        todayNet += amt;
        if (method.includes('cash')) {
          cashTotal += amt;
        } else if (method.includes('bkash') || method.includes('nagad') || method.includes('card') || method.includes('pos')) {
          digitalTotal += amt;
        }
      } else if (st.includes('REFUND')) {
        refundsTotal += amt;
        refundsCount += 1;
      }
    });

    const netEl = document.getElementById('kpi-rec-today-net');
    if (netEl) netEl.textContent = `৳${todayNet.toFixed(2)}`;

    const cashEl = document.getElementById('kpi-rec-cash-total');
    if (cashEl) cashEl.textContent = `৳${cashTotal.toFixed(2)}`;

    const digitalEl = document.getElementById('kpi-rec-digital-total');
    if (digitalEl) digitalEl.textContent = `৳${digitalTotal.toFixed(2)}`;

    const refundsEl = document.getElementById('kpi-rec-refunds-total');
    if (refundsEl) refundsEl.textContent = `৳${refundsTotal.toFixed(2)}`;

    const refundsCntEl = document.getElementById('kpi-rec-refunds-count');
    if (refundsCntEl) refundsCntEl.textContent = `${refundsCount} Reversals`;

    // Fetch Unpaid Active Orders for Open Bills KPI
    try {
      const ordRes = await SmartAPI.get('api/v1/orders/index.php');
      if (ordRes.success && ordRes.data) {
        const unpaid = ordRes.data.filter(o => o.payment_status !== 'PAID' && o.order_status !== 'CANCELLED');
        let outstandingSum = 0;
        unpaid.forEach(o => {
          const grandTotal = parseFloat(o.grand_total || 0);
          const paid = parseFloat(o.total_paid || 0);
          outstandingSum += Math.max(0, grandTotal - paid);
        });

        const openCntEl = document.getElementById('kpi-rec-open-count');
        if (openCntEl) openCntEl.textContent = unpaid.length;

        const openBalEl = document.getElementById('kpi-rec-open-balance');
        if (openBalEl) openBalEl.textContent = `৳${outstandingSum.toFixed(2)} Outstanding`;
      }
    } catch (e) {
      console.warn('Failed to load unpaid orders summary for Reception KPIs:', e);
    }
  },

  renderPaymentsTable(payments) {
    const tbody = document.getElementById('payments-table-tbody');
    if (!tbody) return;

    if (!payments || payments.length === 0) {
      tbody.innerHTML = `<tr><td colspan="9" style="text-align:center; padding: 40px; color: var(--text-muted);">No matching payment records found.</td></tr>`;
      return;
    }

    tbody.innerHTML = payments.map(p => `
      <tr>
        <td><span class="font-mono">${p.payment_number || 'PM-' + p.id}</span></td>
        <td><span class="font-mono">#${p.order_number}</span></td>
        <td>${p.table_number ? `Table ${p.table_number}` : (p.order_type || 'Takeaway')}</td>
        <td><span class="badge badge-neutral">${p.payment_method_name || 'N/A'}</span></td>
        <td class="price-tag">৳${parseFloat(p.amount).toFixed(2)}</td>
        <td><small class="text-muted">${p.transaction_reference || 'N/A'}</small></td>
        <td>${p.received_by_name || 'Cashier'}</td>
        <td><span class="badge badge-${p.status === 'COMPLETED' ? 'success' : (p.status && p.status.includes('REFUND') ? 'danger' : 'warning')}">${p.status}</span></td>
        <td>
          <button class="btn btn-secondary btn-sm" onclick="SmartBilling.openReceiptModal(${p.id}, ${p.order_id})">Receipt</button>
          ${p.status === 'COMPLETED' ? `<button class="btn btn-danger btn-sm" onclick="SmartBilling.promptRefund(${p.id}, ${p.amount})">Refund</button>` : ''}
        </td>
      </tr>
    `).join('');
  },

  filterPayments() {
    const searchVal = (document.getElementById('payments-search-input')?.value || '').toLowerCase().trim();
    const methodVal = (document.getElementById('payments-method-filter')?.value || '').toLowerCase().trim();
    const statusVal = (document.getElementById('payments-status-filter')?.value || '').toUpperCase().trim();

    let filtered = (this.allPayments || []).filter(p => {
      const matchSearch = !searchVal || 
        (p.payment_number && p.payment_number.toLowerCase().includes(searchVal)) ||
        (p.order_number && p.order_number.toString().toLowerCase().includes(searchVal)) ||
        (p.transaction_reference && p.transaction_reference.toLowerCase().includes(searchVal));

      const matchMethod = !methodVal || (p.payment_method_name && p.payment_method_name.toLowerCase().includes(methodVal));
      const matchStatus = !statusVal || p.status === statusVal;

      return matchSearch && matchMethod && matchStatus;
    });

    this.renderPaymentsTable(filtered);
  },

  async openOrderSelectionModal() {
    try {
      const res = await SmartAPI.get('api/v1/orders/index.php');
      const tbody = document.getElementById('order-select-modal-tbody');
      if (!tbody) return;

      if (res.success && res.data && res.data.length > 0) {
        const unpaidOrders = res.data.filter(o => o.payment_status !== 'PAID' && o.status !== 'CANCELLED');
        if (unpaidOrders.length === 0) {
          tbody.innerHTML = `<tr><td colspan="7" style="text-align:center; padding:30px; color:var(--text-muted);">No active unpaid orders found. All active orders are fully settled!</td></tr>`;
        } else {
          tbody.innerHTML = unpaidOrders.map(o => {
            const grandTotal = parseFloat(o.grand_total || 0);
            const paid = parseFloat(o.total_paid || 0);
            const balance = Math.max(0, grandTotal - paid);
            return `
              <tr>
                <td><span class="font-mono">#${o.order_number || o.id}</span></td>
                <td>${o.table_number ? `Table ${o.table_number}` : (o.order_type || 'Takeaway')}</td>
                <td>${o.waiter_name || 'Staff'}</td>
                <td>৳${grandTotal.toFixed(2)}</td>
                <td>৳${paid.toFixed(2)}</td>
                <td><strong style="color:var(--warning);">৳${balance.toFixed(2)}</strong></td>
                <td>
                  <button class="btn btn-primary btn-sm" onclick="SmartModal.close('order-select-modal'); SmartBilling.openBillingModal(${o.id});">💳 Settle Payment</button>
                </td>
              </tr>
            `;
          }).join('');
        }
      } else {
        tbody.innerHTML = `<tr><td colspan="7" style="text-align:center; padding:30px; color:var(--text-muted);">No orders found in database.</td></tr>`;
      }
      SmartModal.open('order-select-modal');
    } catch (err) {
      SmartNotifications.show('Failed to load active orders: ' + err.message, 'danger');
    }
  },

  async promptRefund(paymentId, originalAmount) {
    const refundAmt = prompt(`Enter refund amount (Max ৳${parseFloat(originalAmount).toFixed(2)}):`, originalAmount);
    if (!refundAmt) return;

    const amount = parseFloat(refundAmt);
    if (isNaN(amount) || amount <= 0) {
      SmartNotifications.show('Invalid refund amount', 'danger');
      return;
    }

    const reason = prompt('Please enter mandatory reason for refund:');
    if (!reason) {
      SmartNotifications.show('Refund reason is required', 'danger');
      return;
    }

    try {
      const res = await SmartAPI.post('api/v1/refunds/index.php', {
        payment_id: paymentId,
        refund_amount: amount,
        reason: reason
      });

      if (res.success) {
        SmartNotifications.show(`Refund of ৳${amount.toFixed(2)} processed successfully`, 'info');
        this.loadPaymentHistory();
        if (typeof loadActiveOrders === 'function') loadActiveOrders();
      }
    } catch (err) {
      SmartNotifications.show(err.message || 'Refund processing failed', 'danger');
    }
  }
};

document.addEventListener('DOMContentLoaded', () => {
  SmartBilling.init();
});

window.SmartBilling = SmartBilling;
