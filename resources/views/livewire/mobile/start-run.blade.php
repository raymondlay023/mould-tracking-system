<div class="space-y-6">
    <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-100 flex justify-between items-center">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Start Production Run</h1>
            <p class="text-sm text-slate-500">{{ $mould->code }}</p>
        </div>
        <a wire:navigate href="{{ route('mobile.mould-detail', ['mould' => $mould->id]) }}" class="text-slate-400 hover:text-slate-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </a>
    </div>

    @error('base')
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg text-sm relative" role="alert">
            <span class="block sm:inline">{{ $message }}</span>
        </div>
    @enderror
    @if(session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg text-sm relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-100 space-y-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Select Machine</label>
            <select wire:model="startMachineId" class="w-full rounded-lg border-slate-300 text-lg focus:border-blue-500 focus:ring-blue-500">
                <option value="">-- Select Machine --</option>
                @foreach($machines as $m)
                    <option value="{{ $m->id }}">{{ $m->code }} - {{ $m->name }}</option>
                @endforeach
            </select>
            @error('startMachineId') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Operator Name (Optional)</label>
            <input type="text" wire:model="startOperatorName" class="w-full rounded-lg border-slate-300 text-lg focus:border-blue-500 focus:ring-blue-500">
            @error('startOperatorName') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Notes (Optional)</label>
            <textarea wire:model="startNotes" rows="2" class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
            @error('startNotes') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
        </div>
    </div>

    <button wire:click="submitStartRun" class="w-full bg-blue-600 text-white py-4 rounded-xl font-bold shadow-md flex items-center justify-center gap-2 active:scale-95 transition-transform mb-6">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        Start Run
    </button>
</div>
