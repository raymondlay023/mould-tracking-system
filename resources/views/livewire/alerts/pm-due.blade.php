<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-red-600 to-orange-600 tracking-tight flex items-center gap-2">
                <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                PM Alerts
            </h1>
            <p class="text-gray-500 mt-1">Monitor preventive maintenance schedules and urgency</p>
        </div>

        <div class="flex items-center gap-2 bg-white/60 backdrop-blur-md px-4 py-2 rounded-full shadow-sm border border-white/50">
            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Status:</span>
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-1.5">
                    <span class="relative flex h-3 w-3">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                    </span>
                    <span class="text-sm font-bold text-gray-700">{{ $counts['overdue'] }} <span class="text-xs font-normal text-gray-500">Overdue</span></span>
                </div>
                <div class="h-4 w-px bg-gray-300"></div>
                <div class="flex items-center gap-1.5">
                    <span class="h-2.5 w-2.5 rounded-full bg-yellow-500"></span>
                    <span class="text-sm font-bold text-gray-700">{{ $counts['due'] }} <span class="text-xs font-normal text-gray-500">Due</span></span>
                </div>
                <div class="h-4 w-px bg-gray-300"></div>
                <div class="flex items-center gap-1.5">
                    <span class="h-2.5 w-2.5 rounded-full bg-gray-400"></span>
                    <span class="text-sm font-bold text-gray-700">{{ $counts['all'] }} <span class="text-xs font-normal text-gray-500">Total</span></span>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTERS --}}
    <div class="bg-white/80 backdrop-blur-xl shadow-sm rounded-2xl border border-white/50 p-4 mb-8 sticky top-20 z-20">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="relative">
                <input type="text" placeholder="Search mould..." class="w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm">
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>

            <select wire:model="plant_id" class="rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm">
                <option value="">All Plants</option>
                @foreach ($plants as $p)
                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                @endforeach
            </select>

            <select wire:model="machine_id" class="rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm">
                <option value="">All Machines</option>
                @foreach ($machines as $m)
                    <option value="{{ $m->id }}">{{ $m->code }} ({{ $m->plant?->name }})</option>
                @endforeach
            </select>

            <select wire:model="level" class="rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm">
                <option value="all">All Statuses</option>
                <option value="due">Due Only</option>
                <option value="overdue">Overdue Only</option>
            </select>
        </div>
    </div>

    {{-- CARDS GRID --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($items as $r)
            @php
                $statusColor = match($r->pm_status) {
                    'OVERDUE' => 'red',
                    'DUE' => 'yellow',
                    default => 'green'
                };
                $cardBorder = match($r->pm_status) {
                    'OVERDUE' => 'border-red-200 ring-4 ring-red-50',
                    'DUE' => 'border-yellow-200 ring-4 ring-yellow-50',
                    default => 'border-gray-100 hover:border-blue-200'
                };
            @endphp

            <div class="bg-white rounded-3xl p-5 border {{ $cardBorder }} shadow-sm hover:shadow-lg transition-all group relative overflow-hidden">
                {{-- Status Banner --}}
                <div class="absolute top-0 right-0 px-4 py-1.5 rounded-bl-2xl text-xs font-bold tracking-wider uppercase
                    {{ $r->pm_status === 'OVERDUE' ? 'bg-red-100 text-red-700' : ($r->pm_status === 'DUE' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-600') }}">
                    {{ $r->pm_status }}
                </div>

                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 group-hover:text-blue-600 transition-colors">{{ $r->code }}</h3>
                        <p class="text-sm text-gray-500 line-clamp-1">{{ $r->name }}</p>
                    </div>
                </div>

                {{-- Metrics Grid --}}
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                        <div class="text-xs text-gray-400 uppercase tracking-wide">Next Due</div>
                        <div class="font-bold text-gray-900">{{ $r->next_due_date ?? '-' }}</div>
                        <div class="text-xs text-gray-500">{{ $r->next_due_shot ? number_format($r->next_due_shot) . ' shots' : '-' }}</div>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                        <div class="text-xs text-gray-400 uppercase tracking-wide">Last Maint</div>
                        <div class="font-bold text-gray-900">{{ $r->last_maint_end_ts ? \Carbon\Carbon::parse($r->last_maint_end_ts)->format('d M') : '-' }}</div>
                        <div class="text-xs text-gray-500">{{ $r->last_maint_machine_code ?? 'N/A' }}</div>
                    </div>
                </div>

                <div class="space-y-2 mb-5">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            Location
                        </span>
                        <span class="font-medium text-gray-700 text-right truncate max-w-[120px]">
                            {{ $r->plant_name ?? '-' }} / {{ $r->machine_code ?? ($r->current_location ?? '-') }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Due By
                        </span>
                        <span class="font-medium {{ $r->due_by === 'OVERDUE' ? 'text-red-600' : 'text-gray-700' }}">
                            {{ $r->due_by ?: '-' }}
                        </span>
                    </div>
                </div>

                <a href="{{ route('moulds.show', $r->id) }}"
                   class="flex items-center justify-center w-full py-2.5 rounded-xl border border-gray-200 text-sm font-semibold text-gray-600 hover:bg-gray-900 hover:text-white hover:border-gray-900 transition-all group-hover:shadow-md">
                    View Details
                    <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
            </div>
        @empty
            <div class="col-span-full py-12 text-center text-gray-500 bg-white/50 rounded-3xl border border-dashed border-gray-200">
                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <h3 class="text-lg font-medium text-gray-900">All Good!</h3>
                <p class="text-sm">No mould maintenance alerts found matching criteria.</p>
            </div>
        @endforelse
    </div>
</div>
