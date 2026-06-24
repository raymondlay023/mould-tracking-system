<div class="space-y-6">
    {{-- Greeting --}}
    <div>
        <h1 class="text-xl font-bold text-slate-900">Hello, {{ auth()->user()->name }}</h1>
        <p class="text-sm text-slate-500">Ready for production?</p>
    </div>

    {{-- Quick Actions --}}
    <div class="grid grid-cols-2 gap-4">
        <a href="{{ route('mobile.scanner') }}" class="bg-blue-600 text-white p-4 rounded-xl shadow-md flex flex-col items-center justify-center gap-2 active:scale-95 transition-transform">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 16h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
            <span class="font-medium">Scan QR</span>
        </a>
        <a href="{{ route('mobile.history') }}" class="bg-white border border-slate-200 text-slate-700 p-4 rounded-xl shadow-sm flex flex-col items-center justify-center gap-2 active:scale-95 transition-transform">
            <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="font-medium">History</span>
        </a>
    </div>

    @php
        // A technician whose primary role is maintenance will prefer Work Orders on top
        $isMaintenanceOnly = auth()->user()->can('maintenance.view') && !auth()->user()->can('production.view');
    @endphp

    <div class="flex flex-col gap-6">
        {{-- Active Runs --}}
        <div class="{{ $isMaintenanceOnly ? 'order-2' : 'order-1' }}">
            <h2 class="text-sm font-bold text-slate-900 mb-3 uppercase tracking-wider">Active Floor Activity</h2>
            
            <div class="space-y-3">
                @forelse($myActiveRuns as $run)
                    <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-100 flex justify-between items-center">
                        <div>
                            <div class="font-bold text-slate-900">{{ $run->mould->code }}</div>
                            <div class="text-xs text-slate-500">{{ $run->machine->code }} • {{ $run->operator_name ?? 'Unknown' }}</div>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-2 py-1 rounded bg-green-100 text-green-700 text-xs font-bold">
                                RUNNING
                            </span>
                            <div class="text-xs text-slate-400 mt-1">{{ $run->start_ts->format('H:i') }}</div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-400 bg-white rounded-xl border border-dashed border-slate-200">
                        <p class="text-sm">No active runs.</p>
                    </div>
                @endforelse
            </div>
        </div>

        @can('maintenance_events.create')
        {{-- Active Work Orders --}}
        <div class="{{ $isMaintenanceOnly ? 'order-1' : 'order-2' }}">
            <h2 class="text-sm font-bold text-slate-900 mb-3 uppercase tracking-wider">Active Work Orders</h2>
            
            <div class="space-y-3">
                @forelse($activeWorkOrders as $wo)
                    <a wire:navigate href="{{ route('mobile.mould-detail', ['mould' => $wo->mould_id]) }}" class="block bg-white p-4 rounded-xl shadow-sm border border-slate-100 active:scale-95 transition-transform">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <div class="font-bold text-slate-900">{{ $wo->mould->code }}</div>
                                <div class="text-xs text-slate-500">{{ $wo->type }} • {{ Str::limit($wo->description, 30) }}</div>
                            </div>
                            <div class="text-right">
                                @if($wo->status === 'IN_PROGRESS')
                                    <span class="inline-flex items-center px-2 py-1 rounded bg-blue-100 text-blue-700 text-xs font-bold">
                                        IN PROGRESS
                                    </span>
                                @elseif($wo->status === 'REQUESTED')
                                    <span class="inline-flex items-center px-2 py-1 rounded bg-orange-100 text-orange-700 text-xs font-bold">
                                        REQUESTED
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded bg-purple-100 text-purple-700 text-xs font-bold">
                                        {{ $wo->status }}
                                    </span>
                                @endif
                                <div class="text-xs text-slate-400 mt-1">{{ $wo->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="text-center py-8 text-slate-400 bg-white rounded-xl border border-dashed border-slate-200">
                        <p class="text-sm">No active work orders.</p>
                    </div>
                @endforelse
            </div>
        </div>
        @endcan
    </div>
</div>
