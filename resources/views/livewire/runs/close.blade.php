<div class="max-w-5xl mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-xl font-semibold">Close Run</h1>
        <a href="{{ route('runs.active') }}" class="text-sm text-blue-600">Back to Active Runs</a>
    </div>

    @if (session('success'))
        <div class="mb-4 p-3 rounded bg-green-50 text-green-800 text-sm">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 p-3 rounded bg-red-50 text-red-800 text-sm">{{ session('error') }}</div>
    @endif

    <div class="bg-white shadow-sm rounded p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
            <div><b>Mould:</b> {{ $run->mould?->code }} - {{ $run->mould?->name }}</div>
            <div><b>Machine:</b> {{ $run->machine?->code }} - {{ $run->machine?->name }}</div>
            <div><b>Plant/Zone:</b> {{ $run->machine?->plant?->name }} / {{ $run->machine?->zone?->code }}</div>
            <div><b>Start:</b> {{ $run->start_ts }}</div>
            <div><b>Cavities snapshot:</b> {{ $run->cavities_snapshot }}</div>
            <div><b>Status:</b> {{ $run->end_ts ? 'CLOSED' : 'ACTIVE' }}</div>
        </div>
    </div>

    <div class="bg-white shadow-sm rounded p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div>
                <label class="text-sm">Shot Total *</label>
                <input type="number" min="0" wire:model.defer="shot_total" class="mt-1 w-full rounded border-gray-300">
                @error('shot_total') <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="text-sm">OK Part *</label>
                <input type="number" min="0" wire:model.defer="ok_part" class="mt-1 w-full rounded border-gray-300">
                @error('ok_part') <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="text-sm">NG Part *</label>
                <input type="number" min="0" wire:model.defer="ng_part" class="mt-1 w-full rounded border-gray-300">
                @error('ng_part') <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="text-sm">Cycle Time Avg (sec)</label>
                <input type="number" min="1" wire:model.defer="cycle_time_avg_sec" class="mt-1 w-full rounded border-gray-300">
                @error('cycle_time_avg_sec') <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="md:col-span-4">
                <label class="text-sm">Notes</label>
                <textarea wire:model.defer="notes" class="mt-1 w-full rounded border-gray-300" rows="2"></textarea>
                @error('notes') <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="mt-6">
            <div class="flex items-center justify-between mb-2">
                <div class="font-semibold">Defect Breakdown (sum qty = NG Part)</div>
                <button type="button" wire:click="addDefectRow" class="text-sm text-blue-600">+ Add row</button>
            </div>

            <div class="space-y-2">
                @foreach($defects as $i => $d)
                    <div class="grid grid-cols-12 gap-2 items-center">
                        <div class="col-span-7">
                            <input type="text" wire:model.defer="defects.{{ $i }}.defect_code"
                                class="w-full rounded border-gray-300"
                                placeholder="e.g. FLASH / SHORT / BLACK_DOT">
                            @error("defects.$i.defect_code") <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-span-3">
                            <input type="number" min="0" wire:model.defer="defects.{{ $i }}.qty"
                                class="w-full rounded border-gray-300">
                            @error("defects.$i.qty") <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-span-2 text-right">
                            <button type="button" wire:click="removeDefectRow({{ $i }})" class="text-sm text-red-600">
                                Remove
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mt-6">
            <button type="button" wire:click="save"
                class="px-4 py-2 rounded bg-gray-900 text-white text-sm"
                @if($run->end_ts) disabled @endif>
                Close Run
            </button>
        </div>
    </div>
</div>
