<div class="max-w-7xl mx-auto px-4 py-8">
    <div x-data="{
        toasts: [],
        soundOn: true,
        push(t) {
            const id = Date.now() + Math.random();
            this.toasts.push({ id, ...t });
            setTimeout(() => this.toasts = this.toasts.filter(x => x.id !== id), 3500);
        },
        beep() {
            try {
                const ctx = new(window.AudioContext || window.webkitAudioContext)();
                const o = ctx.createOscillator();
                const g = ctx.createGain();
                o.type = 'sine';
                o.frequency.value = 880;
                g.gain.value = 0.06;
                o.connect(g);
                g.connect(ctx.destination);
                o.start();
                setTimeout(() => { o.stop(); ctx.close(); }, 120);
            } catch (e) {}
        }
    }"
    x-on:toast.window="
        push($event.detail[0] ?? $event.detail);
        const payload = ($event.detail[0] ?? $event.detail);
        if (soundOn && payload.sound) beep();
    "
    class="fixed top-4 right-4 z-50 space-y-2 w-80">
        <template x-for="t in toasts" :key="t.id">
            <div class="rounded-lg shadow-lg border p-4 bg-white/95 backdrop-blur-sm transition-all duration-300 transform scale-100"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-x-8"
                x-transition:enter-end="opacity-100 translate-x-0"
                :class="t.type === 'success' ? 'border-green-200 shadow-green-100' : (t.type === 'warning' ? 'border-yellow-200 shadow-yellow-100' : 'border-gray-200')">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 pt-0.5">
                        <template x-if="t.type === 'success'">
                            <svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </template>
                        <template x-if="t.type !== 'success'">
                            <svg class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </template>
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-gray-900" x-text="t.title"></div>
                        <div class="text-xs text-gray-500 mt-1" x-text="t.message"></div>
                    </div>
                </div>
            </div>
        </template>
        <!-- Sound Toggle -->
        <div class="flex justify-end pr-1">
             <button type="button" class="text-xs px-2 py-1 rounded-full bg-white/80 backdrop-blur border text-gray-500 hover:text-gray-800 transition" 
                x-on:click="soundOn = !soundOn"
                x-text="soundOn ? '🔊 Sound: ON' : '🔇 Sound: OFF'">
            </button>
        </div>
    </div>

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-gray-900 to-gray-600 tracking-tight">Active Production</h1>
            <p class="text-gray-500 mt-1">Real-time monitoring of running moulds</p>
        </div>
        
        <div class="flex items-center gap-4">
             <!-- Live Indicator -->
            <div class="flex items-center gap-2 bg-white/60 backdrop-blur px-3 py-1.5 rounded-full border border-gray-200/50 shadow-sm" wire:poll.5s="refreshNow">
                <span class="relative flex h-2.5 w-2.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-green-500"></span>
                </span>
                <span class="text-xs font-semibold text-green-700 tracking-wide uppercase">Live Monitoring</span>
                 <span class="text-[10px] text-gray-400 border-l pl-2 ml-1">{{ $lastRefreshedAt }}</span>
            </div>

            <a href="{{ route('moulds.index') }}" class="group flex items-center gap-2 px-4 py-2 bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow text-sm font-medium text-gray-700 transition">
                <span>Master Moulds</span>
                <svg class="w-4 h-4 text-gray-400 group-hover:translate-x-0.5 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
            </a>
        </div>
    </div>

    <!-- Filter Bar (Glassmorphism) -->
    <div class="bg-white/70 backdrop-blur-md rounded-2xl shadow-sm border border-white/50 p-4 mb-8 sticky top-4 z-30 transition-shadow hover:shadow-md">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
            <!-- Search -->
            <div class="md:col-span-4 relative group">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                     <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
                <input type="text" wire:model.debounce.400ms="search" 
                    class="block w-full pl-10 pr-3 py-2.5 border-0 bg-gray-50/50 rounded-xl text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-blue-500/20 focus:bg-white transition sm:text-sm"
                    placeholder="Search by mould code, name...">
            </div>

            <!-- Filters -->
            <div class="md:col-span-8 flex flex-col md:flex-row gap-3">
                <select wire:model="plant_id" class="w-full md:w-1/3 py-2.5 px-3 border-0 bg-gray-50/50 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 cursor-pointer hover:bg-white transition">
                    <option value="">All Plants</option>
                    @foreach ($plants as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
                
                <select wire:model="zone_id" class="w-full md:w-1/3 py-2.5 px-3 border-0 bg-gray-50/50 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 cursor-pointer hover:bg-white transition">
                    <option value="">All Zones</option>
                    @foreach ($zones as $z)
                        <option value="{{ $z->id }}">{{ $z->code }} - {{ $z->name }}</option>
                    @endforeach
                </select>

                <select wire:model="machine_id" class="w-full md:w-1/3 py-2.5 px-3 border-0 bg-gray-50/50 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 cursor-pointer hover:bg-white transition">
                    <option value="">All Machines</option>
                    @foreach ($machines as $m)
                        <option value="{{ $m->id }}">{{ $m->code }} ({{ $m->plant?->name }})</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Active Runs Grid -->
    <div wire:loading.class="opacity-60 grayscale blur-sm" class="transition duration-300">
        
        @if($activeCount > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($runs as $r)
                    <!-- Premium Card -->
                    <div class="group relative bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden">
                        <!-- Card Header Gradient -->
                        <div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500"></div>

                        <div class="p-5">
                            <!-- Mould Info -->
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-lg font-bold text-gray-900 tracking-tight leading-none group-hover:text-blue-600 transition">{{ $r->mould?->code }}</span>
                                    </div>
                                    <p class="text-sm text-gray-500 mt-1 line-clamp-1" title="{{ $r->mould?->name }}">{{ $r->mould?->name }}</p>
                                </div>
                                <div class="bg-blue-50 text-blue-700 border border-blue-100 px-2 py-1 rounded-lg text-xs font-bold uppercase tracking-wide">
                                    {{ $r->mould?->cavities ?? '-' }} Cav
                                </div>
                            </div>

                            <!-- Machine & Location -->
                            <div class="space-y-3 mb-5">
                                <div class="flex items-center gap-3 text-sm text-gray-600">
                                    <div class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center border border-gray-100 text-gray-400 group-hover:bg-blue-50 group-hover:text-blue-500 transition">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" /></svg>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $r->machine?->code }}</div>
                                        <div class="text-xs text-gray-400">{{ $r->machine?->plant?->name }} / {{ $r->machine?->zone?->code }}</div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3 text-sm text-gray-600">
                                    <div class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center border border-gray-100 text-gray-400 group-hover:bg-green-50 group-hover:text-green-500 transition">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    </div>
                                    <div>
                                        <div class="font-bold font-mono text-gray-900 tracking-wide">
                                            {{ now()->diffInMinutes($r->start_ts) }} <span class="text-xs font-sans text-gray-400 font-normal">min run</span>
                                        </div>
                                        <div class="text-xs text-gray-400">Since {{ \Carbon\Carbon::parse($r->start_ts)->format('H:i') }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Footer -->
                             <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                <span class="text-xs font-medium text-gray-400 bg-gray-50 px-2 py-1 rounded">
                                    Shot: {{ number_format($r->machine?->total_shot ?? 0) }}
                                </span>
                                <a href="{{ route('runs.close', $r->id) }}" class="text-sm font-semibold text-red-500 hover:text-red-700 hover:underline transition">
                                    Stop Run
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $runs->links() }}
            </div>

        @else
            <!-- Empty State -->
            <div class="text-center py-20 bg-white rounded-3xl border border-dashed border-gray-200">
                <div class="mx-auto w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                     <svg class="w-8 h-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m-8-4v10l8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900">No Active Runs</h3>
                <p class="text-gray-500 mt-1">All production lines are currently idle or data is missing.</p>
                <button wire:click="refreshNow" class="mt-4 px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition">
                    Try Refreshing
                </button>
            </div>
        @endif
    </div>
</div>
