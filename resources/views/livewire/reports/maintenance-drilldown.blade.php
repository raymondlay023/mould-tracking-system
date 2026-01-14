<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="flex items-start justify-between mb-4">
        <div>
            <h1 class="text-xl font-semibold">Maintenance Detail</h1>
            <div class="text-sm text-gray-700">{{ $title }}</div>
            <div class="text-xs text-gray-500">Range: {{ request('date_from') }} → {{ request('date_to') }}</div>
        </div>

        <a href="{{ route('reports.maintenance') }}?date_from={{ request('date_from') }}&date_to={{ request('date_to') }}"
            class="text-sm text-blue-600">← Back</a>
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
            <div class="text-xl font-semibold">{{ number_format($kpi['downtime']) }} min</div>
        </div>
        <div class="bg-white rounded shadow-sm p-4">
            <div class="text-xs text-gray-500">Cost</div>
            <div class="text-xl font-semibold">{{ number_format($kpi['cost']) }}</div>
        </div>
    </div>

    <div class="bg-white shadow-sm rounded p-4 overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left border-b">
                    <th class="py-2">End</th>
                    <th>Type</th>
                    <th>Mould</th>
                    <th>Machine</th>
                    <th class="text-right">Downtime</th>
                    <th class="text-right">Cost</th>
                    <th class="text-center">Next Due</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                @forelse($events as $e)
                    <tr class="border-b">
                        <td class="py-2 text-xs">
                            <div>{{ $e->end_ts }}</div>
                            <div class="text-gray-500">{{ $e->plant_name }} / {{ $e->zone_code }}</div>
                        </td>
                        <td>
                            <span
                                class="text-xs px-2 py-0.5 rounded border {{ $e->type === 'CM' ? 'bg-red-50 border-red-200 text-red-700' : 'bg-green-50 border-green-200 text-green-700' }}">
                                {{ $e->type }}
                            </span>
                        </td>
                        <td>{{ $e->mould_code }}</td>
                        <td>{{ $e->machine_code ?? '-' }}</td>
                        <td class="text-right">{{ number_format($e->downtime_min) }} min</td>
                        <td class="text-right">{{ number_format($e->cost ?? 0) }}</td>
                        <td class="text-xs text-center">
                            <div>Date: {{ $e->next_due_date ?? '-' }}</div>
                            <div>Shot: {{ $e->next_due_shot ?? '-' }}</div>
                        </td>
                        <td class="text-xs">{{ $e->description }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-6 text-center text-gray-500">No events</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
