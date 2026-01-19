<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-gray-900 to-gray-600 tracking-tight">Master Moulds</h1>
            <p class="text-gray-500 mt-1">Manage mould repository and configurations</p>
        </div>

        <button wire:click="createNew"
            class="group flex items-center gap-2 px-5 py-2.5 rounded-full bg-gray-900 text-white text-sm font-medium shadow-lg shadow-gray-200 hover:shadow-xl hover:bg-black transition-all transform hover:-translate-y-0.5">
            <svg class="w-4 h-4 text-gray-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>New Mould</span>
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
                    {{ $mouldId ? 'Edit Mould' : 'Create Mould' }}
                </h2>

                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Code <span class="text-red-500">*</span></label>
                        <input type="text" wire:model.defer="code"
                            class="w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all placeholder-gray-400"
                            placeholder="e.g. M-0001">
                        @error('code') <div class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                        <input type="text" wire:model.defer="name"
                            class="w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all placeholder-gray-400"
                            placeholder="e.g. Mould Cup 250ml">
                        @error('name') <div class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cavities <span class="text-red-500">*</span></label>
                        <input type="number" min="1" wire:model.defer="cavities"
                            class="w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                        @error('cavities') <div class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</div> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Min Tonnage</label>
                            <div class="relative">
                                <input type="number" min="0" wire:model.defer="min_tonnage_t"
                                    class="w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all pr-8">
                                <span class="absolute right-3 top-2.5 text-xs text-gray-400 font-medium">T</span>
                            </div>
                            @error('min_tonnage_t') <div class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Max Tonnage</label>
                            <div class="relative">
                                <input type="number" min="0" wire:model.defer="max_tonnage_t"
                                    class="w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all pr-8">
                                <span class="absolute right-3 top-2.5 text-xs text-gray-400 font-medium">T</span>
                            </div>
                            @error('max_tonnage_t') <div class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">PM Shot Interval</label>
                            <input type="number" min="0" wire:model.defer="pm_interval_shot"
                                class="w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                            @error('pm_interval_shot') <div class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">PM Days Interval</label>
                            <input type="number" min="0" wire:model.defer="pm_interval_days"
                                class="w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                            @error('pm_interval_days') <div class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Customer</label>
                        <input type="text" wire:model.defer="customer"
                            class="w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                        @error('customer') <div class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Resin</label>
                        <input type="text" wire:model.defer="resin"
                            class="w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                        @error('resin') <div class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Commissioned At</label>
                        <input type="date" wire:model.defer="commissioned_at"
                            class="w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                        @error('commissioned_at') <div class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <div class="relative">
                            <select wire:model.defer="status" class="w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all appearance-none">
                                @foreach($statusOptions as $opt)
                                    <option value="{{ $opt->value }}">{{ $opt->label() }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                        @error('status') <div class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</div> @enderror
                    </div>

                    <div class="flex gap-3 pt-4 border-t border-gray-100">
                        <button wire:click="save"
                            class="flex-1 px-4 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-medium shadow-md shadow-blue-100 hover:bg-blue-700 hover:shadow-lg transition-all">
                            {{ $mouldId ? 'Update Mould' : 'Save Mould' }}
                        </button>

                        @if($mouldId)
                            <button type="button" wire:click="createNew"
                                class="px-4 py-2.5 rounded-xl border border-gray-200 text-gray-600 text-sm font-medium hover:bg-gray-50 transition-all">
                                Cancel
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- LIST --}}
        <div class="lg:col-span-2">
            <div class="bg-white/80 backdrop-blur-xl shadow-sm rounded-3xl border border-white/50 flex flex-col h-full">
                <div class="p-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center gap-4 justify-between bg-white/40 rounded-t-3xl">
                    <div class="relative flex-1 max-w-md">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" wire:model.debounce.400ms="search"
                            class="pl-10 w-full rounded-xl border-gray-200 bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all placeholder-gray-400"
                            placeholder="Search code, name, or customer...">
                    </div>

                    <select wire:model="perPage" class="rounded-xl border-gray-200 bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm py-2">
                        <option value="10">10 per page</option>
                        <option value="25">25 per page</option>
                        <option value="50">50 per page</option>
                    </select>
                </div>

                <div class="overflow-x-auto flex-1">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50/50 text-left">
                                <th class="py-3 px-4 font-semibold text-gray-600">Code</th>
                                <th class="py-3 px-4 font-semibold text-gray-600">Name</th>
                                <th class="py-3 px-4 font-semibold text-gray-600">Cavities</th>
                                <th class="py-3 px-4 font-semibold text-gray-600">Status</th>
                                <th class="py-3 px-4 font-semibold text-gray-600 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($moulds as $m)
                                <tr class="hover:bg-blue-50/50 transition-colors group">
                                    <td class="py-3 px-4 font-medium text-gray-900 group-hover:text-blue-700 transition-colors">{{ $m->code }}</td>
                                    <td class="py-3 px-4 text-gray-600">
                                        <div>{{ $m->name }}</div>
                                        @if($m->customer)
                                            <div class="text-xs text-gray-400">{{ $m->customer }}</div>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-gray-600">{{ $m->cavities }}</td>
                                    <td class="py-3 px-4">
                                        @if($m->status instanceof \App\Enums\MouldStatus)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                                {{ $m->status->label() }}
                                            </span>
                                        @else
                                            {{ $m->status }}
                                        @endif
                                    </td>
                                    <td class="text-right py-3 px-4">
                                        <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <button wire:click="edit('{{ $m->id }}')" class="p-1 rounded hover:bg-blue-100 text-blue-600 transition-colors" title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </button>
                                            <button
                                                wire:click="delete('{{ $m->id }}')"
                                                onclick="return confirm('Hapus mould {{ $m->code }}?')"
                                                class="p-1 rounded hover:bg-red-100 text-red-600 transition-colors" title="Delete">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-gray-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                            <p class="text-base font-medium">No moulds found</p>
                                            <p class="text-sm mt-1">Try adjusting your search</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-gray-100 bg-white/40 rounded-b-3xl">
                    {{ $moulds->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
