<div x-data="{
    open: false,
    unreadCount: 0,
    notifications: [],
    fetchNotifications() {
        fetch('{{ route('api.notifications.index') }}')
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    this.unreadCount = data.unread_count;
                    this.notifications = data.notifications;
                }
            })
            .catch(err => console.error('Error fetching notifications:', err));
    },
    markAsRead(item) {
        fetch(`/api/notifications/${item.id}/mark-as-read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                this.fetchNotifications();
                if (item.redirect_url && item.redirect_url !== '#') {
                    window.location.href = item.redirect_url;
                }
            }
        })
        .catch(err => console.error('Error marking notification as read:', err));
    },
    markAllAsRead() {
        fetch('{{ route('api.notifications.mark-all-as-read') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                this.unreadCount = 0;
                this.notifications = [];
            }
        })
        .catch(err => console.error('Error marking all as read:', err));
    }
}" x-init="fetchNotifications(); setInterval(() => fetchNotifications(), 10000)" class="relative">
    
    <!-- Bell Button -->
    <button @click="open = !open" @click.outside="open = false" class="relative p-2 text-text-muted hover:text-text hover:bg-surface rounded-full transition focus:outline-none cursor-pointer">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
        </svg>
        <template x-if="unreadCount > 0">
            <span class="absolute top-1 right-1 flex h-4 w-4">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-danger opacity-75"></span>
                <span class="relative inline-flex rounded-full h-4 w-4 bg-danger text-[9px] font-bold text-white items-center justify-center" x-text="unreadCount"></span>
            </span>
        </template>
    </button>

    <!-- Dropdown Panel -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute right-0 mt-2 w-80 bg-card border border-border rounded-2xl shadow-lg z-50 overflow-hidden" 
         style="display: none;">
        
        <div class="px-4 py-3 border-b border-border flex items-center justify-between bg-surface-alt">
            <span class="font-bold text-text t-size4">Notifikasi</span>
            <button x-show="unreadCount > 0" @click="markAllAsRead()" class="text-accent hover:text-accent/80 font-semibold t-size2 transition focus:outline-none cursor-pointer">
                Tandai semua dibaca
            </button>
        </div>

        <div class="max-h-64 overflow-y-auto divide-y divide-border">
            <template x-if="notifications.length === 0">
                <div class="px-4 py-6 text-center text-text-muted t-size3">
                    Tidak ada notifikasi baru
                </div>
            </template>
            <template x-for="item in notifications" :key="item.id">
                <div class="p-4 hover:bg-surface transition flex flex-col gap-1 cursor-pointer text-left" @click="markAsRead(item)">
                    <div class="flex items-start justify-between gap-2">
                        <span class="t-size3 font-medium text-text" x-text="item.message"></span>
                    </div>
                    <span class="t-size1 text-text-muted" x-text="item.created_at"></span>
                </div>
            </template>
        </div>
    </div>
</div>
