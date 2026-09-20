<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاتورة رقم #{{ $order->order_number }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 14px;
            color: #444;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }

        .invoice-box {
            max-width: 800px;
            margin: 40px auto;
            padding: 40px;
            border: 1px solid #f0f0f0;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
            border-radius: 8px;
        }

        .header-table {
            margin-bottom: 40px;
            width: 100%;
        }

        .brand {
            font-size: 28px;
            font-weight: bold;
            color: #2d3436;
            text-align: left;
        }

        .invoice-title {
            text-align: right;
            color: #636e72;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .info-section {
            margin-bottom: 30px;
            width: 100%;
        }

        .info-section td {
            vertical-align: top;
            border: none;
            padding-bottom: 20px;
        }

        .label {
            color: #2E3192;
            text-transform: uppercase;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        table th {
            background: #fdfdfd;
            text-align: right;
            padding: 12px 10px;
            border-bottom: 2px solid #eee;
            font-size: 16px;
            color: #2E3192;
        }

        table td {
            padding: 15px 10px;
            border-bottom: 1px solid #f0f0f0;
            text-align: right;
        }

        .totals-container {
            width: 40%;
            margin-right: auto;
            margin-top: 20px;
        }

        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
        }

        .totals-row.grand-total {
            border-top: 2px solid #2d3436;
            margin-top: 10px;
            padding-top: 10px;
            font-weight: bold;
            font-size: 18px;
            color: #000;
        }

        .actions {
            text-align: center;
            margin-top: 30px;
        }

        .btn-print {
            background: #2d3436;
            color: #fff;
            border: none;
            padding: 10px 25px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: 0.3s;
        }

        .btn-print:hover {
            background: #000;
        }

        @media print {
            .actions {
                display: none;
            }

            .invoice-box {
                border: none;
                box-shadow: none;
                padding: 0;
                margin: 0;
            }

            body {
                padding: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <table class="header-table" style="width: 100%;">
            <tr>
                <td class="invoice-title" style="text-align: right;">
                    <h2 style="margin:0;">فاتورة</h2>
                    <span style="color:#00b894;">#{{ $order->order_number }}</span>
                </td>
                <td class="brand" style="text-align: left;">
                    @php
                        $defaultLogo = asset('images/logo.png');
                        $savedLogo =
                            $globalSettings && $globalSettings->logo
                                ? public_path('images/settings/' . $globalSettings->logo)
                                : null;

                        $logoSrc =
                            $savedLogo && file_exists($savedLogo)
                                ? asset('images/settings/' . $globalSettings->logo)
                                : $defaultLogo;
                    @endphp

                    <img src="{{ $logoSrc }}" alt="AL Bashawri Logo" class="mb-4"
                        style="height: 45px; width: auto; object-fit: contain;">
                </td>

            </tr>
        </table>


        <table class="info-section">
            <tr>
                <td width="50%">
                    <span class="label">العميل</span>
                    <strong>{{ $order->user->name ?? 'ضيف' }}</strong><br>
                    {{ $order->shipping_email }} <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                        fill="currentColor" class="bi bi-envelope-fill" viewBox="0 0 16 16">
                        <path
                            d="M.05 3.555A2 2 0 0 1 2 2h12a2 2 0 0 1 1.95 1.555L8 8.414zM0 4.697v7.104l5.803-3.558zM6.761 8.83l-6.57 4.027A2 2 0 0 0 2 14h12a2 2 0 0 0 1.808-1.144l-6.57-4.027L8 9.586zm3.436-.586L16 11.801V4.697z" />
                    </svg><br>
                    {{ $order->shipping_phone }}<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                        fill="currentColor" class="bi bi-telephone-fill" viewBox="0 0 16 16">
                        <path fill-rule="evenodd"
                            d="M1.885.511a1.745 1.745 0 0 1 2.61.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.68.68 0 0 0 .178.643l2.457 2.457a.68.68 0 0 0 .644.178l2.189-.547a1.75 1.75 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.6 18.6 0 0 1-7.01-4.42 18.6 18.6 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877z" />
                    </svg><br>
                    {{ $order->shipping_country }}<br>
                    {{ $order->shipping_city }}<br>
                    {{ $order->shipping_address }}
                </td>
                <td width="50%" style="text-align: right;">
                    <span class="label">تاريخ الإصدار</span>
                    <strong>{{ $order->created_at->format('d M Y') }}</strong><br>
                    <span class="label" style="margin-top: 10px;">حالة الدفع</span>
                    <strong>
                        @if ($order->telrTransaction && $order->telrTransaction->status == 'paid')
                            مدفوع
                        @else
                            غير مدفوع
                        @endif
                    </strong>
                </td>

            </tr>
        </table>

        <table>
            <thead>
                <tr>
                    <th>المنتج</th>
                    <th width="10%">الكمية</th>
                    <th width="20%">السعر</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $item)
                    <tr>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->price, 2) }} ريال</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals-container">
            <div class="totals-row">
                <span>المجموع الفرعي:</span>
                <span>{{ number_format($order->subtotal ?? 0, 2) }} ريال</span>
            </div>
            @if ($order->tax)
                <div class="totals-row">
                    <span>الضريبة:</span>
                    <span>{{ number_format($order->tax, 2) }} ريال</span>
                </div>
            @endif
            <div class="totals-row">
                <span>الشحن:</span>
                <span>{{ number_format($order->shipping_cost, 2) }} ريال</span>
            </div>
            <div class="totals-row grand-total">
                <span>الإجمالي:</span>
                <span>{{ number_format($order->total, 2) }} ريال</span>
            </div>
        </div>

        <div class="actions">
            <button class="btn-print" onclick="window.print()">
                طباعة الفاتورة
            </button>
        </div>
    </div>
</body>

</html>
