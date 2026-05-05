<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $reportTitle }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #0f172a;
            font-size: 12px;
            margin: 0;
            padding: 28px;
            background: #ffffff;
        }

        .header {
            margin-bottom: 22px;
            padding-bottom: 14px;
            border-bottom: 2px solid #e2e8f0;
        }

        .brand {
            font-size: 10px;
            letter-spacing: 0.28em;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 4px;
        }

        h1 {
            font-size: 22px;
            margin: 0 0 6px;
        }

        .subtitle {
            margin: 0;
            color: #475569;
        }

        .summary {
            display: flex;
            gap: 16px;
            margin: 20px 0 24px;
        }

        .summary-card {
            flex: 1;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 14px 16px;
            background: #f8fafc;
        }

        .summary-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.18em;
            color: #64748b;
            margin-bottom: 6px;
        }

        .summary-value {
            font-size: 20px;
            font-weight: bold;
            color: #0f172a;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.16em;
            color: #475569;
            padding: 10px 8px;
            border-bottom: 1px solid #cbd5e1;
        }

        tbody td {
            padding: 10px 8px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: bold;
        }

        .badge-success {
            background: #dcfce7;
            color: #166534;
        }

        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .muted {
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="brand">ClearStock</div>
        <h1>{{ $reportTitle }}</h1>
        <p class="subtitle">{{ $reportSubtitle }} | {{ $periodLabel }}</p>
    </div>

    <div class="summary">
        <div class="summary-card">
            <div class="summary-label">Transactions</div>
            <div class="summary-value">{{ $sales->count() }}</div>
        </div>
        <div class="summary-card">
            <div class="summary-label">Net Revenue</div>
            <div class="summary-value">PHP {{ number_format($total, 2) }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Sale ID</th>
                <th>Date / Time</th>
                <th>Cashier</th>
                <th class="text-center">Items</th>
                <th class="text-right">Total</th>
                <th class="text-center">Payment</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sales as $sale)
                @php($isReturned = $sale->sale_returns_count > 0)
                <tr>
                    <td>#{{ str_pad($sale->saleID, 5, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $sale->sold_at?->format('M d, Y h:i A') ?? $sale->created_at?->format('M d, Y h:i A') }}</td>
                    <td>{{ $sale->user->name ?? '-' }}</td>
                    <td class="text-center">{{ $sale->saleLines->count() }}</td>
                    <td class="text-right">
                        @if($isReturned)
                            <div class="muted">PHP 0.00</div>
                            <div class="muted" style="font-size: 10px; text-decoration: line-through;">PHP {{ number_format($sale->total, 2) }}</div>
                        @else
                            PHP {{ number_format($sale->total, 2) }}
                        @endif
                    </td>
                    <td class="text-center">{{ $sale->payment_method }}</td>
                    <td class="text-center">
                        @if($isReturned)
                            <span class="badge badge-warning">Returned</span>
                        @else
                            <span class="badge badge-success">Saved</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center muted">No sales found for this period.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
