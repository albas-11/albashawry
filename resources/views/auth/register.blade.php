@extends('layouts.app')

@section('title', __('message.Register'))

@section('content')
<div class="flex flex-col justify-center items-center pt-6 sm:pt-0 bg-gray-100 min-h-[calc(100vh-4rem)]">
    <div class="w-full sm:max-w-md mt-6 px-6 py-4 overflow-hidden sm:rounded-lg">
        <div class="flex flex-col justify-center items-center px-4 py-6 md:min-h-[85vh]">
            <div class="w-full max-w-md bg-white p-6 md:p-10 shadow-[0_10px_40px_rgba(0,0,0,0.03)] rounded-[1.5rem] border border-gray-50">

                <div class="text-center mb-6">
                    <h3 class="text-xl font-light uppercase tracking-[0.2em] text-gray-900">@lang('message.Register')</h3>
                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-1">
                        @lang('message.Join our community')</p>
                    <div class="mt-3 flex justify-center">
                        <div class="w-6 h-[1px] bg-black"></div>
                    </div>
                </div>

                <form method="POST" action="{{ route('register') }}" class="space-y-3.5">
                    @csrf

                    <div>
                        <label for="name" class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1 text-start">
                            @lang('message.Full Name')
                        </label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                               class="w-full px-4 py-3 bg-gray-50 border-none rounded-xl text-sm focus:ring-1 focus:ring-black transition-all placeholder:text-gray-300"
                               placeholder="John Doe">
                        <x-input-error :messages="$errors->get('name')" class="mt-1 text-[10px] font-medium text-red-500" />
                    </div>

                    <div>
                        <label for="email" class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1 text-start">
                            @lang('message.Email Address')
                        </label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required
                               class="w-full px-4 py-3 bg-gray-50 border-none rounded-xl text-sm focus:ring-1 focus:ring-black transition-all placeholder:text-gray-300"
                               placeholder="email@exemple.com">
                        <x-input-error :messages="$errors->get('email')" class="mt-1 text-[10px] font-medium text-red-500" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label for="password" class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1 text-start">
                                @lang('message.Password')
                            </label>
                            <input id="password" type="password" name="password" required
                                   class="w-full px-4 py-3 bg-gray-50 border-none rounded-xl text-sm focus:ring-1 focus:ring-black transition-all"
                                   placeholder="••••••••">
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1 text-start">
                                @lang('message.Confirm')
                            </label>
                            <input id="password_confirmation" type="password" name="password_confirmation" required
                                   class="w-full px-4 py-3 bg-gray-50 border-none rounded-xl text-sm focus:ring-1 focus:ring-black transition-all"
                                   placeholder="••••••••">
                        </div>
                    </div>

                    <x-input-error :messages="$errors->get('password')" class="mt-1 text-[10px] font-medium text-red-500" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-[10px] font-medium text-red-500" />

                    <div class="py-1">
                        <p class="text-[9px] text-red-500 leading-snug text-center">
                            @lang('message.By registering, you agree to our Terms & Conditions and Privacy Policy.')
                        </p>
                    </div>

                    <button type="submit" class="w-full py-3.5 bg-black text-white text-[10px] font-bold uppercase tracking-[0.2em] rounded-xl hover:bg-gray-800 transition-all shadow-md active:scale-[0.98]">
                        @lang('message.Create Account')
                    </button>
                </form>

                <div class="mt-6 text-center pt-5 border-t border-gray-50">
                    <p class="text-[11px] text-gray-400 font-medium">
                        @lang('message.Already registered?')
                        <a class="text-black font-bold border-b border-black ms-1 pb-0.5 transition-colors"
                           href="{{ route('login') }}">
                            @lang('message.Log in')
                        </a>
                    </p>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
