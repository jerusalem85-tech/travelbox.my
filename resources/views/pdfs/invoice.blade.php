<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Invoice – {{ $invoice->invoice_number }}</title>
<style>
@page{margin:20mm 15mm}
body{font-family:'DejaVu Sans',sans-serif;font-size:10pt;color:#333;line-height:1.5}
.header{text-align:center;border-bottom:3px solid #1a56db;padding-bottom:10px;margin-bottom:20px}
.header h1{color:#1a56db;font-size:18pt;margin:0}
.header p{margin:2px 0;font-size:9pt;color:#6b7280}
.section{margin-bottom:18px;page-break-inside:avoid}
.section h3{font-size:10pt;color:#1a56db;text-transform:uppercase;border-bottom:1px solid #e5e7eb;padding-bottom:4px;margin:0 0 8px}
table{width:100%;border-collapse:collapse;font-size:9pt}
table th{background:#f3f4f6;text-align:left;padding:5px 6px;font-weight:600;border:1px solid #d1d5db}
table td{padding:4px 6px;border:1px solid #d1d5db;vertical-align:top}
table td.text-right{text-align:right}
table td.text-center{text-align:center}
.grid-2{display:flex;gap:15px}
.grid-2>div{flex:1}
.label{color:#6b7280;font-size:8pt;text-transform:uppercase;margin-bottom:1px}
.value{font-weight:600;font-size:10pt}
.footer{text-align:center;border-top:2px solid #e5e7eb;padding-top:10px;margin-top:20px;font-size:8pt;color:#9ca3af}
.badge{display:inline-block;padding:1px 6px;border-radius:3px;font-size:7pt;font-weight:600;text-transform:uppercase}
.badge-draft{background:#f3f4f6;color:#6b7280}
.badge-sent{background:#dbeafe;color:#1e40af}
.badge-paid{background:#d1fae5;color:#065f46}
.badge-overdue{background:#fee2e2;color:#991b1b}
.badge-cancelled{background:#fee2e2;color:#991b1b}
.total-row td{font-weight:700;border-top:2px solid #1a56db}
.grand-total-row td{font-weight:700;font-size:11pt;border-top:3px solid #1a56db;background:#eff6ff}
</style>
</head>
<body>

<div class="header">
    <h1>TravelBox</h1>
    <p style="font-size:11pt;font-weight:600">INVOICE</p>
    <p>{{ $invoice->invoice_number }} &bull; <span class="badge badge-{{ $invoice->status }}">{{ $invoice->status }}</span></p>
</div>

<div class="section">
    <div class="grid-2">
        <div>
            <div class="label">Bill To</div>
            <div class="value">{{ $invoice->customer?->full_name ?? '—' }}</div>
            @if ($invoice->customer?->company)<div style="font-size:9pt">{{ $invoice->customer->company }}</div>@endif
            @if ($invoice->customer?->email)<div style="font-size:9pt;color:#6b7280">{{ $invoice->customer->email }}</div>@endif
            @if ($invoice->customer?->phone)<div style="font-size:9pt;color:#6b7280">{{ $invoice->customer->phone }}</div>@endif
            @if ($invoice->customer?->address)<div style="font-size:9pt;color:#6b7280">{{ $invoice->customer->address }}</div>@endif
        </div>
        <div>
            <div class="label">Invoice Details</div>
            <div class="value">{{ $invoice->invoice_number }}</div>
            <div style="font-size:9pt;color:#6b7280">
                Date: {{ $invoice->invoice_date?->format('M d, Y') }}<br>
                Due: {{ $invoice->due_date?->format('M d, Y') }}<br>
                @if ($invoice->trip)Trip: {{ $invoice->trip->trip_number }}@endif
            </div>
        </div>
    </div>
</div>

@if ($invoice->items->isNotEmpty())
<div class="section">
    <h3>Line Items</h3>
    <table>
        <thead>
            <tr>
                <th style="width:45%">Description</th>
                <th style="width:10%" class="text-center">Qty</th>
                <th style="width:15%" class="text-right">Unit Price</th>
                <th style="width:15%" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->items as $item)
            <tr>
                <td>{{ $item->description }}</td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                <td class="text-right">{{ number_format($item->quantity * $item->unit_price, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="3" class="text-right">Subtotal</td>
                <td class="text-right">{{ number_format($invoice->subtotal, 2) }}</td>
            </tr>
            @if ($invoice->tax > 0)
            <tr>
                <td colspan="3" class="text-right">Tax</td>
                <td class="text-right">{{ number_format($invoice->tax, 2) }}</td>
            </tr>
            @endif
            @if ($invoice->discount > 0)
            <tr>
                <td colspan="3" class="text-right">Discount</td>
                <td class="text-right">-{{ number_format($invoice->discount, 2) }}</td>
            </tr>
            @endif
            <tr class="grand-total-row">
                <td colspan="3" class="text-right">Grand Total</td>
                <td class="text-right">{{ number_format($invoice->grand_total, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</div>
@endif

@if ($invoice->notes)
<div class="section">
    <h3>Notes</h3>
    <p style="font-size:9pt">{{ nl2br(e($invoice->notes)) }}</p>
</div>
@endif

<div class="footer">
    <p>Generated by TravelBox ERP &bull; {{ now()->format('M d, Y H:i') }} &bull; travelbox.my</p>
    <p style="font-size:7pt">This is a computer-generated invoice. Thank you for your business.</p>
</div>

</body>
</html>
