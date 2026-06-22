<div class="space-y-6">
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex justify-between items-center">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Update Location</h1>
            <p class="text-sm text-gray-500">{{ $mould->code }}</p>
        </div>
        <a wire:navigate href="{{ route('mobile.mould-detail', ['mould' => $mould->id]) }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </a>
    </div>

    @error('base')
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg text-sm relative" role="alert">
            <span class="block sm:inline">{{ $message }}</span>
        </div>
    @enderror

    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">New Location</label>
            <select wire:model.live="moveLocation" class="w-full rounded-lg border-gray-300 text-lg focus:border-blue-500 focus:ring-blue-500">
                <option value="TOOL_ROOM">Tool Room</option>
                <option value="WAREHOUSE">Warehouse</option>
                <option value="IN_TRANSIT">In Transit</option>
                <option value="MACHINE">Machine</option>
            </select>
            @error('moveLocation') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
        </div>

        @if($moveLocation === 'MACHINE')
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Select Machine</label>
            <select wire:model="moveMachineId" class="w-full rounded-lg border-gray-300 text-lg focus:border-blue-500 focus:ring-blue-500">
                <option value="">-- Select Machine --</option>
                @foreach($machines as $m)
                    <option value="{{ $m->id }}">{{ $m->code }} - {{ $m->name }}</option>
                @endforeach
            </select>
            @error('moveMachineId') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
        </div>
        @endif

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Note (Optional)</label>
            <textarea wire:model="moveNote" rows="2" class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
            @error('moveNote') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
        </div>
    </div>

    <button wire:click="submitMoveMould" class="w-full bg-blue-600 text-white py-4 rounded-xl font-bold shadow-md flex items-center justify-center gap-2 active:scale-95 transition-transform mb-6">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
        Move Mould
    </button>
</div>
