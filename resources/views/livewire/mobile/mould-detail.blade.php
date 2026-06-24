<div class="space-y-6">
    @if(session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg text-sm relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Header --}}
    <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-100">
        <div class="flex justify-between items-start">
            <div>
                 <span class="inline-flex items-center px-2 py-1 rounded text-xs font-bold bg-blue-100 text-blue-700 mb-2">
                    {{ $mould->status }}
                </span>
                <h1 class="text-2xl font-bold text-slate-900">{{ $mould->code }}</h1>
                <p class="text-sm text-slate-500">{{ $mould->name }}</p>
            </div>
            <div class="text-right">
                <div class="text-xs text-slate-400">Location</div>
                <div class="font-medium text-slate-700">{{ $mould->location ?? 'N/A' }}</div>
            </div>
        </div>
    </div>

    {{-- Actions --}}
    <div class="space-y-3">
        @if($activeRun)
            <div class="bg-green-50 border border-green-200 p-4 rounded-xl">
                <h3 class="font-bold text-green-800 mb-1">Currently Running</h3>
                <p class="text-sm text-green-700 mb-3">
                    Machine: <strong>{{ $activeRun->machine->code }}</strong><br>
                    Started: {{ $activeRun->start_ts->format('H:i') }}
                </p>
                @can('runs.close')
                <a wire:navigate href="{{ route('mobile.runs.end', ['run' => $activeRun->id]) }}" class="block w-full text-center bg-green-600 text-white py-3 rounded-lg font-bold shadow-sm active:scale-95 transition-transform">
                    End Production Run
                </a>
                @endcan
            </div>
        @else
             @can('runs.close')
             {{-- Start Run Button --}}
             <a wire:navigate href="{{ route('mobile.runs.start', ['mould' => $mould->id]) }}" class="w-full bg-blue-600 text-white py-4 rounded-xl font-bold shadow-md flex items-center justify-center gap-2 active:scale-95 transition-transform">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Start Production Run
            </a>
            @endcan
        @endif

        @can('locations.move')
        {{-- Move Button --}}
        <a wire:navigate href="{{ route('mobile.moulds.move', ['mould' => $mould->id]) }}" class="w-full bg-white border border-slate-200 text-slate-700 py-4 rounded-xl font-bold shadow-sm flex items-center justify-center gap-2 active:scale-95 transition-transform">
             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
            Update Location
        </a>
        @endcan

        @can('maintenance_events.create')
        {{-- Maintenance Button --}}
        <button wire:click="openMaintenanceModal" class="w-full bg-orange-600 text-white py-4 rounded-xl font-bold shadow-md flex items-center justify-center gap-2 active:scale-95 transition-transform">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            Request New Maintenance
        </button>
        @endcan
    </div>

    @can('maintenance_events.create')
    @if(count($this->activeWorkOrders) > 0)
    {{-- Active Work Orders --}}
    <div class="pt-4 border-t border-slate-200">
        <h2 class="text-sm font-bold text-slate-900 mb-3 uppercase tracking-wider">Active Work Orders</h2>
        <div class="space-y-3">
            @foreach($this->activeWorkOrders as $wo)
                <div wire:key="wo-{{ $wo->id }}" class="bg-white p-4 rounded-xl shadow-sm border border-slate-200">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-bold {{ $wo->status === 'IN_PROGRESS' ? 'bg-blue-100 text-blue-700' : ($wo->status === 'REQUESTED' ? 'bg-orange-100 text-orange-700' : 'bg-purple-100 text-purple-700') }}">
                                {{ str_replace('_', ' ', $wo->status) }}
                            </span>
                            <div class="font-bold text-slate-900 mt-2">
                                {{ $wo->type }}{{ $wo->pm_subtype ? ' - ' . $wo->pm_subtype : '' }} Work Order
                            </div>
                            <div class="text-xs text-slate-500 mt-1">{{ $wo->description }}</div>
                        </div>
                    </div>
                    
                    @if($wo->status === 'REQUESTED' || $wo->status === 'APPROVED')
                        <button type="button" wire:click="startWorkOrder('{{ $wo->id }}')" class="w-full bg-blue-600 text-white py-3 rounded-lg font-bold shadow-sm active:scale-95 transition-transform text-sm">
                            Start Work Now
                        </button>
                    @elseif($wo->status === 'IN_PROGRESS')
                        <a href="{{ route('mobile.jobs.complete', $wo) }}" class="block w-full text-center bg-green-600 text-white py-3 rounded-lg font-bold shadow-sm active:scale-95 transition-transform text-sm">
                            Complete Work Order
                        </a>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
    @endif
    @endcan

    {{-- Stats --}}
    <div class="grid grid-cols-2 gap-4">
        <div class="bg-white p-3 rounded-lg border border-slate-100 text-center">
            <div class="text-xs text-slate-400">Total Shots</div>
            <div class="font-mono font-bold text-lg">{{ number_format($mould->total_shots) }}</div>
        </div>
        <div class="bg-white p-3 rounded-lg border border-slate-100 text-center">
            <div class="text-xs text-slate-400">Next PM</div>
            <div class="font-mono font-bold text-lg text-{{ $mould->next_pm_due ? 'red' : 'slate' }}-600">
                {{ number_format($mould->pm_interval_shot - ($mould->total_shots - $mould->last_pm_at_shot)) }}
            </div>
        </div>
    </div>

    {{-- Maintenance Modal --}}
    @if($showMaintenanceModal)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-sm overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                <h3 class="font-bold text-slate-900">Log Maintenance</h3>
                <button wire:click="$set('showMaintenanceModal', false)" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="p-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Type</label>
                    <select wire:model.live="maintenanceType" class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="CM">Corrective (CM)</option>
                        <option value="PM">Preventive (PM)</option>
                    </select>
                    @error('maintenanceType') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
                @if($maintenanceType === 'PM')
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">PM Subtype</label>
                    <select wire:model.defer="maintenancePmSubtype" class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select Subtype (Optional)</option>
                        <option value="DAILY">Daily Maintenance</option>
                        <option value="WEEKLY">Weekly Maintenance</option>
                        <option value="PPM">PPM / Overhaul</option>
                    </select>
                    @error('maintenancePmSubtype') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
                @endif
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        Description @if($maintenanceType === 'PM') <span class="text-slate-400 font-normal">(Optional)</span> @endif
                    </label>
                    <textarea wire:model="maintenanceDescription" rows="3" class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="{{ $maintenanceType === 'PM' ? 'Optional description...' : 'Describe the issue or work needed...' }}"></textarea>
                    @error('maintenanceDescription') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="p-4 border-t border-slate-100 flex gap-2">
                <button wire:click="$set('showMaintenanceModal', false)" class="flex-1 py-2 bg-slate-100 text-slate-700 rounded-lg font-medium text-sm">Cancel</button>
                <button wire:click="submitMaintenance" class="flex-1 py-2 bg-blue-600 text-white rounded-lg font-medium text-sm">Submit</button>
            </div>
        </div>
    </div>
    @endif
</div>
