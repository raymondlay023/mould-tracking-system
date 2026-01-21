<div class="relative" x-data="{ open: false }">
    {{-- Bell Icon --}}
    <button @click="open = !open" @click.outside="open = false" 
        class="relative p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-full focus:outline-none transition-colors"
        wire:poll.15s="updateCount"
    >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
        </svg>

        @if($unreadCount > 0)
            <span class="absolute top-1.5 right-1.5 flex h-3 w-3">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500 border border-white"></span>
            </span>
        @endif
    </button>

    {{-- Dropdown --}}
    <div x-show="open" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg ring-1 ring-black ring-opacity-5 z-50 origin-top-right overflow-hidden"
        style="display: none;"
    >
        <div class="px-4 py-3 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <h3 class="text-sm font-semibold text-gray-700">Notifications</h3>
            @if($unreadCount > 0)
                <button wire:click="markAllAsRead" class="text-xs text-blue-600 hover:text-blue-800 font-medium">Mark all read</button>
            @endif
        </div>

        <div class="max-h-80 overflow-y-auto divide-y divide-gray-100">
            @forelse($notifications as $n)
                <div class="p-4 hover:bg-gray-50 transition-colors {{ $n->read_at ? 'opacity-60' : '' }}">
                    <div wire:click="markAsRead('{{ $n->id }}')" class="cursor-pointer">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 mt-1">
                                @if($n->data['type'] === 'warning')
                                    <span class="h-2 w-2 rounded-full bg-red-500 block"></span>
                                @else
                                    <span class="h-2 w-2 rounded-full bg-blue-500 block"></span>
                                @endif
                            </div>
                            <div class="flex-1">
                                <p class="text-xs font-bold text-gray-900">{{ $n->data['title'] ?? 'Notification' }}</p>
                                <p class="text-xs text-gray-600 mt-0.5">{{ $n->data['body'] ?? '' }}</p>
                                <p class="text-[10px] text-gray-400 mt-2">{{ $n->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-gray-400 text-sm">
                    No new notifications
                </div>
            @endforelse
        </div>
        
        <div class="bg-gray-50 p-2 text-center border-t border-gray-100">
             {{-- Could add 'View All' page link here later --}}
        </div>
    </div>
</div>
