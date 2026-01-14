<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="flex items-start justify-between mb-4">
        <div>
            <h1 class="text-xl font-semibold">Production Report</h1>
            <div class="text-xs text-gray-500">
                Range: {{ $date_from }} → {{ $date_to }}
            </div>
        </div>

        <div class="flex gap-2">
            <button type="button" wire:click="exportCsv" class="px-3 py-2 rounded border text-sm hover:bg-gray-50">
                Export CSV
            </button>
        </div>
    </div>

    {{-- KPI --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-4">
        <div class="bg-white rounded shadow-sm p-4">
            <div class="text-xs text-gray-500">Total Shot</div>
            <div class="text-xl font-semibold">{{ number_format($kpi['shot_total']) }}</div>
        </div>
        <div class="bg-white rounded shadow-sm p-4">
            <div class="text-xs text-gray-500">OK Part</div>
            <div class="text-xl font-semibold">{{ number_format($kpi['ok_total']) }}</div>
        </div>
        <div class="bg-white rounded shadow-sm p-4">
            <div class="text-xs text-gray-500">NG Part</div>
            <div class="text-xl font-semibold">{{ number_format($kpi['ng_total']) }}</div>
        </div>
        <div class="bg-white rounded shadow-sm p-4">
            <div class="text-xs text-gray-500">NG Rate</div>
            <div class="text-xl font-semibold">{{ $kpi['ng_rate'] }}%</div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white shadow-sm rounded p-4 mb-4 grid grid-cols-1 md:grid-cols-6 gap-3">
        <div>
            <label class="text-xs text-gray-500">Date From</label>
            <input type="date" wire:model="date_from" class="w-full rounded border-gray-300">
        </div>
        <div>
            <label class="text-xs text-gray-500">Date To</label>
            <input type="date" wire:model="date_to" class="w-full rounded border-gray-300">
        </div>

        <div>
            <label class="text-xs text-gray-500">Plant</label>
            <select wire:model="plant_id" class="w-full rounded border-gray-300">
                <option value="">All</option>
                @foreach ($plants as $p)
                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="text-xs text-gray-500">Zone</label>
            <select wire:model="zone_id" class="w-full rounded border-gray-300">
                <option value="">All</option>
                @foreach ($zones as $z)
                    <option value="{{ $z->id }}">{{ $z->code }} - {{ $z->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="text-xs text-gray-500">Machine</label>
            <select wire:model="machine_id" class="w-full rounded border-gray-300">
                <option value="">All</option>
                @foreach ($machines as $m)
                    <option value="{{ $m->id }}">{{ $m->code }} ({{ $m->plant?->name }})</option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-2 gap-2">
            <div>
                <label class="text-xs text-gray-500">Group</label>
                <select wire:model="group_by" class="w-full rounded border-gray-300">
                    <option value="mould">Mould</option>
                    <option value="machine">Machine</option>
                </select>
            </div>
            <div>
                <label class="text-xs text-gray-500">Sort</label>
                <select wire:model="sort" class="w-full rounded border-gray-300">
                    <option value="ng_rate_desc">NG Rate ↓</option>
                    <option value="ng_desc">NG Qty ↓</option>
                    <option value="ok_desc">OK Qty ↓</option>
                    <option value="shot_desc">Shot ↓</option>
                    <option value="cycle_desc">Cycle ↓</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white shadow-sm rounded p-4 overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left border-b">
                    <th class="py-2">{{ $group_by === 'machine' ? 'Machine' : 'Mould' }}</th>
                    <th class="text-right">Shot</th>
                    <th class="text-right">OK</th>
                    <th class="text-right">NG</th>
                    <th class="text-right">NG Rate</th>
                    <th class="text-right">Avg Cycle (s)</th>
                    <th class="text-right">Runs</th>
                    <th>Top NG Reason</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $r)
                    @php
                        $partTotal = (int) $r->ok_part + (int) $r->ng_part;
                        $ngRate = $partTotal > 0 ? round(((int) $r->ng_part / $partTotal) * 100, 2) : 0;
                    @endphp
                    <tr class="border-b">
                        <td class="py-2">
                            <a class="block hover:underline text-blue-700"
                                href="{{ route('reports.production.drilldown', ['group' => $group_by, 'id' => $r->group_id]) }}
                                        ?date_from={{ $date_from }}&date_to={{ $date_to }}
                                        &plant_id={{ $plant_id }}&zone_id={{ $zone_id }}&machine_id={{ $machine_id }}">
                                <div class="font-medium">{{ $r->group_code }}</div>
                                <div class="text-xs text-gray-600">{{ $r->group_name }}</div>
                            </a>
                        </td>

                        <td class="text-right">{{ number_format($r->shot_total) }}</td>
                        <td class="text-right">{{ number_format($r->ok_part) }}</td>
                        <td class="text-right">{{ number_format($r->ng_part) }}</td>
                        <td class="text-right">{{ $ngRate }}%</td>
                        <td class="text-right">
                            {{ $r->avg_cycle_sec !== null ? number_format($r->avg_cycle_sec, 2) : '-' }}</td>
                        <td class="text-right">{{ number_format($r->runs_count) }}</td>
                        <td class="text-xs">
                            @if ($r->top_defect_code)
                                <div class="font-medium">{{ $r->top_defect_code }}</div>
                                <div class="text-gray-500">{{ number_format($r->top_defect_qty) }} pcs</div>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-6 text-center text-gray-500">No data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
