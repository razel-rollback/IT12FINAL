<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid #16a34a;
        }
        
        .header h1 {
            font-size: 22px;
            color: #16a34a;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .header p {
            font-size: 10px;
            color: #666;
        }
        
        .date-range {
            text-align: center;
            background: #f3f4f6;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .date-range strong {
            color: #16a34a;
        }
        
        .summary-section {
            margin-bottom: 20px;
        }
        
        .summary-grid {
            width: 100%;
            margin-bottom: 15px;
        }
        
        .summary-row {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        
        .summary-cell {
            display: table-cell;
            width: 33.33%;
            padding: 10px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            vertical-align: top;
        }
        
        .summary-cell h3 {
            font-size: 9px;
            color: #6b7280;
            margin-bottom: 4px;
            text-transform: uppercase;
            font-weight: bold;
        }
        
        .summary-cell .value {
            font-size: 16px;
            font-weight: bold;
            color: #16a34a;
        }
        
        .summary-cell .sub-value {
            font-size: 8px;
            color: #9ca3af;
            margin-top: 2px;
        }
        
        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #374151;
            margin: 20px 0 10px 0;
            padding-bottom: 5px;
            border-bottom: 2px solid #e5e7eb;
        }
        
        .top-products {
            margin-bottom: 15px;
        }
        
        .product-item {
            background: #f9fafb;
            padding: 7px 9px;
            margin-bottom: 6px;
            border-left: 3px solid #16a34a;
        }
        
        .product-item .rank {
            font-weight: bold;
            color: #16a34a;
            margin-right: 6px;
        }
        
        .product-item .name {
            font-weight: bold;
            color: #374151;
        }
        
        .product-item .details {
            font-size: 9px;
            color: #6b7280;
            margin-top: 2px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        table thead {
            background: #16a34a;
            color: white;
        }
        
        table th {
            padding: 7px 5px;
            text-align: left;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        table tbody tr {
            border-bottom: 1px solid #e5e7eb;
        }
        
        table tbody tr:nth-child(even) {
            background: #f9fafb;
        }
        
        table td {
            padding: 5px;
            font-size: 9px;
        }
        
        .status-badge {
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
            display: inline-block;
        }
        
        .status-paid {
            background: #d1fae5;
            color: #065f46;
        }
        
        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }
        
        .payment-cash {
            background: #d1fae5;
            color: #065f46;
        }
        
        .payment-gcash {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .font-bold {
            font-weight: bold;
        }
        
        .footer {
            margin-top: 25px;
            padding-top: 12px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            font-size: 9px;
            color: #9ca3af;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>SALES REPORT</h1>
        <p>Generated on {{ now()->format('F d, Y h:i A') }}</p>
    </div>
    
    <!-- Date Range -->
    <div class="date-range">
        <strong>Report Period:</strong> 
        {{ \Carbon\Carbon::parse($request->start_date)->format('F d, Y') }} to {{ \Carbon\Carbon::parse($request->end_date)->format('F d, Y') }}
    </div>
    
    <!-- Summary Section -->
    <div class="summary-section">
        <div class="summary-row">
            <div class="summary-cell">
                <h3>Total Sales</h3>
                <div class="value">₱{{ number_format($summary['total_sales'], 2) }}</div>
                <div class="sub-value">{{ $summary['total_transactions'] }} transactions</div>
            </div>
            <div class="summary-cell">
                <h3>Transaction Status</h3>
                <div class="value">{{ $summary['paid_count'] }} Paid</div>
                <div class="sub-value">{{ $summary['pending_count'] }} Pending</div>
            </div>
            <div class="summary-cell">
                <h3>Payment Methods</h3>
                <div class="value">₱{{ number_format($summary['cash_sales'] + $summary['gcash_sales'], 2) }}</div>
                <div class="sub-value">
                    Cash: ₱{{ number_format($summary['cash_sales'], 2) }}<br>
                    GCash: ₱{{ number_format($summary['gcash_sales'], 2) }}
                </div>
            </div>
        </div>
    </div>
    
    <!-- Top Products -->
    @if(count($topProducts) > 0)
    <h2 class="section-title">Top 5 Products</h2>
    <div class="top-products">
        @foreach($topProducts as $name => $data)
        <div class="product-item">
            <span class="rank">#{{ $loop->iteration }}</span>
            <span class="name">{{ $name }}</span>
            <div class="details">
                Quantity Sold: {{ number_format($data['quantity'], 2) }} | 
                Revenue: ₱{{ number_format($data['revenue'], 2) }}
            </div>
        </div>
        @endforeach
    </div>
    @endif
    
    <!-- Sales Transactions -->
    <h2 class="section-title">Sales Transactions</h2>
    
    @if($sales->count() > 0)
    <table>
        <thead>
            <tr>
                <th>Receipt #</th>
                <th>Date</th>
                <th>Customer</th>
                <th>Items</th>
                <th>Payment</th>
                <th>Status</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sales as $sale)
            <tr>
                <td class="font-bold">{{ $sale->receipt_number ?? 'N/A' }}</td>
                <td>{{ \Carbon\Carbon::parse($sale->transaction_date)->format('M d, Y') }}</td>
                <td>{{ $sale->customer->Customer_Name ?? 'Walk-in' }}</td>
                <td class="text-center">{{ $sale->details->count() }}</td>
                <td>
                    <span class="status-badge payment-{{ strtolower($sale->payment_method) }}">
                        {{ $sale->payment_method }}
                    </span>
                </td>
                <td>
                    <span class="status-badge status-{{ $sale->status }}">
                        {{ ucfirst($sale->status) }}
                    </span>
                </td>
                <td class="text-right font-bold">₱{{ number_format($sale->total_amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p style="text-align: center; padding: 25px; color: #9ca3af;">
        No sales transactions found for this period.
    </p>
    @endif
    
    <!-- Footer -->
    <div class="footer">
        <p>This is a computer-generated report. No signature required.</p>
        <p>© {{ date('Y') }} Your Company. All rights reserved.</p>
    </div>
</body>
</html>