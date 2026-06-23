<x-guest-layout>
    {{-- Session Status --}}
    <x-auth-session-status class="mb-4" :status="session('status')" />

    {{-- Login Header --}}
    <div class="mb-8 anim-fade" style="animation-delay: 0.2s;">
        <h2 class="t-size8 font-heading font-extrabold text-text leading-tight">
            {{ __('auth_page.login_title') }}
        </h2>
        <p class="t-size3 text-text-muted mt-2 font-medium leading-relaxed">
            {{ __('auth_page.login_subtitle') }}
        </p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        {{-- Email Field --}}
        <div class="mb-5 anim-slide-up" style="animation-delay: 0.25s;">
            <label for="email" class="block t-size3 font-semibold text-text mb-1.5">{{ __('auth_page.email') }}</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-text-muted">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                    </svg>
                </span>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                       placeholder="{{ __('auth_page.email_placeholder') }}"
                       class="w-full pl-12 pr-4 py-3 rounded-xl border border-input-border bg-input-bg text-text t-size4 font-medium placeholder:text-text-muted/60 focus:border-input-focus focus:ring-2 focus:ring-primary-soft/50 focus:outline-none transition-all duration-200">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        {{-- Password Field --}}
        <div class="mb-5 anim-slide-up" style="animation-delay: 0.3s;" x-data="{ showPassword: false }">
            <label for="password" class="block t-size3 font-semibold text-text mb-1.5">{{ __('auth_page.password') }}</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-text-muted">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                    </svg>
                </span>
                <input id="password" :type="showPassword ? 'text' : 'password'" name="password" required autocomplete="current-password"
                       placeholder="{{ __('auth_page.password_placeholder') }}"
                       class="w-full pl-12 pr-12 py-3 rounded-xl border border-input-border bg-input-bg text-text t-size4 font-medium placeholder:text-text-muted/60 focus:border-input-focus focus:ring-2 focus:ring-primary-soft/50 focus:outline-none transition-all duration-200">
                <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 flex items-center pr-4 text-text-muted hover:text-primary transition-colors cursor-pointer">
                    {{-- Eye icon (show) --}}
                    <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12c1.292 4.338 5.31 7.5 10.066 7.5.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/>
                    </svg>
                    {{-- Eye-off icon (hide) --}}
                    <svg x-show="showPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        {{-- Remember Me + Forgot Password --}}
        <div class="flex items-center justify-between mb-6 anim-slide-up" style="animation-delay: 0.35s;">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" name="remember"
                       class="w-4 h-4 rounded border-input-border text-primary focus:ring-primary-soft/50 transition-colors">
                <span class="ms-2 t-size3 text-text font-medium">{{ __('auth_page.remember_me') }}</span>
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="t-size3 text-primary hover:text-primary-strong font-semibold transition-colors">
                    {{ __('auth_page.forgot_password') }}
                </a>
            @endif
        </div>

        {{-- Login Button --}}
        <div class="anim-slide-up" style="animation-delay: 0.4s;">
            <button type="submit"
                    class="w-full py-3.5 rounded-xl text-white font-bold t-size5 tracking-wide shadow-md hover:shadow-lg transition-all duration-200 cursor-pointer"
                    style="background: linear-gradient(135deg, #e88ca2 0%, #d96b87 100%);">
                {{ __('auth_page.login_button') }}
            </button>
        </div>

        {{-- Divider --}}
        <div class="flex items-center gap-4 my-6 anim-fade" style="animation-delay: 0.45s;">
            <div class="flex-1 h-px bg-border"></div>
            <span class="t-size2 text-text-muted font-medium whitespace-nowrap">{{ __('auth_page.or_login_with') }}</span>
            <div class="flex-1 h-px bg-border"></div>
        </div>

        {{-- Google Login Button (cosmetic) --}}
        <div class="anim-slide-up" style="animation-delay: 0.5s;">
            <button type="button"
                    class="w-full py-3 rounded-xl border border-border bg-white hover:bg-surface text-primary-strong font-semibold t-size4 flex items-center justify-center gap-3 transition-all duration-200 cursor-pointer shadow-sm hover:shadow-md">
                {{-- Google Icon --}}
                <svg class="w-5 h-5" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                {{ __('auth_page.login_google') }}
            </button>
        </div>

        {{-- Contact Admin --}}
        <div class="text-center mt-6 anim-fade" style="animation-delay: 0.55s;">
            <p class="t-size3 text-text-muted font-medium">
                {{ __('auth_page.no_account') }}
                <a href="#" class="text-primary hover:text-primary-strong font-semibold transition-colors">{{ __('auth_page.contact_admin') }}</a>
            </p>
        </div>
    </form>
</x-guest-layout>
