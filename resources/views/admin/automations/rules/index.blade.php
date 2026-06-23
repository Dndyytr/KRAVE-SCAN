<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <h2 class="font-bold t-size7 font-heading text-text">
                {{ __('Otomatisasi RPA') }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.automations.index') }}" class="px-4 py-2.5 rounded-xl t-size3 font-bold transition {{ request()->routeIs('admin.automations.index') ? 'bg-primary text-white shadow-xs' : 'bg-surface border border-border text-text-muted hover:text-text hover:bg-surface-alt' }}">
                    {{ __('Log Aktivitas') }}
                </a>
                <a href="{{ route('admin.automations.rules.index') }}" class="px-4 py-2.5 rounded-xl t-size3 font-bold transition {{ request()->routeIs('admin.automations.rules.index') ? 'bg-primary text-white shadow-xs' : 'bg-surface border border-border text-text-muted hover:text-text hover:bg-surface-alt' }}">
                    {{ __('Aturan & Pemicu') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6 anim-fade" x-data="rulesManager()">
        @if(session('success'))
            <div class="bg-success/10 border border-success/30 text-success rounded-xl p-4 t-size3 font-semibold">
                {{ session('success') }}
            </div>
        @endif

        <!-- Filter & Header Card -->
        <div class="bg-card border border-border rounded-2xl p-6 shadow-xs flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="space-y-1">
                <h3 class="font-bold t-size5 text-text font-heading">
                    {{ __('Mesin Aturan RPA (Automation Rules)') }}
                </h3>
                <p class="text-text-muted t-size3">
                    {{ __('Kelola kondisi pemicu otomatisasi bisnis (struk belanja, stok minimum, dll) secara dinamis.') }}
                </p>
            </div>
            <div>
                <a href="{{ route('admin.automations.rules.create') }}" class="bg-primary hover:bg-primary-strong text-white font-bold px-5 py-3 rounded-xl transition shadow-xs flex items-center justify-center gap-2 t-size3 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    {{ __('Buat Aturan Baru') }}
                </a>
            </div>
        </div>

        <!-- Rules Table Card -->
        <div class="bg-card border border-border rounded-2xl shadow-xs overflow-hidden">
            @if($rules->isEmpty())
                <div class="p-12 text-center space-y-3">
                    <div class="w-16 h-16 bg-surface-alt text-text-muted/60 rounded-full flex items-center justify-center mx-auto">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <div class="space-y-1">
                        <h3 class="font-bold t-size4 text-text">{{ __('Tidak Ada Aturan Otomatisasi') }}</h3>
                        <p class="text-text-muted t-size2 max-w-sm mx-auto">
                            {{ __('Belum ada aturan otomatisasi dinamis yang dibuat untuk cabang ini.') }}
                        </p>
                    </div>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-surface-alt border-b border-border text-text-muted t-size2 font-bold uppercase tracking-wider">
                                <th class="py-4 px-6">{{ __('Nama Aturan') }}</th>
                                <th class="py-4 px-6">{{ __('Pemicu (Trigger)') }}</th>
                                <th class="py-4 px-6">{{ __('Kondisi') }}</th>
                                <th class="py-4 px-6">{{ __('Aksi Job') }}</th>
                                <th class="py-4 px-6">{{ __('Cabang') }}</th>
                                <th class="py-4 px-6 text-center">{{ __('Status') }}</th>
                                <th class="py-4 px-6 text-right">{{ __('Aksi') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border t-size3 text-text">
                            @foreach($rules as $rule)
                                <tr class="hover:bg-surface/50 transition">
                                    <td class="py-4 px-6 font-bold text-text">
                                        {{ $rule->name }}
                                    </td>
                                    <td class="py-4 px-6 font-semibold whitespace-nowrap text-accent">
                                        {{ class_basename($rule->trigger_event) }}
                                        <span class="text-text-muted text-[10px] block font-normal">{{ $rule->trigger_event }}</span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="t-size2">
                                            @if($rule->condition_type === 'always')
                                                <span class="inline-flex px-2 py-0.5 rounded-md bg-info/10 text-info border border-info/20 font-bold uppercase text-[10px]">{{ __('Tanpa Kondisi') }}</span>
                                            @elseif($rule->condition_type === 'payment_method_equals')
                                                <span class="font-bold text-text">{{ __('Metode') }}:</span> <span class="bg-surface border border-border px-1.5 py-0.5 rounded text-mono font-bold">{{ strtoupper($rule->condition_value['payment_method'] ?? '') }}</span>
                                            @elseif($rule->condition_type === 'min_order_amount')
                                                <span class="font-bold text-text">{{ __('Min Belanja') }}:</span> <span class="text-accent font-bold">Rp{{ number_format($rule->condition_value['min_amount'] ?? 0, 0, ',', '.') }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-4 px-6 font-semibold text-text whitespace-nowrap">
                                        {{ class_basename($rule->action_job) }}
                                        <span class="text-text-muted text-[10px] block font-normal">{{ $rule->action_job }}</span>
                                    </td>
                                    <td class="py-4 px-6 whitespace-nowrap">
                                        {{ $rule->branch->name ?? __('Semua Cabang / HQ') }}
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <button 
                                            @click="toggleRule({{ $rule->id }})" 
                                            :class="states[{{ $rule->id }}] ? 'bg-success text-white' : 'bg-surface border border-border text-text-muted'"
                                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full t-size1 font-extrabold uppercase border cursor-pointer transition-colors shadow-2xs"
                                        >
                                            <span x-text="states[{{ $rule->id }}] ? '{{ __('Active') }}' : '{{ __('Inactive') }}'"></span>
                                        </button>
                                    </td>
                                    <td class="py-4 px-6 text-right whitespace-nowrap space-x-2">
                                        <a href="{{ route('admin.automations.rules.edit', $rule->id) }}" class="inline-block bg-surface border border-border hover:bg-border text-text-muted hover:text-text font-bold px-3 py-1.5 rounded-xl transition t-size2 cursor-pointer shadow-2xs">
                                            {{ __('Edit') }}
                                        </a>
                                        <form method="POST" action="{{ route('admin.automations.rules.destroy', $rule->id) }}" class="inline-block" onsubmit="return confirm('{{ __('Apakah Anda yakin ingin menghapus aturan otomatisasi ini?') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-danger/10 hover:bg-danger text-danger hover:text-white border border-danger/30 font-bold px-3 py-1.5 rounded-xl transition t-size2 cursor-pointer shadow-2xs">
                                                {{ __('Hapus') }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($rules->hasPages())
                    <div class="p-6 border-t border-border bg-surface-alt/30">
                        {{ $rules->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>

    <script>
        function rulesManager() {
            return {
                states: {
                    @foreach($rules as $rule)
                        {{ $rule->id }}: {{ $rule->is_active ? 'true' : 'false' }},
                    @endforeach
                },
                toggleRule(ruleId) {
                    const originalState = this.states[ruleId];
                    this.states[ruleId] = !originalState;

                    fetch(`/admin/automations/rules/${ruleId}/toggle`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.states[ruleId] = data.is_active;
                        } else {
                            this.states[ruleId] = originalState;
                            alert('Gagal memperbarui status aturan.');
                        }
                    })
                    .catch(error => {
                        this.states[ruleId] = originalState;
                        console.error('Error:', error);
                        alert('Terjadi kesalahan koneksi.');
                    });
                }
            };
        }
    </script>
</x-app-layout>
