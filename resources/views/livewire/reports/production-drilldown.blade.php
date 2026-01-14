<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="flex items-start justify-between mb-4">
        <div>
            <h1 class="text-xl font-semibold">Production Detail</h1>
            <div class="text-sm text-gray-700">{{ $title }}</div>
            <div class="text-xs text-gray-500">Range: {{ request('date_from') }} → {{ request('date_to') }}</div>
        </div>

        <a href="{{ route('reports.production') }}?date_from={{ request('date_from') }}&date_to={{ request('date_to') }}"
           class="text-sm text-blue-600">← Back</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-4">
        <div class="bg-white rounded shadow-sm p-4">
            <div class="text-xs text-gray-500">Shot</div>
            <div class="text-xl font-semibold">{{ number_format($kpiShot) }}</div>
        </div>
        <div class="bg-white rounded shadow-sm p-4">
            <div class="text-xs text-gray-500">OK</div>
            <div class="text-xl font-semibold">{{ number_format($kpiOk) }}</div>
        </div>
        <div class="bg-white rounded shadow-sm p-4">
            <div class="text-xs text-gray-500">NG</div>
            <div class="text-xl font-semibold">{{ number_format($kpiNg) }}</div>
        </div>
        <div class="bg-white rounded shadow-sm p-4">
            <div class="text-xs text-gray-500">NG Rate</div>
            <div class="text-xl font-semibold">{{ $ngRate }}%</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white shadow-sm rounded p-4">
            <h2 class="font-semibold mb-3">Top NG Reasons</h2>

            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left border-b">
                        <th class="py-2">Defect</th>
                        <th class="text-right">Qty</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topDefects as $d)
                        <tr class="border-b">
                            <td class="py-2">{{ $d->defect_code }}</td>
                            <td class="text-right">{{ number_format($d->qty_sum) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="py-6 text-center text-gray-500">No defects</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="lg:col-span-2 bg-white shadow-sm rounded p-4 overflow-x-auto">
            <h2 class="font-semibold mb-3">Runs (latest 200)</h2>

            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left border-b">
                        <th class="py-2">End</th>
                        <th>Mould</th>
                        <th>Machine</th>
                        <th class="text-right">Shot</th>
                        <th class="text-right">OK</th>
                        <th class="text-right">NG</th>
                        <th class="text-right">Cycle(s)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($runs as $r)
                        <tr class="border-b">
                            <td class="py-2 text-xs">
                                <div>{{ $r->end_ts }}</div>
                                <div class="text-gray-500">{{ $r->plant_name }} / {{ $r->zone_code }}</div>
                            </td>
                            <td>{{ $r->mould_code }}</td>
                            <td>{{ $r->machine_code }}</td>
                            <td class="text-right">{{ number_format($r->shot_total) }}</td>
                            <td class="text-right">{{ number_format($r->ok_part) }}</td>
                            <td class="text-right">{{ number_format($r->ng_part) }}</td>
                            <td class="text-right">{{ $r->cycle_time_avg_sec !== null ? number_format($r->cycle_time_avg_sec, 2) : '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-6 text-center text-gray-500">No runs</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
