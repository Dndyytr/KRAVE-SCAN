<x-guest-layout>
    <x-alert />
    <header class="mb-11">
        <h2 class="font-heading text-[30px] font-extrabold leading-tight tracking-[-.025em] text-[#291815]">
            {{ __('auth_page.login_title') }}</h2>
        <p class="mt-3 max-w-[410px] text-[16px] font-medium leading-[1.55] text-[#5f5957]">
            {{ __('auth_page.login_subtitle') }}</p>
    </header>
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="mb-7">
            <label for="email" class="mb-2 block text-[14px] font-semibold text-[#302624]">{{ __('auth_page.email') }}</label>
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-5 text-[#ed7f97]"><svg class="size-6" fill="none"
                        stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4 7 8 6 8-6"></path>
                    </svg></span>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                    placeholder="{{ __('auth_page.email_placeholder') }}"
                    class="h-[60px] w-full rounded-xl border border-[#ded9d7] bg-white pl-[70px] pr-5 text-[15px] font-medium text-text placeholder:text-[#aaa7a5] focus:border-primary focus:ring-2 focus:ring-primary-soft/40">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
        <div class="mb-7" x-data="{ showPassword: false }">
            <label for="password" class="mb-2 block text-[14px] font-semibold text-[#302624]">{{ __('auth_page.password') }}</label>
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-5 text-[#ed7f97]"><svg class="size-6" fill="none"
                        stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <rect x="5" y="10" width="14" height="11" rx="2"></rect>
                        <path stroke-linecap="round" d="M8 10V7a4 4 0 0 1 8 0v3M12 14v3"></path>
                    </svg></span>
                <input id="password" :type="showPassword ? 'text' : 'password'" name="password" required autocomplete="current-password"
                    placeholder="{{ __('auth_page.password_placeholder') }}"
                    class="h-[60px] w-full rounded-xl border border-[#ded9d7] bg-white pl-[70px] pr-14 text-[15px] font-medium text-text placeholder:text-[#aaa7a5] focus:border-primary focus:ring-2 focus:ring-primary-soft/40">
                <button type="button" @click="showPassword = !showPassword" :aria-label="showPassword ? 'Sembunyikan password' : 'Tampilkan password'"
                    class="absolute inset-y-0 right-0 flex items-center pr-5 text-[#764d4d] hover:text-primary">
                    <svg x-show="!showPassword" class="size-6" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 3l18 18M10.6 10.6a2 2 0 0 0 2.8 2.8M9.9 4.2A10.9 10.9 0 0 1 12 4c5.5 0 9.5 4.5 10 8a10.6 10.6 0 0 1-2.2 4.4M6.2 6.2A10.8 10.8 0 0 0 2 12c.5 3.5 4.5 8 10 8 1.4 0 2.7-.3 3.8-.8">
                        </path>
                    </svg>
                    <svg x-cloak x-show="showPassword" class="size-6" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"
                        aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2 12c.5-3.5 4.5-8 10-8s9.5 4.5 10 8c-.5 3.5-4.5 8-10 8S2.5 15.5 2 12Z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>
        <div class="mb-8 flex items-center justify-between gap-4">
            <label for="remember_me" class="inline-flex cursor-pointer items-center"><input id="remember_me" type="checkbox" name="remember"
                    class="size-6 rounded-[5px] border-[#d8d3d1] text-primary focus:ring-primary-soft"><span
                    class="ms-3 text-[14px] font-medium text-[#4d4543]">{{ __('auth_page.remember_me') }}</span></label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}"
                    class="text-[14px] font-semibold text-[#ee8199] hover:text-primary-strong">{{ __('auth_page.forgot_password') }}</a>
            @endif
        </div>
        <button type="submit"
            class="h-[60px] w-full rounded-xl bg-gradient-to-r from-[#eb6f8c] to-[#e86686] text-[16px] font-bold text-white shadow-[0_9px_18px_rgba(232,108,137,.18)] hover:-translate-y-px hover:shadow-[0_12px_24px_rgba(232,108,137,.28)] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary">{{ __('auth_page.login_button') }}</button>
        <div class="my-10 flex items-center gap-5"><span class="h-px flex-1 bg-[#d9d4d1]"></span><span
                class="whitespace-nowrap t-size2 font-medium text-[#77716f]">{{ __('auth_page.or_login_with') }}</span><span
                class="h-px flex-1 bg-[#d9d4d1]"></span></div>
        <button type="button"
            class="flex h-[60px] w-full items-center justify-center gap-4 rounded-xl border border-[#f0c4cb] bg-white text-[16px] font-bold text-[#ed7f97] hover:border-primary hover:bg-[#fffafa]">
            <svg class="size-6" viewBox="0 0 24 24" aria-hidden="true">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1Z" />
                <path fill="#34A853"
                    d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84A11 11 0 0 0 12 23Z" />
                <path fill="#FBBC05"
                    d="M5.84 14.09A6.6 6.6 0 0 1 5.49 12c0-.73.13-1.43.35-2.09V7.07H2.18A11 11 0 0 0 1 12c0 1.78.43 3.45 1.18 4.93l3.66-2.84Z" />
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15A10.96 10.96 0 0 0 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53Z" />
            </svg>
            {{ __('auth_page.login_google') }}
        </button>
        <p class="mt-16 text-center text-[14px] font-medium text-[#514a48]">{{ __('auth_page.no_account') }} <a href="#"
                class="font-semibold text-[#ed7f97] hover:text-primary-strong">{{ __('auth_page.contact_admin') }}</a>
        </p>
    </form>
</x-guest-layout>
