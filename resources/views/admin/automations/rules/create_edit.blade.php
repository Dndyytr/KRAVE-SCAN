<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold t-size7 font-heading text-text">
            {{ isset($rule) ? __('Edit Aturan Otomatisasi') : __('Buat Aturan Otomatisasi') }}
        </h2>
    </x-slot>

    <div class="max-w-3xl mx-auto anim-fade">
        <div class="mb-4">
            <a href="{{ route('admin.automations.rules.index') }}" class="inline-flex items-center gap-2 text-text-muted hover:text-text font-semibold t-size3 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                {{ __('Kembali ke Daftar Aturan') }}
            </a>
        </div>

        <div class="bg-card border border-border rounded-2xl p-6 shadow-xs" x-data="ruleForm()">
            <form method="POST" action="{{ isset($rule) ? route('admin.automations.rules.update', $rule->id) : route('admin.automations.rules.store') }}" class="space-y-6">
                @csrf
                @if(isset($rule))
                    @method('PUT')
                @endif

                <!-- Rule Name -->
                <div class="space-y-1.5">
                    <label for="name" class="block t-size3 font-bold text-text">{{ __('Nama Aturan') }}</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $rule->name ?? '') }}" required placeholder="Misal: Buat Struk QRIS Otomatis" class="w-full bg-surface border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-4 py-3 t-size4 outline-hidden transition">
                    @error('name')
                        <p class="text-danger t-size2 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Trigger Event -->
                    <div class="space-y-1.5">
                        <label for="trigger_event" class="block t-size3 font-bold text-text">{{ __('Event Pemicu (Trigger)') }}</label>
                        <select name="trigger_event" id="trigger_event" required class="w-full bg-surface border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-4 py-3 t-size4 outline-hidden transition cursor-pointer">
                            @foreach($triggers as $val => $label)
                                <option value="{{ $val }}" {{ old('trigger_event', $rule->trigger_event ?? '') === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('trigger_event')
                            <p class="text-danger t-size2 font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Action Job -->
                    <div class="space-y-1.5">
                        <label for="action_job" class="block t-size3 font-bold text-text">{{ __('Aksi Pekerjaan (Action Job)') }}</label>
                        <select name="action_job" id="action_job" required class="w-full bg-surface border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-4 py-3 t-size4 outline-hidden transition cursor-pointer">
                            @foreach($actions as $val => $label)
                                <option value="{{ $val }}" {{ old('action_job', $rule->action_job ?? '') === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('action_job')
                            <p class="text-danger t-size2 font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Condition Type -->
                <div class="space-y-1.5">
                    <label for="condition_type" class="block t-size3 font-bold text-text">{{ __('Tipe Kondisi') }}</label>
                    <select name="condition_type" id="condition_type" x-model="conditionType" required class="w-full bg-surface border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-4 py-3 t-size4 outline-hidden transition cursor-pointer">
                        @foreach($conditionTypes as $val => $label)
                            <option value="{{ $val }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('condition_type')
                        <p class="text-danger t-size2 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Dynamic Condition Values -->
                <!-- Payment Method Value -->
                <div class="space-y-1.5 bg-surface-alt/40 border border-border rounded-xl p-4 transition-all" x-show="conditionType === 'payment_method_equals'" x-collapse>
                    <label for="payment_method" class="block t-size3 font-bold text-text">{{ __('Pilih Metode Pembayaran') }}</label>
                    <select name="payment_method" id="payment_method" class="w-full bg-surface border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-4 py-3 t-size4 outline-hidden transition cursor-pointer">
                        <option value="cash" {{ old('payment_method', $rule->condition_value['payment_method'] ?? '') === 'cash' ? 'selected' : '' }}>{{ __('Cash / Tunai') }}</option>
                        <option value="qris" {{ old('payment_method', $rule->condition_value['payment_method'] ?? '') === 'qris' ? 'selected' : '' }}>{{ __('QRIS') }}</option>
                    </select>
                </div>

                <!-- Minimum Order Amount Value -->
                <div class="space-y-1.5 bg-surface-alt/40 border border-border rounded-xl p-4 transition-all" x-show="conditionType === 'min_order_amount'" x-collapse>
                    <label for="min_amount" class="block t-size3 font-bold text-text">{{ __('Minimal Total Belanja (Rupiah)') }}</label>
                    <input type="number" name="min_amount" id="min_amount" min="0" value="{{ old('min_amount', $rule->condition_value['min_amount'] ?? '0') }}" class="w-full bg-surface border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-4 py-3 t-size4 outline-hidden transition">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Branch Assignment (Super Admin Only) -->
                    @if($isSuperAdmin)
                        <div class="space-y-1.5">
                            <label for="branch_id" class="block t-size3 font-bold text-text">{{ __('Penugasan Cabang') }}</label>
                            <select name="branch_id" id="branch_id" required class="w-full bg-surface border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-4 py-3 t-size4 outline-hidden transition cursor-pointer">
                                <option value="" disabled>{{ __('Pilih Cabang') }}</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ old('branch_id', $rule->branch_id ?? '') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                @endforeach
                            </select>
                            @error('branch_id')
                                <p class="text-danger t-size2 font-semibold mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    <!-- Active State Checkbox -->
                    <div class="flex items-center pt-8">
                        <label class="relative inline-flex items-center cursor-pointer select-none">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', $rule->is_active ?? true) ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-surface-alt peer-focus:outline-hidden rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-border after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                            <span class="ml-3 t-size3 font-bold text-text">{{ __('Aktifkan Aturan Ini') }}</span>
                        </label>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-border">
                    <a href="{{ route('admin.automations.rules.index') }}" class="bg-surface border border-border hover:bg-border text-text-muted hover:text-text px-6 py-3 rounded-xl transition cursor-pointer text-center font-bold t-size3 shadow-2xs">
                        {{ __('Batal') }}
                    </a>
                    <button type="submit" class="bg-primary hover:bg-primary-strong text-white font-bold px-8 py-3 rounded-xl transition shadow-xs cursor-pointer t-size3">
                        {{ isset($rule) ? __('Simpan Perubahan') : __('Buat Aturan') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function ruleForm() {
            return {
                conditionType: '{{ old('condition_type', $rule->condition_type ?? 'always') }}'
            };
        }
    </script>
</x-app-layout>
