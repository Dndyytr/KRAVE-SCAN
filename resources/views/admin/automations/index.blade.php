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

    <div class="space-y-6 anim-fade">
        <!-- Filter Card -->
        <div class="bg-card border border-border rounded-2xl p-6 shadow-xs space-y-4">
            <div>
                <h3 class="font-bold t-size5 text-text font-heading">
                    {{ __('Filter Aktivitas Robot RPA') }}
                </h3>
                <p class="text-text-muted t-size3 mt-0.5">
                    {{ __('Pantau seluruh proses otomatisasi sistem, background jobs, dan penjadwalan harian.') }}
                </p>
            </div>

            <form method="GET" action="{{ route('admin.automations.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 pt-2">
                <!-- Task Name Filter -->
                <div class="space-y-1">
                    <label for="task_name" class="block t-size2 font-semibold text-text-muted">{{ __('Nama Tugas') }}</label>
                    <select name="task_name" id="task_name" class="w-full bg-card border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-3 py-2.5 t-size4 outline-hidden transition cursor-pointer">
                        <option value="">{{ __('Semua Tugas') }}</option>
                        <option value="Generate Receipt" {{ request('task_name') === 'Generate Receipt' ? 'selected' : '' }}>{{ __('Generate Receipt') }}</option>
                        <option value="Low Stock Warning" {{ request('task_name') === 'Low Stock Warning' ? 'selected' : '' }}>{{ __('Low Stock Warning') }}</option>
                        <option value="Auto Cancel Order" {{ request('task_name') === 'Auto Cancel Order' ? 'selected' : '' }}>{{ __('Auto Cancel Order') }}</option>
                        <option value="Aggregate Daily Reports" {{ request('task_name') === 'Aggregate Daily Reports' ? 'selected' : '' }}>{{ __('Aggregate Daily Reports') }}</option>
                    </select>
                </div>

                <!-- Status Filter -->
                <div class="space-y-1">
                    <label for="status" class="block t-size2 font-semibold text-text-muted">{{ __('Status') }}</label>
                    <select name="status" id="status" class="w-full bg-card border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-3 py-2.5 t-size4 outline-hidden transition cursor-pointer">
                        <option value="">{{ __('Semua Status') }}</option>
                        <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>{{ __('Success') }}</option>
                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>{{ __('Failed') }}</option>
                        <option value="warning" {{ request('status') === 'warning' ? 'selected' : '' }}>{{ __('Warning') }}</option>
                    </select>
                </div>

                <!-- Branch Filter (Super Admin only) -->
                @if($isSuperAdmin)
                    <div class="space-y-1">
                        <label for="branch_id" class="block t-size2 font-semibold text-text-muted">{{ __('Cabang') }}</label>
                        <select name="branch_id" id="branch_id" class="w-full bg-card border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-3 py-2.5 t-size4 outline-hidden transition cursor-pointer">
                            <option value="">{{ __('Semua Cabang') }}</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="{{ $isSuperAdmin ? 'md:col-span-1' : 'md:col-span-2' }} flex items-end justify-end gap-3 pb-0.5">
                    <button type="submit" class="bg-primary hover:bg-primary-strong text-white font-bold px-6 py-2.5 rounded-xl transition shadow-xs cursor-pointer w-full md:w-auto">
                        {{ __('Filter') }}
                    </button>
                    @if(request()->anyFilled(['task_name', 'status', 'branch_id']))
                        <a href="{{ route('admin.automations.index') }}" class="bg-surface border border-border hover:bg-border text-text-muted hover:text-text px-6 py-2.5 rounded-xl transition cursor-pointer text-center flex items-center justify-center font-bold">
                            {{ __('Reset') }}
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Logs Table Card -->
        <div class="bg-card border border-border rounded-2xl shadow-xs overflow-hidden">
            @if($logs->isEmpty())
                <div class="p-12 text-center space-y-3">
                    <div class="w-16 h-16 bg-surface-alt text-text-muted/60 rounded-full flex items-center justify-center mx-auto">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                        </svg>
                    </div>
                    <div class="space-y-1">
                        <h3 class="font-bold t-size4 text-text">{{ __('Tidak Ada Log Otomatisasi') }}</h3>
                        <p class="text-text-muted t-size2 max-w-sm mx-auto">
                            {{ __('Belum ada riwayat aktivitas RPA otomatisasi yang tercatat saat ini.') }}
                        </p>
                    </div>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-surface-alt border-b border-border text-text-muted t-size2 font-bold uppercase tracking-wider">
                                <th class="py-4 px-6">{{ __('Waktu Eksekusi') }}</th>
                                <th class="py-4 px-6">{{ __('Nama Tugas') }}</th>
                                <th class="py-4 px-6">{{ __('Cabang') }}</th>
                                <th class="py-4 px-6">{{ __('Status') }}</th>
                                <th class="py-4 px-6">{{ __('Detail Payload') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border t-size3 text-text">
                            @foreach($logs as $log)
                                <tr class="hover:bg-surface/50 transition">
                                    <td class="py-4 px-6 font-semibold whitespace-nowrap">
                                        {{ $log->created_at->format('d M Y, H:i:s') }}
                                        <span class="text-text-muted text-[10px] block font-normal">{{ $log->created_at->diffForHumans() }}</span>
                                    </td>
                                    <td class="py-4 px-6 font-bold text-accent whitespace-nowrap">
                                        {{ $log->task_name }}
                                    </td>
                                    <td class="py-4 px-6 whitespace-nowrap">
                                        {{ $log->branch->name ?? __('Semua Cabang / HQ') }}
                                    </td>
                                    <td class="py-4 px-6 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full t-size1 font-extrabold uppercase border
                                            @if($log->status === 'success') bg-success/10 text-success border-success/30
                                            @elseif($log->status === 'warning') bg-warning/10 text-warning border-warning/30
                                            @else bg-danger/10 text-danger border-danger/30
                                            @endif">
                                            {{ $log->status }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 min-w-[300px]">
                                        <div class="bg-surface border border-border rounded-lg p-2 font-mono text-[11px] max-h-32 overflow-y-auto select-all">
                                            @php
                                                $decodedDetails = json_decode($log->details, true);
                                            @endphp
                                            @if(is_array($decodedDetails))
                                                <ul class="space-y-0.5">
                                                    @foreach($decodedDetails as $key => $value)
                                                        <li>
                                                            <strong class="text-text-muted">{{ $key }}:</strong> 
                                                            @if(is_array($value))
                                                                <span class="text-accent">{{ json_encode($value) }}</span>
                                                            @else
                                                                <span class="text-text">{{ $value }}</span>
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                {{ $log->details }}
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($logs->hasPages())
                    <div class="p-6 border-t border-border bg-surface-alt/30">
                        {{ $logs->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-app-layout>
