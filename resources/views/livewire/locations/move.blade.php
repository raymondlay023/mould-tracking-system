<div class="max-w-2xl mx-auto px-4 py-12">
    <div class="text-center mb-10">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-50 text-blue-600 mb-4 shadow-sm ring-4 ring-blue-50/50">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
        </div>
        <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Move Mould Location</h1>
        <p class="text-gray-500 mt-2 text-lg">Transfer mould ownership and track physical location</p>
    </div>

    @if (session('success'))
        <div class="mb-8 p-4 rounded-2xl bg-green-50 text-green-800 text-sm border border-green-100 flex items-center gap-3 shadow-md transform transition-all hover:scale-[1.01]" x-data="{ show: true }" x-show="show">
            <div class="shrink-0 w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <div class="flex-1 font-medium text-base">{{ session('success') }}</div>
            <button @click="show = false" class="text-green-600 hover:text-green-800 transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
        </div>
    @endif

    <div class="bg-white/80 backdrop-blur-xl shadow-xl rounded-3xl border border-white/50 p-8 relative overflow-hidden">
        {{-- Decorative Background Blob --}}
        <div class="absolute -top-20 -right-20 w-64 h-64 bg-blue-50 rounded-full blur-3xl opacity-50 -z-10"></div>
        <div class="absolute -bottom-20 -left-20 w-64 h-64 bg-purple-50 rounded-full blur-3xl opacity-50 -z-10"></div>

        <div class="space-y-6 relative z-10">
            {{-- Step 1: Select Mould --}}
            <div>
                <label class="block text-sm font-semibold text-gray-900 mb-2">Select Mould to Move</label>
                <div class="relative group">
                    <select wire:model.defer="mould_id" class="w-full rounded-2xl border-gray-200 bg-white/60 focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all appearance-none py-3.5 pl-4 pr-10 shadow-sm text-gray-700 font-medium cursor-pointer hover:border-blue-300">
                        <option value="">-- Choose a mould --</option>
                        @foreach ($moulds as $m)
                            <option value="{{ $m->id }}">{{ $m->code }} - {{ $m->name }}</option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500 group-hover:text-blue-500 transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
                @error('mould_id') <div class="text-red-500 text-sm mt-1.5 font-medium flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg> {{ $message }}</div> @enderror
            </div>

            <div class="h-px bg-gradient-to-r from-transparent via-gray-200 to-transparent"></div>

            {{-- Step 2: Destination --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Destination Plant <span class="text-gray-400 font-normal">(Optional)</span></label>
                    <div class="relative group">
                        <select wire:model.defer="plant_id" class="w-full rounded-2xl border-gray-200 bg-white/60 focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all appearance-none py-3.5 pl-4 pr-10 shadow-sm text-gray-700 cursor-pointer hover:border-blue-300">
                            <option value="">-- Main Plant --</option>
                            @foreach ($plants as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500 group-hover:text-blue-500 transition-colors">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                    @error('plant_id') <div class="text-red-500 text-sm mt-1.5 font-medium">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">New Location Status</label>
                    <div class="relative group">
                        <select wire:model="location" class="w-full rounded-2xl border-gray-200 bg-white/60 focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all appearance-none py-3.5 pl-4 pr-10 shadow-sm text-gray-700 font-medium cursor-pointer hover:border-blue-300">
                            <option value="TOOL_ROOM">Tool Room</option>
                            <option value="WAREHOUSE">Warehouse</option>
                            <option value="IN_TRANSIT">In Transit</option>
                            <option value="MACHINE">Machine</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500 group-hover:text-blue-500 transition-colors">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                    @error('location') <div class="text-red-500 text-sm mt-1.5 font-medium">{{ $message }}</div> @enderror
                </div>
            </div>

            {{-- Conditional: Machine Selection --}}
            <div x-show="$wire.location === 'MACHINE'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="bg-blue-50/50 rounded-2xl p-5 border border-blue-100">
                    <label class="block text-sm font-semibold text-blue-900 mb-2">Select Machine</label>
                    <div class="relative">
                        <select wire:model.defer="machine_id" class="w-full rounded-xl border-blue-200 bg-white focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all appearance-none py-3 pl-4 pr-10 text-gray-700">
                            <option value="">-- Choose Machine --</option>
                            @foreach ($machines as $mc)
                                <option value="{{ $mc->id }}">{{ $mc->code }} ({{ $mc->plant?->name }} / {{ $mc->zone?->code }})</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-blue-500">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"></path></svg>
                        </div>
                    </div>
                    @error('machine_id') <div class="text-red-500 text-sm mt-1.5 font-medium">{{ $message }}</div> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-900 mb-2">Transfer Note</label>
                <div class="relative">
                    <input type="text" wire:model.defer="note" class="w-full rounded-2xl border-gray-200 bg-white/60 focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all py-3.5 pl-10 pr-4 shadow-sm placeholder-gray-400" placeholder="Optional remark...">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </div>
                </div>
            </div>

            <div class="pt-4">
                <button type="button" wire:click="move" class="w-full py-4 rounded-2xl bg-gradient-to-r from-blue-600 to-blue-700 text-white font-bold text-lg shadow-lg shadow-blue-200 hover:shadow-xl hover:from-blue-700 hover:to-blue-800 transition-all transform hover:-translate-y-0.5 active:translate-y-0 active:shadow-md flex items-center justify-center gap-2 group">
                    <span>Confirm Move</span>
                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>
            </div>
        </div>
    </div>
</div>
