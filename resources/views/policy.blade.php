{{--
    ⚠️ Vue inutilisée depuis la mise en place des pages éditables.
    /policy est désormais rendu par resources/views/pages/show.blade.php
    et son contenu se modifie depuis l'admin : Espace admin → Pages du site.
    Fichier conservé à titre de référence uniquement.
--}}
@extends('layouts.app')

@section('title', __('Policy of Refund and Exchange'))

@section('content')
    <style>
        :root {
            --primary-color: #2E3192;
            --accent-color: #9E0168;
            --bg-color: #f9f9f9;
            --text-color: #444;
            --border-radius: 8px;
        }

        .policy-container {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 1000px;
            margin: 40px auto;
            padding: 30px;
            color: var(--text-color);
            line-height: 1.6;
        }

        h1 {
            color: var(--primary-color);
            text-align: center;
            border-bottom: 2px solid var(--bg-color);
            padding-bottom: 15px;
            margin-bottom: 30px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .policy-section {
            margin-bottom: 25px;
            padding: 15px;
            border-left: 4px solid var(--primary-color);
            background-color: #fcfcfc;
        }

        .no-return {
            border-left-color: var(--accent-color);
        }

        h2 {
            font-size: 1.2rem;
            margin-top: 0;
            color: var(--primary-color);
            display: flex;
            align-items: center;
        }

        .highlight {
            font-weight: bold;
            color: var(--primary-color);
        }

        ul {
            padding-left: 20px;
            margin: 10px 0;
        }

        li {
            margin-bottom: 8px;
        }

        .warranty-box {
            background-color: #eef2f7;
            padding: 20px;
            border-radius: var(--border-radius);
            margin-top: 30px;
            border: 1px dashed var(--primary-color);
        }

        .footer-note {
            font-size: 0.9rem;
            text-align: center;
            margin-top: 30px;
            color: #777;
        }

        .btn-link {
            color: var(--accent-color);
            text-decoration: underline;
            font-weight: bold;
        }
    </style>
    </head>

    <body>

        <div class="policy-container">
            <h1>@lang('message.policy_title')</h1>

            <div class="policy-section no-return">
                <h2>@lang('message.general_restrictions')</h2>
                <ul>
                    <li><strong>@lang('message.contact_lenses'):</strong> @lang('message.no_refund_exchange').</li>
                    <li><strong>@lang('message.medical_eyeglasses'):</strong> @lang('message.no_refund_exchange').</li>
                    <li><strong>@lang('message.eyeglasses_sunglasses'):</strong> @lang('message.no_refund_exchange'), <span
                            class="highlight">@lang('message.except')</span> @lang('message.factory_issues').</li>
                </ul>
            </div>

            <div class="policy-section">
                <h2>@lang('message.exchange_period_title')</h2>
                <p>@lang('message.exchange_period_desc', ['days' => 7])</p>
                <ul>
                    <li>@lang('message.original_condition')</li>
                    <li>@lang('message.original_box')</li>
                </ul>
                <p><strong>@lang('message.shipping_costs'):</strong> @lang('message.shipping_costs_desc', ['days' => 3])</p>
            </div>

            <div class="policy-section">
                <h2>@lang('message.request_replacement_title')</h2>
                <p>@lang('message.request_replacement_desc')</p>
            </div>

            <div class="warranty-box">
                <h2>@lang('message.warranty_title')</h2>
                <p>@lang('message.warranty_desc')</p>
                <ul>
                    <li>@lang('message.free_maintenance')</li>
                    <li><em>@lang('message.warranty_note')</em></li>
                </ul>
            </div>

            <p class="footer-note">@lang('message.footer_note')</p>
        </div>

    @endsection
