<div class="max-w-7xl mx-auto px-4 py-6">
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
                setTimeout(() => { o.stop();
                    ctx.close(); }, 120);
            } catch (e) {}
        }
    }"
        x-on:toast.window="
        push($event.detail[0] ?? $event.detail);
        const payload = ($event.detail[0] ?? $event.detail);
        if (soundOn && payload.sound) beep();
    "
        class="fixed top-4 right-4 z-50 space-y-2 w-80">
        <div class="flex justify-end">
            <button type="button" class="text-xs px-2 py-1 rounded border bg-white" x-on:click="soundOn = !soundOn"
                x-text="soundOn ? 'Sound: ON' : 'Sound: OFF'">
            </button>
        </div>

        <template x-for="t in toasts" :key="t.id">
            <div class="rounded shadow border p-3 bg-white"
                :class="t.type === 'success' ? 'border-green-200' : (t.type === 'warning' ? 'border-yellow-200' :
                    'border-gray-200')">
                <div class="text-sm font-semibold" x-text="t.title"></div>
                <div class="text-xs text-gray-600 mt-1" x-text="t.message"></div>
            </div>
        </template>
    </div>

    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <h1 class="text-xl font-semibold">Active Runs</h1>

            <span
                class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                Active: {{ $activeCount }}
            </span>
        </div>

        <a href="{{ route('moulds.index') }}" class="text-sm text-blue-600">Go to Moulds</a>
    </div>

    <div class="bg-white shadow-sm rounded p-4 mb-4 grid grid-cols-1 md:grid-cols-4 gap-3">
        <input type="text" wire:model.debounce.400ms="search" class="rounded border-gray-300"
            placeholder="Search mould code/name...">

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
    </div>

    <div class="bg-white shadow-sm rounded p-4 overflow-x-auto" wire:poll.5s="refreshNow"
        wire:loading.class="opacity-60">

        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-2 text-sm">
                    <span class="relative flex h-2 w-2">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-500 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-green-600"></span>
                    </span>
                    Live
                </span>

                <span class="text-xs text-gray-500">
                    Updated: {{ $lastRefreshedAt }}
                </span>

                <span wire:loading class="text-xs text-gray-500">
                    Refreshing...
                </span>
            </div>

            <button type="button" wire:click="refreshNow" class="text-xs px-3 py-1 rounded border hover:bg-gray-50">
                Refresh
            </button>
        </div>

        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left border-b">
                    <th class="py-2">Mould </th>
                    <th>Machine</th>
                    <th>Plant/Zone</th>
                    <th>Run Start</th>
                    <th>Duration</th>
                    <th class="text-right">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($runs as $r)
                    <tr class="border-b">
                        <td class="py-2">
                            <div class="font-medium">{{ $r->mould?->code }} <span
                                    class="text-xs px-2 py-0.5 rounded bg-green-50 text-green-700 border border-green-200">
                                    ACTIVE
                                </span></div>
                            <div class="text-gray-600 text-xs">{{ $r->mould?->name }}</div>
                        </td>
                        <td>{{ $r->machine?->code }} - {{ $r->machine?->name }}</td>
                        <td>{{ $r->machine?->plant?->name }} / {{ $r->machine?->zone?->code }}</td>
                        <td>{{ $r->start_ts }}</td>
                        <td>
                            {{-- Durasi akan berubah karena poll re-render --}}
                            {{ now()->diffInMinutes($r->start_ts) }} min
                        </td>
                        <td class="text-right">
                            <a href="{{ route('runs.close', $r->id) }}" class="text-blue-600">Close</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-6 text-center text-gray-500">No active runs</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="mt-4">
            {{ $runs->links() }}
        </div>
    </div>

</div>
