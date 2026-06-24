<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold t-size7 font-heading text-text">
            {{ __('Audit & Log Aktivitas') }}
        </h2>
    </x-slot>

    <div class="space-y-6 anim-fade" x-data="{
        showDetailModal: false,
        selectedLog: null,
        openModal(log) {
            this.selectedLog = log;
            this.showDetailModal = true;
        }
    }">
        <!-- Filter Panel -->
        <div class="bg-card border border-border rounded-2xl p-6 shadow-xs space-y-4">
            <div>
                <h3 class="font-bold t-size5 text-text font-heading">
                    Jejak Audit Aktivitas Pengguna
                </h3>
                <p class="text-text-muted t-size3 mt-0.5">
                    Pantau semua aktivitas pengguna, log masuk/keluar, dan riwayat perubahan data penting pada sistem.
                </p>
            </div>

            <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3 pt-2">
                <!-- User Filter -->
                <div>
                    <label class="block text-text-muted t-size2 font-semibold mb-1.5">Pengguna</label>
                    <select name="user_id" class="w-full bg-card border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-3 py-2.5 t-size4 outline-hidden transition cursor-pointer">
                        <option value="">Semua Pengguna</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                                {{ $u->name }} ({{ ucfirst($u->role?->name ?? 'Staff') }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Action Filter -->
                <div>
                    <label class="block text-text-muted t-size2 font-semibold mb-1.5">Aksi</label>
                    <select name="action" class="w-full bg-card border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-3 py-2.5 t-size4 outline-hidden transition cursor-pointer">
                        <option value="">Semua Aksi</option>
                        @foreach($actions as $act)
                            <option value="{{ $act }}" {{ request('action') === $act ? 'selected' : '' }}>
                                {{ ucfirst($act) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Branch Filter (Only Super Admin) -->
                @if(auth()->user()->branch_id === null)
                    <div>
                        <label class="block text-text-muted t-size2 font-semibold mb-1.5">Cabang</label>
                        <select name="branch_id" class="w-full bg-card border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-3 py-2.5 t-size4 outline-hidden transition cursor-pointer">
                            <option value="">Semua Cabang</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <div>
                        <label class="block text-text-muted t-size2 font-semibold mb-1.5">Cabang</label>
                        <input type="text" readonly value="{{ auth()->user()->branch?->name }}" class="w-full bg-surface border border-border rounded-xl px-3 py-2.5 t-size4 outline-hidden text-text-muted cursor-not-allowed">
                    </div>
                @endif

                <!-- Date From -->
                <div>
                    <label class="block text-text-muted t-size2 font-semibold mb-1.5">Dari Tanggal</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full bg-card border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-3 py-2 t-size4 outline-hidden transition">
                </div>

                <!-- Date To -->
                <div>
                    <label class="block text-text-muted t-size2 font-semibold mb-1.5">Hingga Tanggal</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full bg-card border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-3 py-2 t-size4 outline-hidden transition">
                </div>

                <!-- Buttons -->
                <div class="sm:col-span-2 md:col-span-5 flex justify-end gap-2 pt-2">
                    @if(request()->anyFilled(['user_id', 'action', 'branch_id', 'date_from', 'date_to']))
                        <a href="{{ route('admin.activity-logs.index') }}" class="bg-surface border border-border hover:bg-border text-text-muted hover:text-text px-5 py-2.5 rounded-xl transition cursor-pointer text-center flex items-center justify-center font-bold">
                            Reset
                        </a>
                    @endif
                    <button type="submit" class="bg-primary hover:bg-primary-strong text-white font-bold px-6 py-2.5 rounded-xl transition shadow-xs cursor-pointer">
                        Terapkan Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Logs Table -->
        <div class="bg-card border border-border rounded-2xl shadow-xs overflow-hidden">
            @if($logs->isEmpty())
                <div class="py-12 text-center text-text-muted t-size4 font-semibold">
                    Tidak ada log aktivitas yang ditemukan.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-surface-alt border-b border-border text-text-muted t-size2 font-bold uppercase tracking-wider">
                                <th class="py-3 px-6">Waktu</th>
                                <th class="py-3 px-6">Pengguna</th>
                                <th class="py-3 px-6">Cabang</th>
                                <th class="py-3 px-6">Aksi</th>
                                <th class="py-3 px-6">Target Entitas</th>
                                <th class="py-3 px-6">IP Address</th>
                                <th class="py-3 px-6 text-right w-32">Detail</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @foreach($logs as $log)
                                <tr class="hover:bg-surface/30 transition">
                                    <!-- Timestamp -->
                                    <td class="py-4 px-6 text-text t-size3 font-medium whitespace-nowrap">
                                        {{ $log->created_at->format('Y-m-d H:i:s') }}
                                    </td>
                                    <!-- User -->
                                    <td class="py-4 px-6 text-text t-size3">
                                        @if($log->user)
                                            <span class="font-bold text-text">{{ $log->user->name }}</span>
                                            <span class="block t-size1 text-text-muted">ID: {{ $log->user_id }} - {{ ucfirst($log->user->role?->name ?? 'Staff') }}</span>
                                        @else
                                            <span class="text-text-muted italic">Sistem (Otomatis)</span>
                                        @endif
                                    </td>
                                    <!-- Branch -->
                                    <td class="py-4 px-6 text-text t-size3">
                                        {{ $log->branch?->name ?: 'Super Admin (Global)' }}
                                    </td>
                                    <!-- Action badge -->
                                    <td class="py-4 px-6 whitespace-nowrap">
                                        @if($log->action === 'created')
                                            <span class="bg-success-soft/30 text-success font-bold px-2.5 py-0.5 rounded-full t-size1 border border-success-soft/60">
                                                Created
                                            </span>
                                        @elseif($log->action === 'updated')
                                            <span class="bg-warning-soft/30 text-warning font-bold px-2.5 py-0.5 rounded-full t-size1 border border-warning-soft/60">
                                                Updated
                                            </span>
                                        @elseif($log->action === 'deleted')
                                            <span class="bg-danger-soft/30 text-danger font-bold px-2.5 py-0.5 rounded-full t-size1 border border-danger-soft/60">
                                                Deleted
                                            </span>
                                        @elseif($log->action === 'login')
                                            <span class="bg-info-soft/30 text-info font-bold px-2.5 py-0.5 rounded-full t-size1 border border-info-soft/60">
                                                Login
                                            </span>
                                        @elseif($log->action === 'logout')
                                            <span class="bg-text-muted/10 text-text-muted font-bold px-2.5 py-0.5 rounded-full t-size1 border border-text-muted/20">
                                                Logout
                                            </span>
                                        @else
                                            <span class="bg-text-muted/10 text-text-muted font-bold px-2.5 py-0.5 rounded-full t-size1 border border-text-muted/20">
                                                {{ ucfirst($log->action) }}
                                            </span>
                                        @endif
                                    </td>
                                    <!-- Target Entity -->
                                    <td class="py-4 px-6 text-text-muted t-size3">
                                        @if($log->loggable_type)
                                            <span class="font-semibold text-text">{{ class_basename($log->loggable_type) }}</span>
                                            <span class="t-size2"> (ID: {{ $log->loggable_id }})</span>
                                        @else
                                            <span class="italic text-text-muted">-</span>
                                        @endif
                                    </td>
                                    <!-- IP -->
                                    <td class="py-4 px-6 text-text-muted t-size3 font-mono">
                                        {{ $log->ip_address ?: '-' }}
                                    </td>
                                    <!-- Detail Action -->
                                    <td class="py-4 px-6 text-right">
                                        @if($log->old_values || $log->new_values)
                                            <button @click="openModal({{ json_encode($log) }})" class="text-accent hover:text-primary font-bold t-size3 transition cursor-pointer">
                                                Inspeksi
                                            </button>
                                        @else
                                            <span class="text-text-muted/40 font-bold t-size3 cursor-not-allowed select-none">Tidak ada</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-border">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>

        <!-- Inspect details modal -->
        <div x-show="showDetailModal"
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4"
             style="display: none;"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">
            <div class="bg-card border border-border rounded-2xl max-w-3xl w-full p-6 shadow-lg space-y-4" @click.away="showDetailModal = false">
                <div class="flex justify-between items-center border-b border-border pb-3">
                    <h3 class="font-bold t-size5 text-text font-heading">
                        Inspeksi Perubahan Data
                    </h3>
                    <button @click="showDetailModal = false" class="text-text-muted hover:text-text cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-[60vh] overflow-y-auto pt-2">
                    <!-- Old values -->
                    <div class="space-y-2">
                        <h4 class="font-bold t-size3 text-danger flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-danger"></span>
                            Sebelum (Old Values)
                        </h4>
                        <div class="bg-surface border border-border rounded-xl p-4 overflow-x-auto">
                            <template x-if="selectedLog && selectedLog.old_values">
                                <pre class="font-mono t-size2 text-text text-left leading-relaxed"><code x-text="JSON.stringify(selectedLog.old_values, null, 2)"></code></pre>
                            </template>
                            <template x-if="!selectedLog || !selectedLog.old_values">
                                <span class="text-text-muted italic t-size3">Tidak ada data sebelumnya (Baru dibuat).</span>
                            </template>
                        </div>
                    </div>

                    <!-- New values -->
                    <div class="space-y-2">
                        <h4 class="font-bold t-size3 text-success flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-success"></span>
                            Sesudah (New Values)
                        </h4>
                        <div class="bg-surface border border-border rounded-xl p-4 overflow-x-auto">
                            <template x-if="selectedLog && selectedLog.new_values">
                                <pre class="font-mono t-size2 text-text text-left leading-relaxed"><code x-text="JSON.stringify(selectedLog.new_values, null, 2)"></code></pre>
                            </template>
                            <template x-if="!selectedLog || !selectedLog.new_values">
                                <span class="text-text-muted italic t-size3">Tidak ada data sesudahnya (Dihapus).</span>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Info metadata -->
                <div class="bg-surface border border-border rounded-xl p-3 text-text-muted t-size2 space-y-1">
                    <div>
                        <span class="font-semibold">User Agent:</span>
                        <span x-text="selectedLog ? (selectedLog.user_agent || '-') : '-'"></span>
                    </div>
                    <div>
                        <span class="font-semibold">Target Class:</span>
                        <span x-text="selectedLog ? (selectedLog.loggable_type || '-') : '-'"></span>
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <button @click="showDetailModal = false" class="bg-surface border border-border text-text hover:bg-border px-5 py-2.5 rounded-xl transition cursor-pointer font-bold t-size4">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
