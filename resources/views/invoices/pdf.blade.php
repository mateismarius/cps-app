{{-- resources/views/invoices/pdf.blade.php --}}

    <!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #333;
        }

        .container {
            padding: 20px;
        }

        .header {
            margin-bottom: 30px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 10px;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 5px;
        }

        .invoice-title {
            font-size: 28px;
            font-weight: bold;
            text-align: right;
            color: #1e40af;
        }

        .info-row {
            display: table;
            width: 100%;
            margin: 20px 0;
        }

        .info-column {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .info-box {
            margin-bottom: 15px;
        }

        .info-label {
            font-weight: bold;
            color: #666;
            font-size: 10px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .info-content {
            line-height: 1.5;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        thead {
            background-color: #2563eb;
            color: white;
        }

        th {
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }

        td {
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
        }

        tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .text-right {
            text-align: right;
        }

        .totals-table {
            width: 300px;
            margin-left: auto;
            margin-top: 20px;
        }

        .totals-table td {
            border: none;
            padding: 5px 10px;
        }

        .totals-table .total-row {
            font-weight: bold;
            font-size: 14px;
            background-color: #2563eb;
            color: white;
        }

        .notes {
            margin-top: 30px;
            padding: 15px;
            background-color: #f9fafb;
            border-left: 3px solid #2563eb;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 10px;
            color: #666;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .badge-paid {
            background-color: #10b981;
            color: white;
        }

        .badge-sent {
            background-color: #3b82f6;
            color: white;
        }

        .badge-draft {
            background-color: #6b7280;
            color: white;
        }

        .badge-overdue {
            background-color: #ef4444;
            color: white;
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Header -->
    <div class="header">
        <div style="display: table; width: 100%;">
            <div style="display: table-cell; width: 50%; vertical-align: top;">
                <div class="company-name">CPS Network Communications</div>
                <div class="info-content">
                    123 Business Street<br>
                    London, E1 6AN<br>
                    United Kingdom<br>
                    Tel: +44 20 1234 5678<br>
                    VAT: GB123456789
                </div>
            </div>
            <div style="display: table-cell; width: 50%; vertical-align: top; text-align: right;">
                <div class="invoice-title">INVOICE</div>
                <div style="margin-top: 10px;">
                        <span class="badge badge-{{ strtolower($invoice->status) }}">
                            {{ strtoupper($invoice->status) }}
                        </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Invoice Details -->
    <div class="info-row">
        <div class="info-column">
            <div class="info-box">
                <div class="info-label">Bill To:</div>
                <div class="info-content">
                    <strong>{{ $invoice->invoiceable->name }}</strong><br>
                    @if($invoice->invoiceable->address)
                        {{ $invoice->invoiceable->address }}<br>
                    @endif
                    @if($invoice->invoiceable->city && $invoice->invoiceable->postcode)
                        {{ $invoice->invoiceable->city }}, {{ $invoice->invoiceable->postcode }}<br>
                    @endif
                    @if($invoice->invoiceable->email)
                        {{ $invoice->invoiceable->email }}<br>
                    @endif
                    @if($invoice->invoiceable->vat_number)
                        VAT: {{ $invoice->invoiceable->vat_number }}
                    @endif
                </div>
            </div>
        </div>
        <div class="info-column">
            <div class="info-box">
                <div class="info-label">Invoice Number:</div>
                <div class="info-content">{{ $invoice->invoice_number }}</div>
            </div>
            <div class="info-box">
                <div class="info-label">Invoice Date:</div>
                <div class="info-content">{{ $invoice->invoice_date->format('d/m/Y') }}</div>
            </div>
            <div class="info-box">
                <div class="info-label">Due Date:</div>
                <div class="info-content">{{ $invoice->due_date->format('d/m/Y') }}</div>
            </div>
            @if($invoice->paid_date)
                <div class="info-box">
                    <div class="info-label">Paid Date:</div>
                    <div class="info-content">{{ $invoice->paid_date->format('d/m/Y') }}</div>
                </div>
            @endif
        </div>
    </div>

    <!-- Invoice Items -->
    <table>
        <thead>
        <tr>
            <th>Description</th>
            <th class="text-right">Quantity</th>
            <th class="text-right">Unit Price</th>
            <th class="text-right">Amount</th>
        </tr>
        </thead>
        <tbody>
        @foreach($invoice->items as $item)
            <tr>
                <td>{{ $item->description }}</td>
                <td class="text-right">{{ number_format($item->quantity, 2) }} {{ $item->unit }}</td>
                <td class="text-right">£{{ number_format($item->unit_price, 2) }}</td>
                <td class="text-right">£{{ number_format($item->amount, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <!-- Totals -->
    <table class="totals-table">
        <tr>
            <td>Subtotal:</td>
            <td class="text-right">£{{ number_format($invoice->subtotal, 2) }}</td>
        </tr>
        <tr>
            <td>VAT ({{ number_format($invoice->vat_rate, 0) }}%):</td>
            <td class="text-right">£{{ number_format($invoice->vat_amount, 2) }}</td>
        </tr>
        <tr class="total-row">
            <td>Total:</td>
            <td class="text-right">£{{ number_format($invoice->total_amount, 2) }}</td>
        </tr>
    </table>

    <!-- Notes -->
    @if($invoice->notes)
        <div class="notes">
            <div class="info-label">Notes:</div>
            <div>{{ $invoice->notes }}</div>
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Thank you for your business</p>
        <p>Payment terms: {{ $invoice->invoiceable->payment_terms_days ?? 30 }} days</p>
        <p>Bank Details: Account Name: CPS Network Communications Ltd | Sort Code: 12-34-56 | Account Number: 12345678</p>
    </div>
</div>
</body>
</html>
