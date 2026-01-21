<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-gray-900 to-gray-600 tracking-tight">Work Orders</h1>
            <p class="text-gray-500 mt-1">Manage active maintenance tasks</p>
        </div>
        <div class="flex gap-4 items-center">
             <button wire:click="create" class="px-4 py-2 bg-blue-600 text-white rounded-xl text-sm font-bold hover:bg-blue-700 shadow-md shadow-blue-100 transition-all">
                + New Work Order
            </button>
             <a href="{{ route('maintenance.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">View History &rarr;</a>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-6 p-4 rounded-xl bg-green-50 text-green-800 text-sm border border-green-100 shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        {{-- REQUESTED --}}
        <div class="bg-gray-50 rounded-2xl p-4 border border-gray-200/50">
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4 px-1 flex items-center justify-between">
                <span>Requested</span>
                <span class="bg-gray-200 text-gray-600 px-2 py-0.5 rounded-full text-[10px]">{{ $cols['REQUESTED']->count() }}</span>
            </h3>
            <div class="space-y-3">
                @foreach($cols['REQUESTED'] as $ev)
                    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-all">
                        <div class="flex justify-between items-start mb-2">
                            <div class="font-bold text-gray-900">{{ $ev->mould->code }}</div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-600">{{ $ev->type }}</span>
                        </div>
                        <p class="text-sm text-gray-600 mb-3">{{ $ev->description }}</p>
                        <div class="text-xs text-gray-400 mb-3">Created {{ $ev->created_at->diffForHumans() }}</div>
                        
                        <button wire:click="approve('{{ $ev->id }}')" class="w-full py-2 bg-gray-900 text-white rounded-lg text-xs font-semibold hover:bg-black transition-colors">
                            Approve
                        </button>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- APPROVED --}}
        <div class="bg-blue-50/50 rounded-2xl p-4 border border-blue-100/50">
             <h3 class="text-xs font-bold text-blue-500 uppercase tracking-wider mb-4 px-1 flex items-center justify-between">
                <span>Approved</span>
                <span class="bg-blue-100 text-blue-600 px-2 py-0.5 rounded-full text-[10px]">{{ $cols['APPROVED']->count() }}</span>
            </h3>
            <div class="space-y-3">
                @foreach($cols['APPROVED'] as $ev)
                    <div class="bg-white p-4 rounded-xl shadow-sm border border-blue-100 hover:shadow-md transition-all relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-1 h-full bg-blue-400"></div>
                         <div class="flex justify-between items-start mb-2 pl-2">
                            <div class="font-bold text-gray-900">{{ $ev->mould->code }}</div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-600">{{ $ev->type }}</span>
                        </div>
                        <p class="text-sm text-gray-600 mb-3 pl-2">{{ $ev->description }}</p>
                        
                        <button wire:click="start('{{ $ev->id }}')" class="w-full py-2 bg-blue-600 text-white rounded-lg text-xs font-semibold hover:bg-blue-700 transition-colors">
                            Start Work
                        </button>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- IN PROGRESS --}}
        <div class="bg-amber-50/50 rounded-2xl p-4 border border-amber-100/50">
             <h3 class="text-xs font-bold text-amber-600 uppercase tracking-wider mb-4 px-1 flex items-center justify-between">
                <span>In Progress</span>
                <span class="bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full text-[10px]">{{ $cols['IN_PROGRESS']->count() }}</span>
            </h3>
            <div class="space-y-3">
                 @foreach($cols['IN_PROGRESS'] as $ev)
                    <div class="bg-white p-4 rounded-xl shadow-sm border border-amber-100 hover:shadow-md transition-all relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-1 h-full bg-amber-400"></div>
                         <div class="flex justify-between items-start mb-2 pl-2">
                            <div class="font-bold text-gray-900">{{ $ev->mould->code }}</div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 animate-pulse">ACTIVE</span>
                        </div>
                        <p class="text-sm text-gray-600 mb-3 pl-2">{{ $ev->description }}</p>
                        
                        <button wire:click="initiateCompletion('{{ $ev->id }}')" class="w-full py-2 bg-green-600 text-white rounded-lg text-xs font-semibold hover:bg-green-700 transition-colors">
                            Complete
                        </button>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    {{-- CREATE MODAL --}}
    @if($creating)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 animate-in fade-in zoom-in duration-200">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Request Work Order</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase">Mould</label>
                        <select wire:model.defer="newMouldId" class="w-full mt-1 rounded-lg border-gray-200 bg-gray-50 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all appearance-none">
                            <option value="">Select Mould</option>
                            @foreach ($moulds as $m)
                                <option value="{{ $m->id }}">{{ $m->code }} - {{ $m->name }}</option>
                            @endforeach
                        </select>
                        @error('newMouldId') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase">Type</label>
                         <select wire:model.defer="newType" class="w-full mt-1 rounded-lg border-gray-200 bg-gray-50 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all appearance-none">
                            <option value="CM">CM (Corrective)</option>
                            <option value="PM">PM (Preventive)</option>
                        </select>
                        @error('newType') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                    </div>

                     <div>
                        <label class="text-xs font-bold text-gray-500 uppercase">Date Requested</label>
                        <input type="datetime-local" wire:model.defer="newStartTs" class="w-full mt-1 rounded-lg border-gray-200 bg-gray-50 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                        @error('newStartTs') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                    </div>

                     <div>
                        <label class="text-xs font-bold text-gray-500 uppercase">Description</label>
                        <textarea wire:model.defer="newDescription" rows="2" class="w-full mt-1 rounded-lg border-gray-200 bg-gray-50 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="Briefly describe the issue..."></textarea>
                        @error('newDescription') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="flex gap-3 mt-6">
                    <button wire:click="cancelCreate" class="flex-1 py-2.5 rounded-xl border border-gray-200 text-gray-600 font-medium hover:bg-gray-50">Cancel</button>
                    <button wire:click="saveNew" class="flex-1 py-2.5 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 shadow-lg shadow-blue-100">Submit Request</button>
                </div>
            </div>
        </div>
    @endif

    {{-- COMPLETION MODAL --}}
    @if($completingId)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 animate-in fade-in zoom-in duration-200">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Complete Work Order</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase">Downtime (min)</label>
                        <input type="number" wire:model.defer="downtime_min" class="w-full mt-1 rounded-lg border-gray-200 bg-gray-50 focus:ring-2 focus:ring-green-500/20 focus:border-green-500 transition-all">
                        @error('downtime_min') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                    </div>
                     <div>
                        <label class="text-xs font-bold text-gray-500 uppercase">Parts Used</label>
                        <textarea wire:model.defer="parts_used" rows="2" class="w-full mt-1 rounded-lg border-gray-200 bg-gray-50 focus:ring-2 focus:ring-green-500/20 focus:border-green-500 transition-all"></textarea>
                    </div>
                     <div>
                        <label class="text-xs font-bold text-gray-500 uppercase">Cost (Estimate)</label>
                        <input type="number" wire:model.defer="cost" class="w-full mt-1 rounded-lg border-gray-200 bg-gray-50 focus:ring-2 focus:ring-green-500/20 focus:border-green-500 transition-all">
                    </div>
                     <div>
                        <label class="text-xs font-bold text-gray-500 uppercase">Notes</label>
                        <textarea wire:model.defer="notes" rows="2" class="w-full mt-1 rounded-lg border-gray-200 bg-gray-50 focus:ring-2 focus:ring-green-500/20 focus:border-green-500 transition-all"></textarea>
                    </div>
                </div>

                <div class="flex gap-3 mt-6">
                    <button wire:click="cancelCompletion" class="flex-1 py-2.5 rounded-xl border border-gray-200 text-gray-600 font-medium hover:bg-gray-50">Cancel</button>
                    <button wire:click="complete" class="flex-1 py-2.5 rounded-xl bg-green-600 text-white font-bold hover:bg-green-700 shadow-lg shadow-green-100">Complete</button>
                </div>
            </div>
        </div>
    @endif
</div>
