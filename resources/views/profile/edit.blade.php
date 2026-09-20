@extends('layouts.app')

@section('title', 'Paramètres du Profil')

@section('content')
    <div class="bg-[#FBFCFE] min-h-screen py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mb-12 text-center">
            <h1 class="text-3xl font-light text-gray-900 tracking-tight">
                @lang('message.Profile Settings')
            </h1>
            <p class="text-gray-500 text-sm mt-2 max-w-md mx-auto">
                @lang('message.Manage your account information and security')
            </p>
        </div>

        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">

                <div class="bg-white border border-gray-100 rounded-2xl p-8 shadow-sm">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-10 h-10 bg-gray-50 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-gray-900">@lang('message.Personal Information')</h2>
                            <p class="text-xs text-gray-400 tracking-wide uppercase">@lang('message.Details & Contact')</p>
                        </div>
                    </div>

                    <div class="prose prose-sm max-w-none custom-form-wrapper">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                <div class="bg-white border border-gray-100 rounded-2xl p-8 shadow-sm">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-10 h-10 bg-gray-50 rounded-full flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-lock-fill" viewBox="0 0 16 16">
                                <path fill-rule="evenodd"
                                    d="M8 0a4 4 0 0 1 4 4v2.05a2.5 2.5 0 0 1 2 2.45v5a2.5 2.5 0 0 1-2.5 2.5h-7A2.5 2.5 0 0 1 2 13.5v-5a2.5 2.5 0 0 1 2-2.45V4a4 4 0 0 1 4-4m0 1a3 3 0 0 0-3 3v2h6V4a3 3 0 0 0-3-3" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-gray-900">@lang('message.Security')</h2>
                            <p class="text-xs text-gray-400 tracking-wide uppercase">@lang('message.Password & Protection')</p>
                        </div>
                    </div>

                    <div class="prose prose-sm max-w-none custom-form-wrapper">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>
            </div>

            <div class="bg-white border border-red-100 rounded-2xl overflow-hidden">
                <div class="px-8 py-4 bg-red-50/50 border-b border-red-50 flex justify-between items-center">
                    <h2 class="text-xs font-bold uppercase tracking-widest text-red-600">@lang('message.Danger Zone')</h2>
                    <span class="text-[10px] text-red-400 font-medium">@lang('message.Action irréversible')</span>
                </div>
                <div class="p-8">
                    <div class="max-w-xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>

        </div>
    </div>

    <style>
        /* Inputs raffinés */
        .custom-form-wrapper input {
            @apply w-full rounded-xl border-gray-100 bg-gray-50/30 px-4 py-2.5 text-sm transition-all duration-200 !important;
        }

        .custom-form-wrapper input:focus {
            @apply bg-white border-[#9E0168] ring-4 ring-[#9E0168]/5 outline-none !important;
        }

        /* Boutons stylisés */
        button[type="submit"] {
            @apply inline-flex items-center justify-center bg-gray-900 text-white px-8 py-3 rounded-xl text-xs font-bold uppercase tracking-widest hover:bg-[#9E0168] transform hover:-translate-y-0.5 transition-all duration-200 shadow-sm !important;
        }

        /* Labels */
        label {
            @apply text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 block !important;
        }
    </style>
@endsection
