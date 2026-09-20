@extends('layouts.app')

@section('title', __('message.Reset Password'))

@section('content')
<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
    <div class="w-full sm:max-w-md mt-6 px-6 py-4 overflow-hidden sm:rounded-lg">
        <div class="min-h-screen md:min-h-[85vh] flex flex-col justify-center items-center px-4 py-8">

            <div class="w-full max-w-md bg-white p-8 md:p-10 shadow-[0_10px_40px_rgba(0,0,0,0.03)] rounded-[1.5rem] border border-gray-50">

                <div class="text-center mb-6">
                    <h3 class="text-xl uppercase text-gray-900">@lang('message.Reset Password')</h3>
                    <div class="mt-2 flex justify-center">
                        <div class="w-6 h-[1px] bg-black"></div>
                    </div>
                    <p class="mt-4 text-[13px] text-gray-500 leading-relaxed">
                        @lang('message.Reset password message')
                    </p>
                </div>

                <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
                    @csrf

                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <div>
                        <label for="email"
                            class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5 text-start">
                            @lang('message.Email Address')
                        </label>
                        <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username"
                            class="w-full px-4 py-3 bg-gray-50 border-none rounded-xl text-sm focus:ring-1 focus:ring-black transition-all placeholder:text-gray-300"
                            placeholder="email@exemple.com">
                        <x-input-error :messages="$errors->get('email')" class="mt-1 text-[11px] font-medium text-red-500" />
                    </div>

                    <div>
                        <label for="password"
                            class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5 text-start">
                            @lang('message.New Password')
                        </label>
                        <input id="password" type="password" name="password" required autocomplete="new-password"
                            class="w-full px-4 py-3 bg-gray-50 border-none rounded-xl text-sm focus:ring-1 focus:ring-black transition-all placeholder:text-gray-300"
                            placeholder="••••••••">
                        <x-input-error :messages="$errors->get('password')" class="mt-1 text-[11px] font-medium text-red-500" />
                    </div>

                    <div>
                        <label for="password_confirmation"
                            class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5 text-start">
                            @lang('message.Confirm Password')
                        </label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                            class="w-full px-4 py-3 bg-gray-50 border-none rounded-xl text-sm focus:ring-1 focus:ring-black transition-all placeholder:text-gray-300"
                            placeholder="••••••••">
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-[11px] font-medium text-red-500" />
                    </div>

                    <button type="submit"
                        class="w-full py-3.5 bg-black text-white text-[12px] font-bold rounded-xl hover:bg-gray-800 transition-all shadow-md active:scale-[0.98]">
                        @lang('message.Reset Password')
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <a href="{{ route('login') }}"
                        class="text-[11px] text-gray-400 font-medium hover:text-black transition-colors">
                        @lang('message.Back to Sign in')
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
