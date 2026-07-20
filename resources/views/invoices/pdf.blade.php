<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
            color: #111;
        }

        .header {
            margin-bottom: 30px;
        }

        .clinic {
            font-size: 20px;
            font-weight: bold;
        }

        .meta {
            color: #555;
            margin-top: 4px;
        }

        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table.items th {
            text-align: left;
            border-bottom: 2px solid #333;
            padding: 8px 4px;
        }

        table.items td {
            border-bottom: 1px solid #ddd;
            padding: 8px 4px;
        }

        .right {
            text-align: right;
        }

        .total-row td {
            border-bottom: none;
            font-weight: bold;
            font-size: 15px;
            padding-top: 14px;
        }

        .status {
            display: inline-block;
            padding: 3px 10px;
            border: 1px solid #333;
            font-weight: bold;
            text-transform: uppercase;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="clinic">HealthCare System</div>
        <div class="meta">
            Invoice #{{ $invoice->invoice_id }}<br>
            Date: {{ $invoice->created_at->format('M j, Y') }}<br>
            Patient: {{ $invoice->patient->first_name }} {{ $invoice->patient->last_name }}<br>
            Doctor: Dr. {{ $invoice->appointment->doctor->first_name }} {{ $invoice->appointment->doctor->last_name }}
        </div>
        <p><span class="status">{{ $invoice->status }}</span></p>
    </div>

    <table class="items">
        <thead>
            <tr>
                <th>Description</th>
                <th class="right">Qty</th>
                <th class="right">Unit price</th>
                <th class="right">Line total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td class="right">{{ $item->quantity }}</td>
                    <td class="right">${{ number_format($item->unit_price_cents / 100, 2) }}</td>
                    <td class="right">${{ number_format($item->line_total_cents / 100, 2) }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="3" class="right">Total</td>
                <td class="right">${{ $invoice->totalDollars() }}</td>
            </tr>
        </tbody>
    </table>
</body>

</html>
