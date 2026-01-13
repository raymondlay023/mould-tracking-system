<div class="max-w-4xl mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-xl font-semibold">Mould Detail</h1>
        <a href="{{ route('runs.active') }}" class="text-sm text-blue-600">Active Runs</a>
    </div>

    @if (session('success'))
        <div class="mb-4 p-3 rounded bg-green-50 text-green-800 text-sm">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 p-3 rounded bg-red-50 text-red-800 text-sm">{{ session('error') }}</div>
    @endif

    <div class="bg-white shadow-sm rounded p-4 space-y-2 mb-6">
        <div><b>Code:</b> {{ $mould->code }}</div>
        <div><b>Name:</b> {{ $mould->name }}</div>
        <div><b>Cavities:</b> {{ $mould->cavities }}</div>
        <div><b>Status:</b> {{ $mould->status }}</div>
    </div>

    {{-- ACTIVE RUN --}}
    @if($activeRun)
        <div class="bg-yellow-50 border border-yellow-200 rounded p-4 mb-6">
            <div class="font-semibold mb-1">Run Active</div>
            <div class="text-sm">
                Machine: <b>{{ $activeRun->machine?->code }}</b>
                ({{ $activeRun->machine?->plant?->name }} / {{ $activeRun->machine?->zone?->code }})
            </div>
            <div class="text-sm">Start: {{ $activeRun->start_ts }}</div>
            <div class="mt-2">
                <a class="text-blue-600 text-sm" href="{{ route('runs.close', $activeRun->id) }}">Close Run</a>
            </div>
        </div>
    @else
        {{-- START RUN --}}
        <div class="bg-white shadow-sm rounded p-4 mb-6">
            <div class="font-semibold mb-3">Start Run</div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="text-sm">Machine *</label>
                    <select wire:model.defer="machine_id" class="mt-1 w-full rounded border-gray-300">
                        <option value="">-- pilih machine --</option>
                        @foreach($machines as $mc)
                            <option value="{{ $mc->id }}">
                                {{ $mc->code }} ({{ $mc->plant?->name }} / {{ $mc->zone?->code }})
                            </option>
                        @endforeach
                    </select>
                    @error('machine_id') <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="text-sm">Operator (optional)</label>
                    <input type="text" wire:model.defer="operator_name" class="mt-1 w-full rounded border-gray-300">
                    @error('operator_name') <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="text-sm">Notes (optional)</label>
                    <textarea wire:model.defer="notes" class="mt-1 w-full rounded border-gray-300" rows="2"></textarea>
                    @error('notes') <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="pt-3">
                <button type="button" wire:click="startRun" class="px-4 py-2 rounded bg-blue-600 text-white text-sm">
                    Start
                </button>
            </div>
        </div>
    @endif
</div>
