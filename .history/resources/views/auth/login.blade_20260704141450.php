@extends('layouts.app')

@section('title', __('Login'))
@php
use Illuminate\Support\Facades\Route;
@endphp

@section('content')
<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
    <div class="w-full sm:max-w-md mt-6 px-6 py-4 overflow-hidden sm:rounded-lg">
        <div class="min-h-screen md:min-h-[85vh] flex flex-col justify-center items-center px-4 py-8">

            <!-- Session Status -->
            <x-auth-session-status
                class="mb-4 w-full max-w-md rounded-xl p-3 text-[10px] font-bold uppercase tracking-widest bg-gray-50 text-gray-900 border border-gray-100"
                :status="session('status')" />

            <div class="w-full max-w-md bg-white p-8 md:p-10 shadow-[0_10px_40px_rgba(0,0,0,0.03)] rounded-[1.5rem] border border-gray-50">

                <div class="text-center mb-6">
                    <h3 class="text-xl uppercase text-gray-900">@lang('message.Sign in')</h3>
                    <div class="mt-2 flex justify-center">
                        <div class="w-6 h-[1px] bg-black"></div>
                    </div>
                </div>

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="email"
                            class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5 text-start">
                            @lang('message.Email Address')
                        </label>
                        <input id="email" type="email" name="email" :value="old('email')" required autofocus
                            class="w-full px-4 py-3 bg-gray-50 border-none rounded-xl text-sm focus:ring-1 focus:ring-black transition-all placeholder:text-gray-300"
                            placeholder="email@exemple.com">
                        <x-input-error :messages="$errors->get('email')" class="mt-1 text-[11px] font-medium text-red-500" />
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-1.5">
                            <label for="password"
                                class="block text-[10px] font-bold uppercase tracking-widest text-gray-400">
                                @lang('message.Password')
                            </label>
                            @if (Route::has('password.request'))
                                <a class="text-[9px] font-bold uppercase tracking-widest text-gray-300 hover:text-black transition-colors"
                                    href="{{ route('password.request') }}">
                                    @lang('message.Forgot?')
                                </a>
                            @endif
                        </div>
                        <input id="password" type="password" name="password" required
                            class="w-full px-4 py-3 bg-gray-50 border-none rounded-xl text-sm focus:ring-1 focus:ring-black transition-all placeholder:text-gray-300"
                            placeholder="••••••••">
                        <x-input-error :messages="$errors->get('password')" class="mt-1 text-[11px] font-medium text-red-500" />
                    </div>

                    <div class="flex items-center justify-between py-1">
                        <label class="flex items-center">
                            <input id="remember_me" type="checkbox" name="remember"
                                class="w-4 h-4 rounded border-gray-200 text-black focus:ring-black">
                            <span class="ms-2 text-[11px] font-medium text-gray-500 uppercase tracking-tighter">
                                @lang('message.Remember me')
                            </span>
                        </label>
                    </div>

                    <button type="submit"
                        class="w-full py-3.5 bg-black text-white text-[12px] font-bold rounded-xl hover:bg-gray-800 transition-all shadow-md active:scale-[0.98]">
                        @lang('message.Log in')
                    </button>
                </form>

                <!-- OR Divider -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-100"></div>
                    </div>
                    <div class="relative flex justify-center text-[9px] font-bold uppercase tracking-widest">
                        <span class="bg-white px-4 text-gray-400">@lang('message.OR')</span>
                    </div>
                </div>

          {{--       <!-- Social Login -->
                <div class="space-y-3">
                    <a href="{{ route('login.google') }}"
                        class="flex items-center justify-center gap-3 w-full py-3 bg-white border border-gray-100 rounded-xl text-[13px] font-bold tracking-widest hover:bg-gray-50 transition-all">
                        <!-- Google logo -->
                        <svg class="h-5 w-5" viewBox="0 0 533.5 544.3">
                            <path fill="#4285F4"
                                d="M533.5 278.4c0-18.6-1.5-36.5-4.3-54H272v102.3h146.9c-6.3 33.6-25.5 62.1-54.5 81.2v67.3h88.1c51.7-47.6 81-118 81-196.8z" />
                            <path fill="#34A853"
                                d="M272 544.3c73.5 0 135-24.3 180-65.9l-88.1-67.3c-24.5 16.4-56 26-91.9 26-70.8 0-130.7-47.7-152.3-111.5H30.5v69.8C75.5 483.3 166.8 544.3 272 544.3z" />
                            <path fill="#FBBC05"
                                d="M119.7 312.4c-5.5-16.5-8.7-34.2-8.7-52.4s3.2-35.9 8.7-52.4V137.8H30.5C10.9 177.2 0 221.6 0 267.9s10.9 90.7 30.5 130.1l89.2-69.8z" />
                            <path fill="#EA4335"
                                d="M272 107.7c39.9 0 75.6 13.7 103.7 40.5l77.6-77.6C405.1 24.5 343.6 0 272 0 166.8 0 75.5 61 30.5 137.8l89.2 69.8c21.6-63.8 81.5-111.5 152.3-111.5z" />
                        </svg>
                        Sign in with Google
                    </a>
                </div> --}}

                <!-- Register Link -->
                <div class="mt-6 text-center">
                    <p class="text-[11px] text-gray-400 font-medium">
                        @lang("message.Don't have an account?")
                        <a href="{{ route('register') }}"
                            class="text-black font-bold border-b border-black ms-1 pb-0.5 transition-colors">
                            @lang('message.Register')
                        </a>
                    </p>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
