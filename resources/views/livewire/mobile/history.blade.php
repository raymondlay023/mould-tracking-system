<div class="space-y-4">
    {{-- Header --}}
    <div class="flex items-center gap-3 pb-2">
        <a href="{{ route('mobile.dashboard') }}" class="p-2 -ml-2 rounded-full hover:bg-slate-100 text-slate-500 active:bg-slate-200 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <h1 class="text-xl font-bold text-slate-900">Activity History</h1>
    </div>

    {{-- Timeline --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4">
        @if($activities->isEmpty())
            <div class="text-center py-10 text-slate-400">
                <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <p class="text-sm">No recent activity found.</p>
            </div>
        @else
            <div class="space-y-6 relative before:absolute before:inset-0 before:ml-5 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-slate-200 before:to-transparent">
                @foreach($activities as $activity)
                    <div class="relative flex items-start justify-between">
                        <!-- Icon -->
                        <div class="flex items-center justify-center w-10 h-10 rounded-full shadow shrink-0 z-10 {{ $activity['icon'] === 'wrench' ? 'bg-amber-100 text-amber-600' : 'bg-blue-100 text-blue-600' }}">
                            @if($activity['icon'] === 'wrench')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            @endif
                        </div>
                        
                        <!-- Content -->
                        <div class="ml-4 w-full">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="text-[10px] font-bold uppercase tracking-wider {{ $activity['icon'] === 'wrench' ? 'text-amber-500' : 'text-blue-500' }}">{{ $activity['type'] }}</div>
                                    <h3 class="font-bold text-slate-900 text-sm mt-0.5">{{ $activity['title'] }}</h3>
                                    <div class="text-xs font-medium text-slate-700 mt-1">{{ $activity['mould'] }}</div>
                                    <p class="text-xs text-slate-500 mt-0.5">{{ $activity['subtitle'] }}</p>
                                </div>
                                <div class="text-right shrink-0 ml-2">
                                    <div class="text-xs font-semibold text-slate-700">{{ \Carbon\Carbon::parse($activity['date'])->format('H:i') }}</div>
                                    <div class="text-[10px] text-slate-400">{{ \Carbon\Carbon::parse($activity['date'])->format('M d') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
