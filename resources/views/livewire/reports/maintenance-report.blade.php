<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="flex items-start justify-between mb-4">
        <div>
            <h1 class="text-xl font-semibold">Maintenance Report</h1>
            <div class="text-xs text-gray-500">Range: {{ $date_from }} → {{ $date_to }}</div>
        </div>
        
        <div class="flex gap-2">
            <a href="{{ route('reports.production') }}" class="text-sm text-blue-600 px-3 py-2">Go to Production Report</a>
            <button type="button" wire:click="exportExcel" class="px-3 py-2 rounded border text-sm hover:bg-gray-50">
                Export Excel
            </button>
        </div>

    </div>

    <div class="grid grid-cols-1 md:grid-cols-5 gap-3 mb-4">
        <div class="bg-white rounded shadow-sm p-4">
            <div class="text-xs text-gray-500">Events</div>
            <div class="text-xl font-semibold">{{ number_format($kpi['events']) }}</div>
        </div>
        <div class="bg-white rounded shadow-sm p-4">
            <div class="text-xs text-gray-500">PM</div>
            <div class="text-xl font-semibold">{{ number_format($kpi['pm']) }}</div>
        </div>
        <div class="bg-white rounded shadow-sm p-4">
            <div class="text-xs text-gray-500">CM</div>
            <div class="text-xl font-semibold">{{ number_format($kpi['cm']) }}</div>
        </div>
        <div class="bg-white rounded shadow-sm p-4">
            <div class="text-xs text-gray-500">Downtime</div>
            <div class="text-xl font-semibold">{{ number_format($kpi['downtime_min']) }} min</div>
        </div>
        <div class="bg-white rounded shadow-sm p-4">
            <div class="text-xs text-gray-500">Cost</div>
            <div class="text-xl font-semibold">{{ number_format($kpi['cost_sum']) }}</div>
        </div>
    </div>

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
                    <option value="downtime_desc">Downtime ↓</option>
                    <option value="cm_desc">CM Count ↓</option>
                    <option value="pm_desc">PM Count ↓</option>
                    <option value="cost_desc">Cost ↓</option>
                    <option value="count_desc">Events ↓</option>
                </select>
            </div>
        </div>
    </div>

    <div class="bg-white shadow-sm rounded p-4 overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left border-b">
                    <th class="py-2">{{ $group_by === 'machine' ? 'Machine' : 'Mould' }}</th>
                    <th class="text-right">Events</th>
                    <th class="text-right">PM</th>
                    <th class="text-right">CM</th>
                    <th class="text-right">Downtime (min)</th>
                    <th class="text-right">Cost</th>
                    <th class="text-right">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $r)
                    <tr class="border-b">
                        <td class="py-2">
                            <div class="font-medium">{{ $r->group_code }}</div>
                            <div class="text-xs text-gray-600">{{ $r->group_name }}</div>
                        </td>
                        <td class="text-right">{{ number_format($r->events_count) }}</td>
                        <td class="text-right">{{ number_format($r->pm_count) }}</td>
                        <td class="text-right">{{ number_format($r->cm_count) }}</td>
                        <td class="text-right">{{ number_format($r->downtime_min) }}</td>
                        <td class="text-right">{{ number_format($r->cost_sum) }}</td>
                        <td class="text-right">
                            <a class="text-blue-600"
                                href="{{ route('reports.maintenance.drilldown', ['group' => $group_by, 'id' => $r->group_id]) }}
                                    ?date_from={{ $date_from }}&date_to={{ $date_to }}
                                    &plant_id={{ $plant_id }}&zone_id={{ $zone_id }}&machine_id={{ $machine_id }}">
                                Detail
                            </a>
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
