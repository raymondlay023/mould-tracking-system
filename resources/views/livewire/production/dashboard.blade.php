<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Production Control</h1>
            <p class="text-gray-500">Overview of active lines and available resources</p>
        </div>
        <div class="space-x-2">
            <a href="{{ route('runs.active') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">Manage Runs</a>
            <a href="{{ route('setups.index') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium">Log Setup</a>
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="text-sm font-medium text-gray-500">Active Lines</div>
            <div class="text-3xl font-bold text-blue-600 mt-2">{{ $activeRuns->count() }}</div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="text-sm font-medium text-gray-500">Moulds Available</div>
            <div class="text-3xl font-bold text-green-600 mt-2">{{ $availableMoulds->count() }}</div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="text-sm font-medium text-gray-500">Avg Defect Rate (7d)</div>
            @php
                $avgRate = $topNg->avg(fn($t) => ($t->ng_sum + $t->ok_sum) > 0 ? ($t->ng_sum / ($t->ng_sum + $t->ok_sum)) * 100 : 0);
            @endphp
            <div class="text-3xl font-bold text-{{ $avgRate > 5 ? 'red' : 'gray' }}-600 mt-2">{{ number_format($avgRate ?? 0, 1) }}%</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Active Runs --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/50 flex justify-between items-center">
                <h3 class="font-semibold text-gray-900">Running Machines</h3>
                <span class="text-xs text-green-600 font-medium animate-pulse">● Live</span>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($activeRuns as $run)
                    <div class="p-4 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs">
                                {{ $run->machine_code }}
                            </div>
                            <div>
                                <div class="font-medium text-gray-900 text-sm">{{ $run->mould_code }}</div>
                                <div class="text-xs text-gray-500">{{ $run->mould_name }}</div>
                            </div>
                        </div>
                        <div class="text-right">
                             <div class="text-sm font-mono text-gray-700">{{ number_format($run->shot_total) }} shots</div>
                             <div class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($run->start_ts)->diffForHumans() }}</div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-400 text-sm">No active runs.</div>
                @endforelse
            </div>
        </div>

        {{-- Available Moulds --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
             <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/50">
                <h3 class="font-semibold text-gray-900">Ready for Production</h3>
            </div>
            <div class="divide-y divide-gray-100 max-h-[400px] overflow-y-auto">
                @forelse($availableMoulds as $m)
                    <div class="p-4 flex items-center justify-between hover:bg-gray-50">
                        <div>
                             <div class="font-medium text-gray-900 text-sm mb-0.5">{{ $m->code }}</div>
                             <div class="text-xs text-gray-500">{{ $m->name }}</div>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-2 py-1 rounded-md bg-green-50 text-green-700 text-xs font-medium">{{ $m->cavities }} Cav</span>
                            <div class="text-xs text-gray-400 mt-1">{{ Str::limit($m->customer, 10) }}</div>
                        </div>
                    </div>
                @empty
                     <div class="p-8 text-center text-gray-400 text-sm">No moulds available.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
