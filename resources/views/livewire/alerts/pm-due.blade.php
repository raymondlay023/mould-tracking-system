<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-xl font-semibold">PM Due / Overdue</h1>
            <div class="text-xs text-gray-500">Today: {{ $today }}</div>
        </div>

        <a href="{{ route('moulds.index') }}" class="text-sm text-blue-600">Go to Moulds</a>
    </div>

    <div class="bg-white shadow-sm rounded p-4 mb-4 grid grid-cols-1 md:grid-cols-4 gap-3">
        <select wire:model="plant_id" class="rounded border-gray-300">
            <option value="">All Plants</option>
            @foreach ($plants as $p)
                <option value="{{ $p->id }}">{{ $p->name }}</option>
            @endforeach
        </select>

        <select wire:model="zone_id" class="rounded border-gray-300">
            <option value="">All Zones</option>
            @foreach ($zones as $z)
                <option value="{{ $z->id }}">{{ $z->code }} - {{ $z->name }}</option>
            @endforeach
        </select>

        <select wire:model="machine_id" class="rounded border-gray-300">
            <option value="">All Machines</option>
            @foreach ($machines as $m)
                <option value="{{ $m->id }}">{{ $m->code }} ({{ $m->plant?->name }})</option>
            @endforeach
        </select>

        <select wire:model="level" class="rounded border-gray-300">
            <option value="all">All</option>
            <option value="due">Due Only</option>
            <option value="overdue">Overdue Only</option>
        </select>
    </div>

    <div class="flex items-center gap-2 mb-3">
        <span class="text-xs px-3 py-1 rounded-full border bg-gray-50">Total: {{ $counts['all'] }}</span>
        <span class="text-xs px-3 py-1 rounded-full border bg-yellow-50 border-yellow-200 text-yellow-800">
            Due: {{ $counts['due'] }}
        </span>
        <span class="text-xs px-3 py-1 rounded-full border bg-red-50 border-red-200 text-red-800">
            Overdue: {{ $counts['overdue'] }}
        </span>
    </div>

    <div class="bg-white shadow-sm rounded p-4 overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left border-b">
                    <th class="py-2">Mould</th>
                    <th>Current</th>
                    <th>Last Maint</th>
                    <th>Next Due</th>
                    <th>Total Shot</th>
                    <th>Status</th>
                    <th class="text-right">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $r)
                    <tr class="border-b">
                        <td class="py-2">
                            <div class="font-medium">{{ $r->code }}</div>
                            <div class="text-gray-600 text-xs">{{ $r->name }}</div>
                        </td>

                        <td class="text-xs">
                            <div>{{ $r->plant_name ?? '-' }} / {{ $r->zone_code ?? '-' }}</div>
                            <div class="text-gray-500">{{ $r->machine_code ?? ($r->current_location ?? '-') }}</div>
                            <div class="text-gray-400">Since: {{ $r->location_since ?? '-' }}</div>
                        </td>

                        <td class="text-xs">
                            {{ $r->last_maint_end_ts ?? '-' }}
                        </td>

                        <td class="text-xs">
                            <div>Date: {{ $r->next_due_date ?? '-' }}</div>
                            <div>Shot: {{ $r->next_due_shot ?? '-' }}</div>
                            <div class="text-gray-500">DueBy: {{ $r->due_by ?: '-' }}</div>
                        </td>

                        <td>{{ $r->total_shot }}</td>

                        <td>
                            @if ($r->pm_status === 'OVERDUE')
                                <span class="text-xs px-2 py-0.5 rounded bg-red-50 text-red-700 border border-red-200">
                                    OVERDUE
                                </span>
                            @elseif($r->pm_status === 'DUE')
                                <span
                                    class="text-xs px-2 py-0.5 rounded bg-yellow-50 text-yellow-800 border border-yellow-200">
                                    DUE
                                </span>
                            @else
                                <span
                                    class="text-xs px-2 py-0.5 rounded bg-green-50 text-green-700 border border-green-200">
                                    OK
                                </span>
                            @endif
                        </td>

                        <td class="text-right">
                            <a href="{{ route('moulds.show', $r->id) }}" class="text-blue-600">Open</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-6 text-center text-gray-500">No PM due items</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
