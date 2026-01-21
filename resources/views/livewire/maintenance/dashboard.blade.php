<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Maintenance Hub</h1>
            <p class="text-gray-500">PM Schedules and repair logs</p>
        </div>
        <div class="space-x-2">
            <a href="{{ route('maintenance.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">Log Maintenance</a>
            <a href="{{ route('alerts.pm_due') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium">All PM Alerts</a>
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <div class="text-sm font-medium text-gray-500">Overdue PM</div>
                <div class="text-3xl font-bold text-red-600 mt-2">{{ $overdueCount }}</div>
            </div>
            <div class="h-10 w-10 bg-red-50 rounded-full flex items-center justify-center text-red-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <div class="text-sm font-medium text-gray-500">Due Soon</div>
                <div class="text-3xl font-bold text-amber-600 mt-2">{{ $dueCount }}</div>
            </div>
            <div class="h-10 w-10 bg-amber-50 rounded-full flex items-center justify-center text-amber-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between col-span-2">
            <div>
                <div class="text-sm font-medium text-gray-500">This Month's Top Downtime</div>
                <div class="flex gap-4 mt-2">
                    @forelse($topDowntime as $td)
                        <div class="text-center">
                            <div class="text-sm font-bold text-gray-900">{{ $td->code }}</div>
                            <div class="text-xs text-gray-500">{{ number_format($td->downtime_sum) }}m</div>
                        </div>
                    @empty
                        <span class="text-gray-400 text-sm">No significant downtime.</span>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- PM List --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
             <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/50 flex justify-between items-center">
                <h3 class="font-semibold text-gray-900">Priority Maintenance</h3>
            </div>
            <div class="divide-y divide-gray-100 max-h-[400px] overflow-y-auto">
                @forelse($pmDue as $m)
                    <div class="p-4 flex items-center justify-between hover:bg-gray-50">
                        <div class="flex items-center gap-3">
                             <span class="h-2 w-2 rounded-full {{ $m->pm_status === 'OVERDUE' ? 'bg-red-500' : 'bg-amber-500' }}"></span>
                             <div>
                                <div class="font-medium text-gray-900 text-sm">{{ $m->mould_code }}</div>
                                <div class="text-xs text-gray-500">{{ $m->mould_name }}</div>
                            </div>
                        </div>
                        <div class="text-right">
                             @if($m->pm_status === 'OVERDUE')
                                <span class="text-xs font-bold text-red-600">OVERDUE</span>
                             @else
                                <span class="text-xs font-bold text-amber-600">DUE</span>
                             @endif
                             <div class="text-xs text-gray-400">
                                 {{ $m->next_due_date ?? ($m->next_due_shot ? number_format($m->next_due_shot - $m->total_shot) . ' shots left' : 'Due') }}
                             </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-400 text-sm">All PMs are up to date!</div>
                @endforelse
            </div>
        </div>

        {{-- Recent History --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
             <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/50">
                <h3 class="font-semibold text-gray-900">Recent Activity</h3>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($recentEvents as $e)
                    <div class="p-4 hover:bg-gray-50">
                        <div class="flex justify-between mb-1">
                            <span class="font-medium text-sm text-gray-900">{{ $e->mould->code }}</span>
                            <span class="text-xs text-gray-500">{{ $e->end_ts->setTimezone(auth()->user()->timezone ?? 'Asia/Jakarta')->format('M d, H:i') }}</span>
                        </div>
                        <div class="text-sm text-gray-600">{{ $e->description ?? $e->type }}</div>
                        <div class="mt-2 flex items-center justify-between">
                            <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold tracking-wider {{ $e->type === 'PM' ? 'bg-green-100 text-green-700' : 'bg-purple-100 text-purple-700' }}">
                                {{ $e->type }}
                            </span>
                            <span class="text-xs text-gray-400">by {{ $e->performed_by }}</span>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-400 text-sm">No recent activity.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
