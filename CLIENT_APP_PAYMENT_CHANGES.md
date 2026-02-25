# Client App Payment Changes - Instructions

This file contains all the code changes needed for the Electron client app to support spending payment recording and viewing.

## Files to Modify

### 1. `NGN-CLIENT/index.html`

#### Change 1: Add Payment Input Section (After line 758)

Find this section (around line 755-758):
```html
<div class="input-container" style="margin-bottom: 15px;">
    <label for="spending-search-invoice">Invoice Number</label>
    <input type="text" id="spending-search-invoice" name="spending-search-invoice" placeholder="Search by invoice number" maxlength="50">
</div>
```

**ADD THIS CODE AFTER IT:**
```html
<!-- Payment Input Section -->
<div id="payment-input-section" style="display: none; margin-bottom: 20px; padding: 15px; background-color: #e8f5e9; border: 2px solid #4CAF50; border-radius: 4px;">
    <h4 style="color:black; margin-bottom: 15px;">Record Payment</h4>
    <div style="display: flex; gap: 10px; margin-bottom: 15px; flex-wrap: wrap;">
        <div class="input-container" style="flex: 1; min-width: 150px;">
            <label for="payment-amount">Payment Amount (£)</label>
            <input type="number" id="payment-amount" name="payment-amount" placeholder="Enter amount" step="0.01" min="0.01">
        </div>
        <div class="input-container" style="flex: 1; min-width: 150px;">
            <label for="payment-invoice">Payment Invoice (Optional)</label>
            <input type="text" id="payment-invoice" name="payment-invoice" placeholder="Payment invoice number" maxlength="50">
        </div>
    </div>
    <div style="display: flex; gap: 10px;">
        <button id="record-payment-btn" style="flex: 1; background-color: #4CAF50; color: white; padding: 10px;">Record Payment</button>
    </div>
    <div id="payment-result" style="margin-top: 10px; display: none;"></div>
</div>
```

---

### 2. `NGN-CLIENT/assets/js/main.js`

#### Change 1: Update displaySpendingRecords function (Around line 3003)

Find the function `displaySpendingRecords(data)` and locate where it creates the table header (around line 3069-3076).

**REPLACE the headerRow.innerHTML section:**
```javascript
headerRow.innerHTML = `
    <th>Date</th>
    <th>Invoice#</th>
    <th>Branch</th>
    <th>Total</th>
`;
```

**WITH:**
```javascript
headerRow.innerHTML = `
    <th>Date</th>
    <th>Invoice#</th>
    <th>Branch</th>
    <th>Total</th>
    <th>Paid</th>
    <th>Unpaid</th>
    <th>Status</th>
`;
```

**FIND the row.innerHTML section (around line 3098-3103) and REPLACE:**
```javascript
row.innerHTML = `
    <td class="table-cell">${date}</td>
    <td class="table-cell">${record.pos_invoice || 'N/A'}</td>
    <td class="table-cell">${record.branch_id || 'N/A'}</td>
    <td class="table-cell">£${parseFloat(record.total).toFixed(2)}</td>
   
`;
```

**WITH:**
```javascript
// Calculate paid/unpaid amounts
const paidAmount = parseFloat(record.paid_amount || 0);
const unpaidAmount = parseFloat(record.unpaid_amount || record.total);
const isPaid = record.is_paid || false;

// Determine status and color
let statusText = 'Unpaid';
let statusColor = '#f44336'; // red
if (isPaid || unpaidAmount <= 0.01) {
    statusText = 'Paid';
    statusColor = '#4CAF50'; // green
} else if (paidAmount > 0) {
    statusText = 'Partial';
    statusColor = '#FF9800'; // yellow/orange
}

row.innerHTML = `
    <td class="table-cell">${date}</td>
    <td class="table-cell">${record.pos_invoice || 'N/A'}</td>
    <td class="table-cell">${record.branch_id || 'N/A'}</td>
    <td class="table-cell">£${parseFloat(record.total).toFixed(2)}</td>
    <td class="table-cell">£${paidAmount.toFixed(2)}</td>
    <td class="table-cell">£${unpaidAmount.toFixed(2)}</td>
    <td class="table-cell">
        <span style="background-color: ${statusColor}; color: white; padding: 4px 8px; border-radius: 3px; font-size: 12px; font-weight: bold;">
            ${statusText}
        </span>
    </td>
`;
```

#### Change 2: Update summary display (Around line 3012-3019)

**FIND this section:**
```javascript
summaryContent.innerHTML = `
    <p style="color:black"><strong>Customer:</strong> ${data.club_member.full_name} (${data.club_member.phone})</p>
    <p style="color:black"><strong>VRM:</strong> ${data.club_member.vrm || 'N/A'}</p>
    <p style="color:black"><strong>Total Amount:</strong> £${parseFloat(data.summary.total_amount).toFixed(2)}</p>
    <p style="color:black"><strong>Total Transactions:</strong> ${data.summary.total_transactions}</p>
    <p style="color:black"><strong>First Transaction:</strong> ${data.summary.first_transaction_date ? new Date(data.summary.first_transaction_date).toLocaleString('en-GB') : 'N/A'}</p>
    <p style="color:black"><strong>Last Transaction:</strong> ${data.summary.last_transaction_date ? new Date(data.summary.last_transaction_date).toLocaleString('en-GB') : 'N/A'}</p>
`;
```

**REPLACE WITH:**
```javascript
summaryContent.innerHTML = `
    <p style="color:black"><strong>Customer:</strong> ${data.club_member.full_name} (${data.club_member.phone})</p>
    <p style="color:black"><strong>VRM:</strong> ${data.club_member.vrm || 'N/A'}</p>
    <p style="color:black"><strong>Total Spending:</strong> £${parseFloat(data.summary.total_amount).toFixed(2)}</p>
    <p style="color:black"><strong>Total Paid:</strong> £${parseFloat(data.summary.total_paid || 0).toFixed(2)}</p>
    <p style="color:black"><strong>Total Unpaid:</strong> £${parseFloat(data.summary.total_unpaid || data.summary.total_amount).toFixed(2)}</p>
    <p style="color:black"><strong>Total Transactions:</strong> ${data.summary.total_transactions}</p>
    <p style="color:black"><strong>First Transaction:</strong> ${data.summary.first_transaction_date ? new Date(data.summary.first_transaction_date).toLocaleString('en-GB') : 'N/A'}</p>
    <p style="color:black"><strong>Last Transaction:</strong> ${data.summary.last_transaction_date ? new Date(data.summary.last_transaction_date).toLocaleString('en-GB') : 'N/A'}</p>
`;
```

#### Change 3: Show payment input section when customer is found (Around line 2978)

**FIND this section (after successful spending search):**
```javascript
if (response.data.success) {
    currentSpendingData = response.data;
    currentSpendingMember = response.data.club_member;
    displaySpendingRecords(response.data);
    document.getElementById('download-spending-csv-btn').disabled = false;
}
```

**ADD AFTER `displaySpendingRecords(response.data);`:**
```javascript
// Show payment input section
document.getElementById('payment-input-section').style.display = 'block';
```

#### Change 4: Hide payment input section when no customer found (Around line 2982-2984)

**FIND:**
```javascript
} else {
    showAlert(response.data.message || 'Failed to fetch spending records.');
    document.getElementById('spending-results').innerHTML = '<p>No spending records found.</p>';
    document.getElementById('spending-summary').style.display = 'none';
    document.getElementById('download-spending-csv-btn').disabled = true;
}
```

**ADD:**
```javascript
// Hide payment input section
document.getElementById('payment-input-section').style.display = 'none';
```

#### Change 5: Add recordSpendingPayment function (Add after displaySpendingRecords function, around line 3125)

**ADD THIS NEW FUNCTION:**
```javascript
// Function to record spending payment
async function recordSpendingPayment() {
    const recordButton = document.getElementById('record-payment-btn');
    const paymentResult = document.getElementById('payment-result');
    
    // Disable button
    recordButton.disabled = true;
    recordButton.textContent = 'Recording...';
    paymentResult.style.display = 'none';

    // Get values
    const phone = document.getElementById('spending-search-phone').value.trim();
    const vrm = document.getElementById('spending-search-vrm').value.trim().toUpperCase();
    const amountPaid = document.getElementById('payment-amount').value.trim();
    const invoice = document.getElementById('payment-invoice').value.trim();

    // Validate
    if (!phone && !vrm) {
        showAlert('Please search for a customer first.');
        recordButton.disabled = false;
        recordButton.textContent = 'Record Payment';
        return;
    }

    if (!amountPaid || parseFloat(amountPaid) <= 0) {
        showAlert('Please enter a valid payment amount.');
        recordButton.disabled = false;
        recordButton.textContent = 'Record Payment';
        return;
    }

    const payload = {
        amount_paid: parseFloat(amountPaid),
        branch_id: getBranchId(),
    };

    if (phone) payload.phone = phone;
    if (vrm) payload.vrm = vrm;
    if (invoice) payload.invoice = invoice;

    try {
        const response = await axios.post(`${REST_API_URL}record-spending-payment`, payload, {
            headers: {
                'Authorization': `Bearer ${getAuthToken()}`,
                'Content-Type': 'application/json'
            }
        });

        if (response.data.success) {
            paymentResult.style.display = 'block';
            paymentResult.innerHTML = `
                <p style="color: green; font-weight: bold;">Payment recorded successfully!</p>
                <p style="color:black;"><strong>Amount Paid:</strong> £${response.data.amount_paid}</p>
                <p style="color:black;"><strong>Total Spending:</strong> £${response.data.total_spending}</p>
                <p style="color:black;"><strong>Total Paid:</strong> £${response.data.total_paid}</p>
                <p style="color:black;"><strong>Total Unpaid:</strong> £${response.data.total_unpaid}</p>
            `;

            // Clear payment inputs
            document.getElementById('payment-amount').value = '';
            document.getElementById('payment-invoice').value = '';

            // Refresh spending list
            document.getElementById('search-spending-btn').click();
        } else {
            showAlert(response.data.message || 'Failed to record payment.');
            paymentResult.style.display = 'block';
            paymentResult.innerHTML = `<p style="color: red;">${response.data.message || 'Failed to record payment.'}</p>`;
        }
    } catch (error) {
        console.error('Error recording payment:', error);
        let errorMessage = 'Failed to record payment. Please try again.';
        if (error.response && error.response.data && error.response.data.message) {
            errorMessage = Array.isArray(error.response.data.message) 
                ? error.response.data.message.join('\n')
                : error.response.data.message;
        }
        showAlert(errorMessage);
        paymentResult.style.display = 'block';
        paymentResult.innerHTML = `<p style="color: red;">${errorMessage}</p>`;
    } finally {
        recordButton.disabled = false;
        recordButton.textContent = 'Record Payment';
    }
}
```

#### Change 6: Add event listener for Record Payment button (Add after search-spending-btn event listener, around line 3000)

**ADD THIS CODE:**
```javascript
// Handle Record Payment button click
document.getElementById('record-payment-btn').addEventListener('click', function () {
    recordSpendingPayment();
});

// Allow Enter key to trigger payment recording
document.getElementById('payment-amount').addEventListener('keydown', function (e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        document.getElementById('record-payment-btn').click();
    }
});

document.getElementById('payment-invoice').addEventListener('keydown', function (e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        document.getElementById('record-payment-btn').click();
    }
});
```

#### Change 7: Hide payment section on initial load (Add in window.onload or DOMContentLoaded, around line 979)

**FIND the window.onload section and ADD:**
```javascript
// Hide payment input section initially
document.getElementById('payment-input-section').style.display = 'none';
```

---

## Summary of Changes

### HTML Changes (`index.html`):
1. Added payment input section with amount and invoice fields
2. Added Record Payment button
3. Added payment result display area

### JavaScript Changes (`main.js`):
1. Updated `displaySpendingRecords()` to show Paid/Unpaid/Status columns
2. Updated summary to show Total Paid and Total Unpaid
3. Added `recordSpendingPayment()` function
4. Added event listeners for payment button
5. Show/hide payment section based on customer search

## Testing Checklist

1. Search for a customer by phone or VRM
2. Verify payment input section appears
3. Enter payment amount and optional invoice
4. Click "Record Payment"
5. Verify payment is recorded and spending list refreshes
6. Verify paid/unpaid amounts update correctly
7. Verify status badges show correctly (Paid/Partial/Unpaid)
8. Test with partial payments (should apply FIFO)
9. Test with overpayment (should show error)

## API Endpoints Used

- `POST /api/record-spending-payment` - Record payment
- `POST /api/list-customer-spending` - Get spending records (already exists, now includes paid/unpaid)

## Notes

- Payment section is hidden by default
- Payment section shows only after successful customer search
- Payment button is disabled during processing
- Spending list auto-refreshes after successful payment
- All amounts display with 2 decimal places
- Status badges use colour coding: Green (Paid), Yellow (Partial), Red (Unpaid)
