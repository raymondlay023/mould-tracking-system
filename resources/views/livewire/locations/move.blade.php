<div class="max-w-3xl mx-auto px-4 py-6">
    <h1 class="text-xl font-semibold mb-4">Move Mould Location</h1>

    @if (session('success'))
        <div class="mb-4 p-3 rounded bg-green-50 text-green-800 text-sm">{{ session('success') }}</div>
    @endif

    <div class="bg-white shadow-sm rounded p-4 space-y-3">
        <div>
            <label class="text-sm">Mould *</label>
            <select wire:model.defer="mould_id" class="mt-1 w-full rounded border-gray-300">
                <option value="">-- pilih mould --</option>
                @foreach ($moulds as $m)
                    <option value="{{ $m->id }}">{{ $m->code }} - {{ $m->name }}</option>
                @endforeach
            </select>
            @error('mould_id')
                <div class="text-red-600 text-xs mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="text-sm">Plant (optional)</label>
                <select wire:model.defer="plant_id" class="mt-1 w-full rounded border-gray-300">
                    <option value="">-- pilih plant --</option>
                    @foreach ($plants as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
                @error('plant_id')
                    <div class="text-red-600 text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label class="text-sm">Location *</label>
                <select wire:model="location" class="mt-1 w-full rounded border-gray-300">
                    <option value="TOOL_ROOM">TOOL_ROOM</option>
                    <option value="WAREHOUSE">WAREHOUSE</option>
                    <option value="IN_TRANSIT">IN_TRANSIT</option>
                    <option value="MACHINE">MACHINE</option>
                </select>
                @error('location')
                    <div class="text-red-600 text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>
        </div>

        @if ($location === 'MACHINE')
            <div>
                <label class="text-sm">Machine *</label>
                <select wire:model.defer="machine_id" class="mt-1 w-full rounded border-gray-300">
                    <option value="">-- pilih machine --</option>
                    @foreach ($machines as $mc)
                        <option value="{{ $mc->id }}">{{ $mc->code }}
                            ({{ $mc->plant?->name }}/{{ $mc->zone?->code }})</option>
                    @endforeach
                </select>
                @error('machine_id')
                    <div class="text-red-600 text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>
        @endif

        <div>
            <label class="text-sm">Note</label>
            <input type="text" wire:model.defer="note" class="mt-1 w-full rounded border-gray-300">
        </div>

        <button type="button" wire:click="move" class="px-4 py-2 rounded bg-blue-600 text-white text-sm">
            Move
        </button>
    </div>
</div>
