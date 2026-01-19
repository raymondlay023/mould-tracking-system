<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-700 to-indigo-600 tracking-tight">Production Report</h1>
            <p class="text-gray-500 mt-1 flex items-center gap-2 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Range: <span class="font-medium text-gray-700">{{ $date_from }}</span> <span class="text-gray-400">→</span> <span class="font-medium text-gray-700">{{ $date_to }}</span>
            </p>
        </div>

        <div class="flex gap-3">
            <button type="button" wire:click="exportCsv" class="px-4 py-2.5 rounded-xl border border-gray-200 text-gray-600 font-medium hover:bg-gray-50 hover:border-gray-300 transition-all flex items-center gap-2 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Export CSV
            </button>
            <button type="button" wire:click="exportExcel" class="px-4 py-2.5 rounded-xl bg-green-600 text-white font-medium hover:bg-green-700 shadow-lg shadow-green-100 transition-all flex items-center gap-2 transform hover:-translate-y-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Export Excel
            </button>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        {{-- Total Shots --}}
        <div class="relative overflow-hidden bg-white/60 backdrop-blur-md rounded-3xl p-6 border border-white/60 shadow-sm hover:shadow-lg transition-all group">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <svg class="w-24 h-24 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
            <div class="relative z-10">
                <div class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Total Shot</div>
                <div class="text-3xl font-bold text-gray-900">{{ number_format($kpi['shot_total']) }}</div>
                <div class="h-1 w-12 bg-blue-500 rounded-full mt-3"></div>
            </div>
        </div>

        {{-- OK Part --}}
        <div class="relative overflow-hidden bg-gradient-to-br from-green-50/50 to-white/60 backdrop-blur-md rounded-3xl p-6 border border-green-100 shadow-sm hover:shadow-lg transition-all group">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <svg class="w-24 h-24 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div class="relative z-10">
                <div class="text-sm font-semibold text-green-600 uppercase tracking-wider mb-2">OK Part</div>
                <div class="text-3xl font-bold text-gray-900">{{ number_format($kpi['ok_total']) }}</div>
                <div class="h-1 w-12 bg-green-500 rounded-full mt-3"></div>
            </div>
        </div>

        {{-- NG Part --}}
        <div class="relative overflow-hidden bg-gradient-to-br from-red-50/50 to-white/60 backdrop-blur-md rounded-3xl p-6 border border-red-100 shadow-sm hover:shadow-lg transition-all group">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <svg class="w-24 h-24 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div class="relative z-10">
                <div class="text-sm font-semibold text-red-600 uppercase tracking-wider mb-2">NG Part</div>
                <div class="text-3xl font-bold text-gray-900">{{ number_format($kpi['ng_total']) }}</div>
                <div class="h-1 w-12 bg-red-500 rounded-full mt-3"></div>
            </div>
        </div>

        {{-- NG Rate --}}
        <div class="relative overflow-hidden bg-white/60 backdrop-blur-md rounded-3xl p-6 border border-white/60 shadow-sm hover:shadow-lg transition-all group">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <svg class="w-24 h-24 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            </div>
            <div class="relative z-10">
                <div class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">NG Rate</div>
                <div class="text-3xl font-bold {{ $kpi['ng_rate'] > 2 ? 'text-red-600' : 'text-gray-900' }}">{{ $kpi['ng_rate'] }}%</div>
                <div class="h-1 w-12 bg-gray-400 rounded-full mt-3"></div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white/80 backdrop-blur-xl shadow-sm rounded-2xl border border-white/50 p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">From</label>
                <input type="date" wire:model="date_from" class="w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">To</label>
                <input type="date" wire:model="date_to" class="w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Plant</label>
                <select wire:model="plant_id" class="w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm">
                    <option value="">All Plants</option>
                    @foreach ($plants as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Zone</label>
                <select wire:model="zone_id" class="w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm">
                    <option value="">All Zones</option>
                    @foreach ($zones as $z)
                        <option value="{{ $z->id }}">{{ $z->code }} - {{ $z->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Machine</label>
                <select wire:model="machine_id" class="w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm">
                    <option value="">All Machines</option>
                    @foreach ($machines as $m)
                        <option value="{{ $m->id }}">{{ $m->code }} ({{ $m->plant?->name }})</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Group By</label>
                    <select wire:model="group_by" class="w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm">
                        <option value="mould">Mould</option>
                        <option value="machine">Machine</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Sort</label>
                    <select wire:model="sort" class="w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm">
                        <option value="ng_rate_desc">NG Rate ↓</option>
                        <option value="ng_desc">NG Qty ↓</option>
                        <option value="ok_desc">OK Qty ↓</option>
                        <option value="shot_desc">Shot ↓</option>
                        <option value="cycle_desc">Cycle ↓</option>
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
                        <th class="py-4 px-6 text-right font-semibold text-gray-600 uppercase tracking-wider text-xs">Shot</th>
                        <th class="py-4 px-6 text-right font-semibold text-gray-600 uppercase tracking-wider text-xs">OK</th>
                        <th class="py-4 px-6 text-right font-semibold text-gray-600 uppercase tracking-wider text-xs">NG</th>
                        <th class="py-4 px-6 text-right font-semibold text-gray-600 uppercase tracking-wider text-xs">NG Rate</th>
                        <th class="py-4 px-6 text-right font-semibold text-gray-600 uppercase tracking-wider text-xs">Avg Cycle</th>
                        <th class="py-4 px-6 text-right font-semibold text-gray-600 uppercase tracking-wider text-xs">Runs</th>
                        <th class="py-4 px-6 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Top Defect</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($rows as $r)
                        @php
                            $partTotal = (int) $r->ok_part + (int) $r->ng_part;
                            $ngRate = $partTotal > 0 ? round(((int) $r->ng_part / $partTotal) * 100, 2) : 0;
                            $rowClass = $loop->even ? 'bg-gray-50/30' : 'bg-white';
                        @endphp
                        <tr class="{{ $rowClass }} hover:bg-blue-50/50 transition-colors group">
                            <td class="py-4 px-6">
                                <a class="block group-hover:translate-x-1 transition-transform"
                                    href="{{ route('reports.production.drilldown', ['group' => $group_by, 'id' => $r->group_id]) }}
                                            ?date_from={{ $date_from }}&date_to={{ $date_to }}
                                            &plant_id={{ $plant_id }}&zone_id={{ $zone_id }}&machine_id={{ $machine_id }}">
                                    <div class="font-bold text-gray-900 group-hover:text-blue-600 transition-colors">{{ $r->group_code }}</div>
                                    <div class="text-xs text-gray-500">{{ $r->group_name }}</div>
                                </a>
                            </td>

                            <td class="py-4 px-6 text-right font-medium text-gray-700">{{ number_format($r->shot_total) }}</td>
                            <td class="py-4 px-6 text-right font-medium text-green-600">{{ number_format($r->ok_part) }}</td>
                            <td class="py-4 px-6 text-right font-medium text-red-600">{{ number_format($r->ng_part) }}</td>
                            <td class="py-4 px-6 text-right">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $ngRate > 2 ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                                    {{ $ngRate }}%
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right text-gray-600">
                                {{ $r->avg_cycle_sec !== null ? number_format($r->avg_cycle_sec, 2) . 's' : '-' }}
                            </td>
                            <td class="py-4 px-6 text-right text-gray-600">{{ number_format($r->runs_count) }}</td>
                            <td class="py-4 px-6">
                                @if ($r->top_defect_code)
                                    <div class="flex flex-col">
                                        <span class="font-medium text-red-600 text-xs">{{ $r->top_defect_code }}</span>
                                        <span class="text-gray-400 text-[10px]">{{ number_format($r->top_defect_qty) }} pcs</span>
                                    </div>
                                @else
                                    <span class="text-gray-300">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-gray-400 font-medium bg-gray-50/50">No production data found for this range</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
