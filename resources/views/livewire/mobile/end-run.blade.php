<div class="space-y-6">
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex justify-between items-center">
        <div>
            <h1 class="text-xl font-bold text-gray-900">End Production Run</h1>
            <p class="text-sm text-gray-500">{{ $run->mould->code }} on {{ $run->machine->code }}</p>
        </div>
        <a href="{{ route('mobile.mould-detail', ['mould' => $run->mould_id]) }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </a>
    </div>

    @error('base')
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ $message }}</span>
        </div>
    @enderror

    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Total Shots</label>
            <input type="number" wire:model="shot_total" class="w-full rounded-lg border-gray-300 text-lg focus:border-blue-500 focus:ring-blue-500 font-mono">
            @error('shot_total') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">OK Parts</label>
                <input type="number" wire:model="ok_part" class="w-full rounded-lg border-gray-300 text-lg focus:border-blue-500 focus:ring-blue-500 font-mono text-green-700">
                @error('ok_part') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">NG Parts (Rejects)</label>
                <input type="number" wire:model="ng_part" class="w-full rounded-lg border-gray-300 text-lg focus:border-blue-500 focus:ring-blue-500 font-mono text-red-700">
                @error('ng_part') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Avg Cycle Time (sec)</label>
            <input type="number" wire:model="cycle_time_avg_sec" class="w-full rounded-lg border-gray-300 text-lg focus:border-blue-500 focus:ring-blue-500 font-mono">
            @error('cycle_time_avg_sec') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 space-y-4">
        <h3 class="font-bold text-gray-900 border-b border-gray-100 pb-2">Defects</h3>
        
        <div class="space-y-3">
            @foreach($defects as $i => $defect)
                <div class="flex gap-2 items-center bg-gray-50 p-2 rounded-lg border border-gray-100">
                    <div class="flex-1">
                        <input type="text" wire:model="defects.{{ $i }}.defect_code" placeholder="Defect Code (e.g., FLASH)" class="w-full rounded border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div class="w-20">
                        <input type="number" wire:model="defects.{{ $i }}.qty" class="w-full rounded border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500 font-mono" placeholder="Qty">
                    </div>
                    <button wire:click="removeDefectRow({{ $i }})" class="text-red-500 p-2 hover:bg-red-50 rounded">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </div>
            @endforeach
        </div>
        
        <button wire:click="addDefectRow" class="w-full py-2 border-2 border-dashed border-gray-300 text-gray-500 rounded-lg text-sm font-medium hover:bg-gray-50">
            + Add Defect Log
        </button>
    </div>

    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
        <textarea wire:model="notes" rows="2" class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Optional notes..."></textarea>
    </div>

    <button wire:click="save" class="w-full bg-blue-600 text-white py-4 rounded-xl font-bold shadow-md flex items-center justify-center gap-2 active:scale-95 transition-transform mb-6">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        Save and End Run
    </button>
</div>
