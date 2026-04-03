<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $invoice->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #333;
            margin: 40px;
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        .header h1 {
            font-size: 28px;
            color: #2d3748;
            margin: 0;
        }
        .header p {
            color: #718096;
            margin: 5px 0;
        }
        .section {
            margin-bottom: 30px;
        }
        .section h3 {
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 8px;
            color: #2d3748;
        }
        .client-info p {
            margin: 4px 0;
        }
        .status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 12px;
        }
        .status.paid {
            background: #c6f6d5;
            color: #276749;
        }
        .status.unpaid {
            background: #fed7d7;
            color: #9b2c2c;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table th {
            background: #2d3748;
            color: white;
            padding: 10px;
            text-align: left;
        }
        table td {
            padding: 10px;
            border-bottom: 1px solid #e2e8f0;
        }
        table tr:nth-child(even) {
            background: #f7fafc;
        }
        .totals {
            float: right;
            width: 300px;
            margin-top: 20px;
        }
        .totals table td {
            padding: 6px 10px;
        }
        .totals .total-row {
            font-weight: bold;
            font-size: 16px;
            background: #2d3748;
            color: white;
        }
        .footer {
            margin-top: 80px;
            text-align: center;
            color: #718096;
            font-size: 12px;
            border-top: 1px solid #e2e8f0;
            padding-top: 20px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>INVOICE #{{ $invoice->id }}</h1>
        <p>Generated on {{ now()->format('F d, Y') }}</p>
    </div>

    <div class="section client-info">
        <h3>Client Information</h3>
        <p><strong>Name:</strong> {{ $invoice->client_name }}</p>
        <p><strong>Email:</strong> {{ $invoice->client_email }}</p>
        <p><strong>Phone:</strong> {{ $invoice->client_phone }}</p>
        <p><strong>Due Date:</strong> {{ \Carbon\Carbon::parse($invoice->due_date)->format('F d, Y') }}</p>
        <p><strong>Status:</strong>
            <span class="status {{ $invoice->status }}">
                {{ strtoupper($invoice->status) }}
            </span>
        </p>
    </div>

    <div class="section">
        <h3>Invoice Items</h3>
        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>${{ number_format($item->unit_price, 2) }}</td>
                    <td>${{ number_format($item->quantity * $item->unit_price, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="totals">
        <table>
            <tr>
                <td>Subtotal</td>
                <td>${{ number_format($subtotal, 2) }}</td>
            </tr>
            <tr>
                <td>Discount ({{ $invoice->discount }}%)</td>
                <td>-${{ number_format($discountAmount, 2) }}</td>
            </tr>
            <tr>
                <td>Tax ({{ $invoice->tax }}%)</td>
                <td>+${{ number_format($taxAmount, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td>Total</td>
                <td>${{ number_format($invoice->total, 2) }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Thank you for your business!</p>
    </div>

</body>
</html>
