<div class="space-y-6">
    {{-- Greeting --}}
    <div>
        <h1 class="text-xl font-bold text-gray-900">Hello, {{ auth()->user()->name }}</h1>
        <p class="text-sm text-gray-500">Ready for production?</p>
    </div>

    {{-- Quick Actions --}}
    <div class="grid grid-cols-2 gap-4">
        <a href="{{ route('mobile.scanner') }}" class="bg-blue-600 text-white p-4 rounded-xl shadow-md flex flex-col items-center justify-center gap-2 active:scale-95 transition-transform">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 16h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
            <span class="font-medium">Scan QR</span>
        </a>
        <button class="bg-white border border-gray-200 text-gray-700 p-4 rounded-xl shadow-sm flex flex-col items-center justify-center gap-2 active:scale-95 transition-transform">
            <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="font-medium">History</span>
        </button>
    </div>

    {{-- Active Runs --}}
    <div>
        <h2 class="text-sm font-bold text-gray-900 mb-3 uppercase tracking-wider">Active Floor Activity</h2>
        
        <div class="space-y-3">
            @forelse($myActiveRuns as $run)
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex justify-between items-center">
                    <div>
                        <div class="font-bold text-gray-900">{{ $run->mould->code }}</div>
                        <div class="text-xs text-gray-500">{{ $run->machine->code }} • {{ $run->operator_name ?? 'Unknown' }}</div>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex items-center px-2 py-1 rounded bg-green-100 text-green-700 text-xs font-bold">
                            RUNNING
                        </span>
                        <div class="text-xs text-gray-400 mt-1">{{ $run->start_ts->format('H:i') }}</div>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-gray-400 bg-white rounded-xl border border-dashed border-gray-200">
                    <p class="text-sm">No active runs.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
