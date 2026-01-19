<div class="max-w-7xl mx-auto px-4 py-8" x-data="{
    prevIds: [],
    toasts: [],
    init() { this.prevIds = @js($activeRunIds); },
    sync(ids) {
        const newOnes = ids.filter(x => !this.prevIds.includes(x));
        const gone = this.prevIds.filter(x => !ids.includes(x));

        newOnes.forEach(() => this.toast('✅ New active run detected'));
        gone.forEach(() => this.toast('ℹ️ Run completed / removed'));

        this.prevIds = ids;
    },
    toast(msg) {
        const id = Date.now() + Math.random();
        this.toasts.push({ id, msg });
        setTimeout(() => this.toasts = this.toasts.filter(t => t.id !== id), 3000);
    }
}" x-init="init()">

    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-gray-900 to-gray-600 tracking-tight">Dashboard Overview</h1>
            <p class="text-gray-500 mt-1">Real-time production and maintenance insights</p>
        </div>
        <div class="text-sm font-medium text-gray-400 bg-white/50 px-3 py-1 rounded-full border border-gray-100 backdrop-blur-sm">
            Updated: {{ now()->format('H:i') }}
        </div>
    </div>

    {{-- KPI cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- Active Runs -->
        <div class="relative bg-white/60 backdrop-blur-md rounded-2xl p-5 border border-white/40 shadow-sm hover:shadow-md transition-all group" wire:poll.5s x-effect="sync(@js($activeRunIds))">
            <div class="absolute top-0 right-0 p-4 opacity-5 bg-gradient-to-br from-blue-400 to-indigo-600 rounded-tr-2xl rounded-bl-3xl w-24 h-24 pointer-events-none group-hover:opacity-10 transition-opacity"></div>
            <div class="flex items-center gap-4">
                <div class="p-3 rounded-xl bg-blue-50 text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-500">Active Runs</div>
                    <div class="text-3xl font-bold text-gray-900 tracking-tight">{{ $activeCount }}</div>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100/50 flex items-center justify-between text-xs">
                <span class="text-green-600 font-medium flex items-center gap-1">
                    <span class="relative flex h-2 w-2">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                    </span>
                    Live Monitoring
                </span>
                <a href="{{ route('runs.active') }}" class="text-blue-600 hover:text-blue-800 font-medium">View All &rarr;</a>
            </div>
        </div>

        <!-- PM Overdue -->
        <div class="relative bg-white/60 backdrop-blur-md rounded-2xl p-5 border border-white/40 shadow-sm hover:shadow-md transition-all group">
            <div class="absolute top-0 right-0 p-4 opacity-5 bg-gradient-to-br from-red-400 to-rose-600 rounded-tr-2xl rounded-bl-3xl w-24 h-24 pointer-events-none group-hover:opacity-10 transition-opacity"></div>
            <div class="flex items-center gap-4">
                <div class="p-3 rounded-xl bg-red-50 text-red-600 group-hover:bg-red-600 group-hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-500">PM Overdue</div>
                    <div class="text-3xl font-bold text-gray-900 tracking-tight">{{ $pmOverdueCount }}</div>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100/50">
                 <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                    <div class="bg-red-500 h-1.5 rounded-full" style="width: {{ $pmOverdueCount > 0 ? '100%' : '0%' }}"></div>
                </div>
            </div>
        </div>

        <!-- PM Due -->
        <div class="relative bg-white/60 backdrop-blur-md rounded-2xl p-5 border border-white/40 shadow-sm hover:shadow-md transition-all group">
            <div class="absolute top-0 right-0 p-4 opacity-5 bg-gradient-to-br from-yellow-400 to-amber-600 rounded-tr-2xl rounded-bl-3xl w-24 h-24 pointer-events-none group-hover:opacity-10 transition-opacity"></div>
            <div class="flex items-center gap-4">
                <div class="p-3 rounded-xl bg-yellow-50 text-yellow-600 group-hover:bg-yellow-600 group-hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-500">PM Due Soon</div>
                    <div class="text-3xl font-bold text-gray-900 tracking-tight">{{ $pmDueCount }}</div>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100/50 flex justify-between items-center text-xs">
                 <span class="text-gray-500">Upcoming maintenance</span>
                 <a href="{{ route('alerts.pm_due') }}" class="text-yellow-600 hover:text-yellow-800 font-medium">Check &rarr;</a>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="relative bg-white/60 backdrop-blur-md rounded-2xl p-5 border border-white/40 shadow-sm hover:shadow-md transition-all flex flex-col justify-center">
            <div class="text-sm font-medium text-gray-500 mb-3">Quick Reports</div>
            <div class="space-y-2">
                <a href="{{ route('reports.production') }}" class="flex items-center justify-between p-2 rounded-lg bg-white/50 hover:bg-white border border-transparent hover:border-blue-100 hover:shadow-sm transition-all group/link">
                    <span class="text-sm font-medium text-gray-700 group-hover/link:text-blue-600">Production Report</span>
                    <svg class="w-4 h-4 text-gray-400 group-hover/link:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
                 <a href="{{ route('reports.maintenance') }}" class="flex items-center justify-between p-2 rounded-lg bg-white/50 hover:bg-white border border-transparent hover:border-purple-100 hover:shadow-sm transition-all group/link">
                    <span class="text-sm font-medium text-gray-700 group-hover/link:text-purple-600">Maintenance Report</span>
                    <svg class="w-4 h-4 text-gray-400 group-hover/link:text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Custom "Top List" Widget Style --}}

        {{-- Active Runs List --}}
        <div class="bg-white/80 backdrop-blur-xl rounded-3xl border border-white/50 shadow-sm overflow-hidden" wire:poll.5s>
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Active Runs</h2>
                    <p class="text-sm text-gray-500">Currently running on floor</p>
                </div>
                <a href="{{ route('runs.active') }}" class="p-2 rounded-full hover:bg-gray-100 text-gray-400 hover:text-blue-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                </a>
            </div>
            <div class="divide-y divide-gray-100 max-h-[400px] overflow-y-auto">
                @forelse($activeRuns as $r)
                    <div class="p-4 hover:bg-gray-50 transition-colors flex items-center justify-between group">
                        <div class="flex items-center gap-4">
                            <div class="h-10 w-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 font-bold text-xs ring-4 ring-white shadow-sm">
                                {{ Str::limit($r->machine_code, 3, '') }}
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">{{ $r->mould_code }}</div>
                                <div class="text-xs text-gray-500">{{ $r->mould_name }}</div>
                            </div>
                        </div>
                        <div class="text-right">
                             <div class="text-sm font-medium text-gray-900">{{ $r->plant_name }}</div>
                             <div class="text-xs text-gray-500">{{ $r->start_ts }}</div>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center text-gray-400">
                        <div class="mx-auto w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mb-3">
                            <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                        </div>
                        <p>No active runs detected</p>
                    </div>
                @endforelse
            </div>
             @if($activeRuns->count() > 0)
                <div class="p-3 bg-gray-50/50 text-center border-t border-gray-100">
                     <a href="{{ route('runs.active') }}" class="text-xs font-medium text-blue-600 hover:text-blue-800">View Full Monitor</a>
                </div>
            @endif
        </div>

        {{-- PM Due List --}}
        <div class="bg-white/80 backdrop-blur-xl rounded-3xl border border-white/50 shadow-sm overflow-hidden">
             <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <div>
                     <h2 class="text-lg font-bold text-gray-900">PM Schedule</h2>
                    <p class="text-sm text-gray-500">Upcoming & Overdue items</p>
                </div>
                 <a href="{{ route('alerts.pm_due') }}" class="p-2 rounded-full hover:bg-gray-100 text-gray-400 hover:text-yellow-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                </a>
            </div>
             <div class="divide-y divide-gray-100 max-h-[400px] overflow-y-auto">
                @forelse($pmDue as $m)
                    <div class="p-4 hover:bg-gray-50 transition-colors flex items-center justify-between">
                         <div class="flex items-center gap-4">
                            <div class="h-10 w-10 rounded-full {{ $m->pm_status === 'OVERDUE' ? 'bg-red-50 text-red-600' : 'bg-amber-50 text-amber-600' }} flex items-center justify-center font-bold text-xs ring-4 ring-white shadow-sm">
                                PM
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">{{ $m->mould_code }}</div>
                                <div class="text-xs text-gray-500">Due: {{ $m->next_due_date ?? $m->next_due_shot . ' shots' }}</div>
                            </div>
                        </div>
                        <div>
                            @if ($m->pm_status === 'OVERDUE')
                                <span class="text-xs font-bold px-2 py-1 rounded-full bg-red-100 text-red-700 border border-red-200 shadow-sm">OVERDUE</span>
                            @else
                                <span class="text-xs font-bold px-2 py-1 rounded-full bg-amber-100 text-amber-700 border border-amber-200 shadow-sm">DUE SOON</span>
                            @endif
                        </div>
                    </div>
                @empty
                     <div class="p-12 text-center text-gray-400">
                        <p>No clean bill of health! Nothing due.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Top NG --}}
        <div class="bg-white/80 backdrop-blur-xl rounded-3xl border border-white/50 shadow-sm overflow-hidden">
             <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">High Defect Rate</h2>
                     <p class="text-sm text-gray-500">Since {{ $ngFrom }}</p>
                </div>
                 <a href="{{ route('reports.production') }}" class="text-xs text-blue-600 hover:underline">Full Report</a>
            </div>
            <div class="p-4 space-y-4">
                 @forelse($topNg as $t)
                    @php
                        $pt = (int) $t->ok_sum + (int) $t->ng_sum;
                        $rate = $pt > 0 ? round(((int) $t->ng_sum / $pt) * 100, 1) : 0;
                        $color = $rate > 5 ? 'red' : ($rate > 2 ? 'amber' : 'blue');
                    @endphp
                    <div>
                         <div class="flex justify-between items-end mb-1">
                            <span class="font-medium text-sm text-gray-700">{{ $t->mould_code }}</span>
                            <span class="font-bold text-sm text-{{$color}}-600">{{ $rate }}% <span class="text-gray-400 text-xs font-normal">({{ number_format($t->ng_sum) }} NG)</span></span>
                        </div>
                         <div class="w-full bg-gray-100 rounded-full h-2">
                            <div class="bg-{{$color}}-500 h-2 rounded-full" style="width: {{ min($rate * 5, 100) }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6 text-gray-500">No defect data available</div>
                @endforelse
            </div>
        </div>

        {{-- Top CM --}}
        <div class="bg-white/80 backdrop-blur-xl rounded-3xl border border-white/50 shadow-sm overflow-hidden">
             <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <div>
                     <h2 class="text-lg font-bold text-gray-900">Highest Downtime</h2>
                     <p class="text-sm text-gray-500">Since {{ $cmFrom }}</p>
                </div>
                 <a href="{{ route('reports.maintenance') }}" class="text-xs text-blue-600 hover:underline">Full Report</a>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($topCm as $t)
                    <div class="p-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                        <div>
                             <div class="font-medium text-gray-900">{{ $t->mould_code }}</div>
                             <div class="text-xs text-gray-500">{{ $t->mould_name }}</div>
                        </div>
                        <div class="text-right">
                             <div class="font-bold text-gray-900">{{ number_format($t->downtime_sum) }} <span class="text-xs font-normal text-gray-500">min</span></div>
                             <div class="text-xs text-purple-600 font-medium">{{ $t->cm_count }} Events</div>
                        </div>
                    </div>
                 @empty
                    <div class="text-center py-6 text-gray-500">No downtime recorded</div>
                @endforelse
            </div>
        </div>

    </div>

    {{-- Safe Toast Notifications --}}
    <div class="fixed bottom-6 right-6 space-y-3 z-50 pointer-events-none">
        <template x-for="t in toasts" :key="t.id">
            <div class="pointer-events-auto bg-gray-900/90 text-white text-sm px-6 py-3 rounded-xl shadow-lg transform transition-all duration-300 flex items-center gap-3"
                 x-transition:enter="translate-y-2 opacity-0"
                 x-transition:leave="translate-y-2 opacity-0"
                 x-text="t.msg"></div>
        </template>
    </div>
</div>
