<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-gray-900 to-gray-600 tracking-tight">Maintenance Log</h1>
            <p class="text-gray-500 mt-1">Track and manage preventative & corrective actions</p>
        </div>

        <button type="button" wire:click="createNew"
            class="group flex items-center gap-2 px-5 py-2.5 rounded-full bg-gray-900 text-white text-sm font-medium shadow-lg shadow-gray-200 hover:shadow-xl hover:bg-black transition-all transform hover:-translate-y-0.5">
            <svg class="w-4 h-4 text-gray-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>New Entry</span>
        </button>
    </div>

    @if (session('success'))
        <div class="mb-6 p-4 rounded-2xl bg-green-50 text-green-800 text-sm border border-green-100 flex items-center gap-2 shadow-sm" x-data="{ show: true }" x-show="show">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span class="font-medium">{{ session('success') }}</span>
            <button @click="show = false" class="ml-auto text-green-600 hover:text-green-800"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- FORM --}}
        <div class="lg:col-span-1">
            <div class="bg-white/80 backdrop-blur-xl shadow-sm rounded-3xl border border-white/50 p-6 sticky top-24">
                <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <div class="w-1 h-6 bg-blue-500 rounded-full"></div>
                    {{ $idEdit ? 'Edit Entry' : 'New Entry' }}
                </h2>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mould <span class="text-red-500">*</span></label>
                        <select wire:model.defer="mould_id" class="w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all appearance-none">
                            <option value="">Select Mould</option>
                            @foreach ($moulds as $m)
                                <option value="{{ $m->id }}">{{ $m->code }} - {{ $m->name }}</option>
                            @endforeach
                        </select>
                        @error('mould_id') <div class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</div> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Start <span class="text-red-500">*</span></label>
                            <input type="datetime-local" wire:model.defer="start_ts"
                                class="w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm">
                            @error('start_ts') <div class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">End <span class="text-red-500">*</span></label>
                            <input type="datetime-local" wire:model.defer="end_ts"
                                class="w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm">
                            @error('end_ts') <div class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Type <span class="text-red-500">*</span></label>
                            <select wire:model.defer="type" class="w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all appearance-none">
                                <option value="PM">PM (Preventive)</option>
                                <option value="CM">CM (Corrective)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Downtime (min)</label>
                            <input type="number" min="0" wire:model.defer="downtime_min"
                                class="w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <input type="text" wire:model.defer="description" class="w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="Brief summary...">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Parts Used</label>
                        <textarea wire:model.defer="parts_used" class="w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" rows="2" placeholder="List parts replaced..."></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cost</label>
                        <input type="number" wire:model.defer="cost" class="w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="Optional cost...">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Next Due Shot</label>
                            <input type="number" min="0" wire:model.defer="next_due_shot"
                                class="w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Next Due Date</label>
                            <input type="date" wire:model.defer="next_due_date"
                                class="w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Performed By</label>
                        <input type="text" wire:model.defer="performed_by" class="w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="Technician name">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea wire:model.defer="notes" class="w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" rows="2" placeholder="Additional details..."></textarea>
                    </div>

                    <div class="flex gap-3 pt-4 border-t border-gray-100">
                        <button type="button" wire:click="save" class="flex-1 px-4 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-medium shadow-md shadow-blue-100 hover:bg-blue-700 hover:shadow-lg transition-all">
                            {{ $idEdit ? 'Update Entry' : 'Save Entry' }}
                        </button>
                        @if ($idEdit)
                            <button type="button" wire:click="createNew"
                                class="px-4 py-2.5 rounded-xl border border-gray-200 text-gray-600 text-sm font-medium hover:bg-gray-50 transition-all">
                                Cancel
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- TIMELINE FEED --}}
        <div class="lg:col-span-2">
            <div class="bg-white/80 backdrop-blur-xl shadow-sm rounded-3xl border border-white/50 flex flex-col h-full">
                {{-- Search Bar --}}
                <div class="p-4 border-b border-gray-100 bg-white/40 rounded-t-3xl sticky top-0 z-10 backdrop-blur-md">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="relative flex-1">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input type="text" wire:model.debounce.400ms="search" class="pl-10 w-full rounded-xl border-gray-200 bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all placeholder-gray-400 shadow-sm"
                                placeholder="Search mould code, description, or technician...">
                        </div>
                        <select wire:model="perPage" class="rounded-xl border-gray-200 bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm py-2 shadow-sm">
                            <option value="10">10 entries</option>
                            <option value="25">25 entries</option>
                            <option value="50">50 entries</option>
                        </select>
                    </div>
                </div>

                {{-- Timeline Content --}}
                <div class="p-4 space-y-4">
                    @forelse($events as $e)
                        <div class="group relative bg-white border border-gray-100 rounded-2xl p-5 hover:shadow-md transition-all">
                            {{-- Timeline Connector --}}
                            @if(!$loop->last)
                                <div class="absolute inset-y-0 left-8 top-12 border-l border-gray-100 -z-10 group-hover:border-gray-200 transition-colors"></div>
                            @endif

                            <div class="flex gap-4">
                                {{-- Icon Box --}}
                                <div class="shrink-0">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $e->type === 'PM' ? 'bg-blue-50 text-blue-600' : 'bg-red-50 text-red-600' }}">
                                        @if($e->type === 'PM')
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                                        @else
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                        @endif
                                    </div>
                                </div>

                                {{-- Details --}}
                                <div class="flex-1">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <h3 class="font-bold text-gray-900">{{ $e->mould?->code }} <span class="text-gray-400 font-normal mx-1">/</span> {{ $e->description ?? 'Maintenance Entry' }}</h3>
                                            <div class="text-sm text-gray-500 mt-0.5">
                                                {{ $e->mould?->name }}
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $e->type === 'PM' ? 'bg-blue-50 text-blue-700' : 'bg-red-50 text-red-700' }}">
                                                {{ $e->type }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4 text-sm bg-gray-50/50 rounded-xl p-3 border border-gray-100">
                                        <div>
                                            <div class="text-xs text-gray-400">Date</div>
                                            <div class="font-medium text-gray-700">{{ \Carbon\Carbon::parse($e->end_ts)->format('d M Y') }}</div>
                                        </div>
                                        <div>
                                            <div class="text-xs text-gray-400">Downtime</div>
                                            <div class="font-medium text-gray-700">{{ $e->downtime_min }} min</div>
                                        </div>
                                        <div>
                                            <div class="text-xs text-gray-400">Next Shot</div>
                                            <div class="font-medium text-gray-700">{{ $e->next_due_shot ?? '-' }}</div>
                                        </div>
                                        <div>
                                            <div class="text-xs text-gray-400">Technician</div>
                                            <div class="font-medium text-gray-700">{{ $e->performed_by ?? '-' }}</div>
                                        </div>
                                    </div>

                                    <div class="mt-4 flex items-center justify-between">
                                        <div class="text-xs text-gray-400">
                                            Completed {{ \Carbon\Carbon::parse($e->end_ts)->diffForHumans() }}
                                        </div>
                                        <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <button wire:click="edit('{{ $e->id }}')" class="p-1.5 rounded-lg text-blue-600 hover:bg-blue-50 transition-colors" title="Edit">
                                                Edit
                                            </button>
                                            <button wire:click="delete('{{ $e->id }}')" onclick="return confirm('Delete this maintenance record?')" class="p-1.5 rounded-lg text-red-600 hover:bg-red-50 transition-colors" title="Delete">
                                                Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center py-12 text-center text-gray-500">
                            <div class="bg-gray-50 rounded-full p-6 mb-4">
                                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900">No maintenance events found</h3>
                            <p class="text-sm mt-1 max-w-sm">Try adjusting your search filters or add a new maintenance entry.</p>
                        </div>
                    @endforelse
                </div>

                <div class="p-4 border-t border-gray-100 bg-white/40 rounded-b-3xl">
                    {{ $events->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
