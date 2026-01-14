<div class="max-w-7xl mx-auto px-4 py-6" x-data="{
    prevIds: [],
    toasts: [],
    init() { this.prevIds = @js($activeRunIds); },
    sync(ids) {
        const newOnes = ids.filter(x => !this.prevIds.includes(x));
        const gone = this.prevIds.filter(x => !ids.includes(x));

        newOnes.forEach(() => this.toast('✅ Active run baru muncul'));
        gone.forEach(() => this.toast('ℹ️ Run selesai / hilang dari active'));

        this.prevIds = ids;
    },
    toast(msg) {
        const id = Date.now() + Math.random();
        this.toasts.push({ id, msg });
        setTimeout(() => this.toasts = this.toasts.filter(t => t.id !== id), 3000);
    }
}" x-init="init()">

    <div class="flex items-center justify-between mb-4">
        <h1 class="text-xl font-semibold">Dashboard Summary</h1>
        <div class="text-xs text-gray-500">Updated: {{ now() }}</div>
    </div>

    {{-- KPI cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-6">
        <div class="bg-white rounded shadow-sm p-4" wire:poll.5s x-effect="sync(@js($activeRunIds))">
            <div class="text-xs text-gray-500">Active Runs</div>
            <div class="text-2xl font-semibold">{{ $activeCount }}</div>
        </div>
        <div class="bg-white rounded shadow-sm p-4">
            <div class="text-xs text-gray-500">PM Overdue</div>
            <div class="text-2xl font-semibold text-red-700">{{ $pmOverdueCount }}</div>
        </div>
        <div class="bg-white rounded shadow-sm p-4">
            <div class="text-xs text-gray-500">PM Due</div>
            <div class="text-2xl font-semibold text-yellow-700">{{ $pmDueCount }}</div>
        </div>
        <div class="bg-white rounded shadow-sm p-4">
            <div class="text-xs text-gray-500">Links</div>
            <div class="text-sm">
                <a class="text-blue-600" href="{{ route('reports.production') }}">Production</a>
                <span class="mx-1">·</span>
                <a class="text-blue-600" href="{{ route('reports.maintenance') }}">Maintenance</a>
                <span class="mx-1">·</span>
                <a class="text-blue-600" href="{{ route('alerts.pm_due') }}">PM Due</a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Active Runs --}}
        <div class="bg-white rounded shadow-sm p-4" wire:poll.5s>
            <div class="flex items-center justify-between mb-3">
                <h2 class="font-semibold">Active Runs</h2>
                <a class="text-xs text-blue-600" href="{{ route('runs.active') }}">Open</a>
            </div>

            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left border-b">
                        <th class="py-2">Mould</th>
                        <th>Machine</th>
                        <th>Plant/Zone</th>
                        <th>Start</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activeRuns as $r)
                        <tr class="border-b">
                            <td class="py-2">
                                <div class="font-medium">{{ $r->mould_code }}</div>
                                <div class="text-xs text-gray-500">{{ $r->mould_name }}</div>
                            </td>
                            <td>{{ $r->machine_code }}</td>
                            <td class="text-xs">{{ $r->plant_name }} / {{ $r->zone_code }}</td>
                            <td class="text-xs">{{ $r->start_ts }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-6 text-center text-gray-500">No active runs</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PM Due --}}
        <div class="bg-white rounded shadow-sm p-4">
            <div class="flex items-center justify-between mb-3">
                <h2 class="font-semibold">PM Due / Overdue</h2>
                <a class="text-xs text-blue-600" href="{{ route('alerts.pm_due') }}">Open</a>
            </div>

            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left border-b">
                        <th class="py-2">Mould</th>
                        <th>Due Date</th>
                        <th>Due Shot</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pmDue as $m)
                        <tr class="border-b">
                            <td class="py-2">
                                <div class="font-medium">{{ $m->mould_code }}</div>
                                <div class="text-xs text-gray-500">{{ $m->mould_name }}</div>
                            </td>
                            <td class="text-xs">{{ $m->next_due_date ?? '-' }}</td>
                            <td class="text-xs">{{ $m->next_due_shot ?? '-' }}</td>
                            <td>
                                @if ($m->pm_status === 'OVERDUE')
                                    <span
                                        class="text-xs px-2 py-0.5 rounded bg-red-50 text-red-700 border border-red-200">OVERDUE</span>
                                @else
                                    <span
                                        class="text-xs px-2 py-0.5 rounded bg-yellow-50 text-yellow-800 border border-yellow-200">DUE</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-6 text-center text-gray-500">No due items</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Top NG --}}
        <div class="bg-white rounded shadow-sm p-4">
            <div class="flex items-center justify-between mb-3">
                <h2 class="font-semibold">Top NG (since {{ $ngFrom }})</h2>
                <a class="text-xs text-blue-600" href="{{ route('reports.production') }}">Open</a>
            </div>

            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left border-b">
                        <th class="py-2">Mould</th>
                        <th class="text-right">NG</th>
                        <th class="text-right">NG Rate</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topNg as $t)
                        @php
                            $pt = (int) $t->ok_sum + (int) $t->ng_sum;
                            $rate = $pt > 0 ? round(((int) $t->ng_sum / $pt) * 100, 2) : 0;
                        @endphp
                        <tr class="border-b">
                            <td class="py-2">
                                <div class="font-medium">{{ $t->mould_code }}</div>
                                <div class="text-xs text-gray-500">{{ $t->mould_name }}</div>
                            </td>
                            <td class="text-right">{{ number_format($t->ng_sum) }}</td>
                            <td class="text-right">{{ $rate }}%</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-6 text-center text-gray-500">No data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Top CM --}}
        <div class="bg-white rounded shadow-sm p-4">
            <div class="flex items-center justify-between mb-3">
                <h2 class="font-semibold">Top CM (since {{ $cmFrom }})</h2>
                <a class="text-xs text-blue-600" href="{{ route('reports.maintenance') }}">Open</a>
            </div>

            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left border-b">
                        <th class="py-2">Mould</th>
                        <th class="text-right">CM</th>
                        <th class="text-right">Downtime</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topCm as $t)
                        <tr class="border-b">
                            <td class="py-2">
                                <div class="font-medium">{{ $t->mould_code }}</div>
                                <div class="text-xs text-gray-500">{{ $t->mould_name }}</div>
                            </td>
                            <td class="text-right">{{ number_format($t->cm_count) }}</td>
                            <td class="text-right">{{ number_format($t->downtime_sum) }} min</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-6 text-center text-gray-500">No data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
    <div class="fixed bottom-4 right-4 space-y-2 z-50">
        <template x-for="t in toasts" :key="t.id">
            <div class="bg-gray-900 text-white text-sm px-4 py-2 rounded shadow" x-text="t.msg"></div>
        </template>
    </div>
</div>
