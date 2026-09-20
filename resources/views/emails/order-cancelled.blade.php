<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تم إلغاء الطلب</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@300;400;600;700&display=swap');

        body {
            font-family: 'Noto Kufi+Arabic', 'Segoe UI', Tahoma, sans-serif;
            background-color: #fcfcfc;
            color: #444;
            margin: 0;
            padding: 0;
            line-height: 1.8;
            direction: rtl;
        }

        .container {
            max-width: 650px;
            margin: 50px auto;
            background-color: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid #eee;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
        }

        .header {
            padding: 50px 30px;
            text-align: center;
            border-bottom: 1px solid #f9f9f9;
        }

        .header-icon {
            width: 80px;
            height: 80px;
            background-color: #fff5f5;
            color: #e53e3e;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 40px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #2d3748;
            font-weight: 700;
        }

        .header p {
            margin: 10px 0 0;
            font-size: 15px;
            color: #718096;
        }

        .content {
            padding: 40px 50px;
        }

        .info-card {
            background-color: #fdfdfd;
            border: 1px solid #f0f0f0;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 35px;
        }

        .info-card h3 {
            margin: 0 0 15px;
            font-size: 16px;
            color: #2d3748;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px dashed #edf2f7;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #718096;
            font-size: 14px;
        }

        .info-value {
            color: #2d3748;
            font-weight: 600;
            font-size: 14px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .items-table th {
            text-align: right;
            padding: 12px 0;
            font-size: 13px;
            color: #a0aec0;
            text-transform: uppercase;
            border-bottom: 2px solid #f7fafc;
        }

        .items-table td {
            padding: 18px 0;
            border-bottom: 1px solid #f7fafc;
            font-size: 14px;
        }

        .product-name {
            color: #2d3748;
            font-weight: 600;
            display: block;
        }

        .product-variant {
            font-size: 12px;
            color: #a0aec0;
        }

        .total-row {
            font-size: 18px;
            font-weight: 700;
            color: #2d3748;
        }

        .refund-notice {
            background-color: #fffaf0;
            border-right: 4px solid #ed8936;
            padding: 20px;
            border-radius: 8px;
            margin: 30px 0;
            font-size: 14px;
        }

        .button {
            display: inline-block;
            padding: 16px 40px;
            background-color: #2d3748;
            color: #ffffff;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 15px;
            transition: background 0.3s;
            margin-bottom: 25px;
        }

        .button:hover {
            background-color: #1a202c;
        }

        .support-text {
            font-size: 13px;
            color: #a0aec0;
            text-align: center;
        }

        .support-text a {
            color: #4a5568;
            text-decoration: underline;
        }

        .footer {
            background-color: #fafafa;
            padding: 40px;
            text-align: center;
            font-size: 13px;
            color: #a0aec0;
        }

        .footer a {
            color: #718096;
            text-decoration: none;
            margin: 0 10px;
        }

        @media (max-width: 600px) {
            .content { padding: 30px 20px; }
            .container { margin: 10px; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <div class="header-icon">✕</div>
        <h1>إلغاء الطلب</h1>
        <p>نؤكد لك أنه تم إلغاء طلبك بنجاح</p>
    </div>

    <div class="content">
        <div class="info-card">
            <h3>📋 ملخص الإلغاء</h3>
            <div class="info-row">
                <span class="info-label">رقم الطلب</span>
                <span class="info-value" style="color:#e53e3e">#{{ $order->order_number }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">تاريخ الطلب</span>
                <span class="info-value">{{ $order->created_at->format('d/m/Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">حالة الدفع</span>
                <span class="info-value">{{ $order->payment_status === 'paid' ? 'تم الدفع' : 'لم يتم الدفع' }}</span>
            </div>
        </div>

        <h3 style="font-size:16px; margin-bottom:10px;">📦 المنتجات الملغاة</h3>
        <table class="items-table">
            <thead>
                <tr>
                    <th>المنتج</th>
                    <th style="text-align:center;">الكمية</th>
                    <th style="text-align:left;">السعر</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>
                        <span class="product-name">{{ $item->product_name }}</span>
                        @if($item->variant)
                            <span class="product-variant">{{ $item->variant }}</span>
                        @endif
                    </td>
                    <td style="text-align:center;">{{ $item->quantity }}</td>
                    <td style="text-align:left;">{{ number_format($item->price * $item->quantity, 2) }} ر.س</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="2" style="padding-top:25px;">الإجمالي المسترد</td>
                    <td style="text-align:left; padding-top:25px; color:#2d3748;">{{ number_format($order->total, 2) }} ر.س</td>
                </tr>
            </tbody>
        </table>

        <div class="refund-notice">
            <strong style="display:block; margin-bottom:5px; color:#c05621;">💡 ملاحظة الاسترجاع</strong>
            @if($order->payment_status === 'paid')
                سيتم استعادة المبلغ إلى حسابك البنكي خلال 5-7 أيام عمل. ستتلقى رسالة تأكيد فور إتمام العملية.
            @else
                هذا الطلب لم يتم دفع قيمته، لذا لا توجد مبالغ مستحقة للاسترداد.
            @endif
        </div>

        <div style="text-align: center; margin-top: 40px;">
            <a href="{{ url('/') }}" class="button">العودة للتسوق</a>
            <p class="support-text">
                هل لديك استفسار؟ <a href="mailto:support@example.com">تحدث مع الدعم الفني</a>
            </p>
        </div>
    </div>

    <div class="footer">
        <p>© {{ date('Y') }} متجرنا. جميع الحقوق محفوظة.</p>
        <div style="margin-top:10px;">
            <a href="{{ url('/terms') }}">الشروط والأحكام</a> |
            <a href="{{ url('/privacy') }}">سياسة الخصوصية</a>
        </div>
    </div>
</div>

</body>
</html>
