<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-teal-600 to-emerald-600 tracking-tight">Maintenance Report</h1>
            <p class="text-gray-500 mt-1 flex items-center gap-2 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Range: <span class="font-medium text-gray-700">{{ $date_from }}</span> <span class="text-gray-400">→</span> <span class="font-medium text-gray-700">{{ $date_to }}</span>
            </p>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('reports.production') }}" class="px-4 py-2.5 rounded-xl border border-blue-200 text-blue-700 font-medium bg-blue-50 hover:bg-blue-100 transition-all flex items-center gap-2 text-sm shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                View Production
            </a>
            <button type="button" wire:click="exportExcel" class="px-4 py-2.5 rounded-xl bg-green-600 text-white font-medium hover:bg-green-700 shadow-lg shadow-green-100 transition-all flex items-center gap-2 transform hover:-translate-y-0.5 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Export Excel
            </button>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
        {{-- Events --}}
        <div class="relative overflow-hidden bg-white/60 backdrop-blur-md rounded-3xl p-6 border border-white/60 shadow-sm hover:shadow-lg transition-all group">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <svg class="w-20 h-20 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
            </div>
            <div class="relative z-10">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Events</div>
                <div class="text-2xl font-bold text-gray-900">{{ number_format($kpi['events']) }}</div>
            </div>
        </div>

        {{-- PM --}}
        <div class="relative overflow-hidden bg-white/60 backdrop-blur-md rounded-3xl p-6 border border-white/60 shadow-sm hover:shadow-lg transition-all group">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <svg class="w-20 h-20 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
            </div>
            <div class="relative z-10">
                <div class="text-xs font-semibold text-blue-600 uppercase tracking-wider mb-2">PM Count</div>
                <div class="text-2xl font-bold text-gray-900">{{ number_format($kpi['pm']) }}</div>
                <div class="h-1 w-8 bg-blue-500 rounded-full mt-2"></div>
            </div>
        </div>

        {{-- CM --}}
        <div class="relative overflow-hidden bg-white/60 backdrop-blur-md rounded-3xl p-6 border border-white/60 shadow-sm hover:shadow-lg transition-all group">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <svg class="w-20 h-20 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <div class="relative z-10">
                <div class="text-xs font-semibold text-orange-600 uppercase tracking-wider mb-2">CM Count</div>
                <div class="text-2xl font-bold text-gray-900">{{ number_format($kpi['cm']) }}</div>
                <div class="h-1 w-8 bg-orange-500 rounded-full mt-2"></div>
            </div>
        </div>

        {{-- Downtime --}}
        <div class="relative overflow-hidden bg-white/60 backdrop-blur-md rounded-3xl p-6 border border-white/60 shadow-sm hover:shadow-lg transition-all group">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <svg class="w-20 h-20 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div class="relative z-10">
                <div class="text-xs font-semibold text-red-600 uppercase tracking-wider mb-2">Downtime</div>
                <div class="text-2xl font-bold text-gray-900">{{ number_format($kpi['downtime_min']) }}<span class="text-base text-gray-500 font-normal ml-1">min</span></div>
                <div class="h-1 w-8 bg-red-500 rounded-full mt-2"></div>
            </div>
        </div>

        {{-- Cost --}}
        <div class="relative overflow-hidden bg-white/60 backdrop-blur-md rounded-3xl p-6 border border-white/60 shadow-sm hover:shadow-lg transition-all group">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <svg class="w-20 h-20 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div class="relative z-10">
                <div class="text-xs font-semibold text-green-600 uppercase tracking-wider mb-2">Total Cost</div>
                <div class="text-2xl font-bold text-gray-900">{{ number_format($kpi['cost_sum']) }}</div>
                <div class="h-1 w-8 bg-green-500 rounded-full mt-2"></div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white/80 backdrop-blur-xl shadow-sm rounded-2xl border border-white/50 p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">From</label>
                <input type="date" wire:model="date_from" class="w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">To</label>
                <input type="date" wire:model="date_to" class="w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all text-sm">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Plant</label>
                <select wire:model="plant_id" class="w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all text-sm">
                    <option value="">All Plants</option>
                    @foreach ($plants as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Zone</label>
                <select wire:model="zone_id" class="w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all text-sm">
                    <option value="">All Zones</option>
                    @foreach ($zones as $z)
                        <option value="{{ $z->id }}">{{ $z->code }} - {{ $z->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Machine</label>
                <select wire:model="machine_id" class="w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all text-sm">
                    <option value="">All Machines</option>
                    @foreach ($machines as $m)
                        <option value="{{ $m->id }}">{{ $m->code }} ({{ $m->plant?->name }})</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Group By</label>
                    <select wire:model="group_by" class="w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all text-sm">
                        <option value="mould">Mould</option>
                        <option value="machine">Machine</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Sort</label>
                    <select wire:model="sort" class="w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all text-sm">
                        <option value="downtime_desc">Downtime ↓</option>
                        <option value="cm_desc">CM Count ↓</option>
                        <option value="pm_desc">PM Count ↓</option>
                        <option value="cost_desc">Cost ↓</option>
                        <option value="count_desc">Events ↓</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Premium Table --}}
    <div class="bg-white/80 backdrop-blur-xl shadow-xl rounded-3xl border border-white/50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="py-4 px-6 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">{{ $group_by === 'machine' ? 'Machine' : 'Mould' }}</th>
                        <th class="py-4 px-6 text-right font-semibold text-gray-600 uppercase tracking-wider text-xs">Events</th>
                        <th class="py-4 px-6 text-right font-semibold text-gray-600 uppercase tracking-wider text-xs">PM</th>
                        <th class="py-4 px-6 text-right font-semibold text-gray-600 uppercase tracking-wider text-xs">CM</th>
                        <th class="py-4 px-6 text-right font-semibold text-gray-600 uppercase tracking-wider text-xs">Downtime (min)</th>
                        <th class="py-4 px-6 text-right font-semibold text-gray-600 uppercase tracking-wider text-xs">Cost</th>
                        <th class="py-4 px-6 text-right font-semibold text-gray-600 uppercase tracking-wider text-xs">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($rows as $r)
                        @php
                            $rowClass = $loop->even ? 'bg-gray-50/30' : 'bg-white';
                        @endphp
                        <tr class="{{ $rowClass }} hover:bg-teal-50/50 transition-colors group">
                            <td class="py-4 px-6">
                                <div class="font-bold text-gray-900 group-hover:text-teal-600 transition-colors">{{ $r->group_code }}</div>
                                <div class="text-xs text-gray-500">{{ $r->group_name }}</div>
                            </td>
                            <td class="py-4 px-6 text-right font-medium text-gray-700">{{ number_format($r->events_count) }}</td>
                            <td class="py-4 px-6 text-right font-medium text-blue-600">{{ number_format($r->pm_count) }}</td>
                            <td class="py-4 px-6 text-right font-medium text-orange-600">{{ number_format($r->cm_count) }}</td>
                            <td class="py-4 px-6 text-right font-medium text-red-600">{{ number_format($r->downtime_min) }}</td>
                            <td class="py-4 px-6 text-right font-medium text-gray-700">{{ number_format($r->cost_sum) }}</td>
                            <td class="py-4 px-6 text-right">
                                <a class="inline-flex items-center px-3 py-1.5 rounded-lg border border-gray-200 text-xs font-semibold text-gray-600 hover:bg-teal-50 hover:text-teal-700 hover:border-teal-200 transition-all"
                                    href="{{ route('reports.maintenance.drilldown', ['group' => $group_by, 'id' => $r->group_id]) }}
                                        ?date_from={{ $date_from }}&date_to={{ $date_to }}
                                        &plant_id={{ $plant_id }}&zone_id={{ $zone_id }}&machine_id={{ $machine_id }}">
                                    Details
                                    <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-gray-400 font-medium bg-gray-50/50">No maintenance data found for this range</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
