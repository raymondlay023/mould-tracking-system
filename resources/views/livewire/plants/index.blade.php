<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-gray-900 to-gray-600 tracking-tight">Master Plant</h1>
            <p class="text-sm text-gray-500 mt-1">Manage production plant locations</p>
        </div>
        <button type="button" wire:click="createNew" class="px-5 py-2.5 rounded-xl bg-gray-900 text-white font-medium hover:bg-gray-800 transition-all shadow-lg shadow-gray-200 transform hover:-translate-y-0.5 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            New Plant
        </button>
    </div>

    @if (session('success'))
        <div class="mb-6 p-4 rounded-2xl bg-green-50 text-green-800 text-sm border border-green-100 flex items-center gap-3 shadow-sm" x-data="{ show: true }" x-show="show">
            <div class="shrink-0 w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <div class="flex-1 font-medium">{{ session('success') }}</div>
            <button @click="show = false" class="text-green-600 hover:text-green-800"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Form Section --}}
        <div class="bg-white/80 backdrop-blur-xl shadow-xl rounded-3xl border border-white/50 p-6 h-fit sticky top-24">
            <div class="flex items-center gap-3 mb-6 border-b border-gray-100 pb-4">
                <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <h2 class="text-lg font-bold text-gray-900">{{ $plantId ? 'Edit Plant' : 'Create New Plant' }}</h2>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Plant Name <span class="text-red-500">*</span></label>
                    <input type="text" wire:model.defer="name" class="w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all text-sm py-2.5 px-4 placeholder-gray-400" placeholder="e.g. Plant Jakarta">
                    @error('name') <div class="text-red-500 text-xs mt-1.5 font-medium flex items-center gap-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>{{ $message }}</div> @enderror
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="button" wire:click="save" class="flex-1 py-2.5 rounded-xl bg-blue-600 text-white font-semibold text-sm shadow-md shadow-blue-200 hover:shadow-lg hover:bg-blue-700 transition-all transform hover:-translate-y-0.5">
                        {{ $plantId ? 'Update Plant' : 'Save Plant' }}
                    </button>
                    @if($plantId)
                        <button type="button" wire:click="createNew" class="px-4 py-2.5 rounded-xl border border-gray-200 text-gray-600 font-medium text-sm hover:bg-gray-50 transition-colors">
                            Cancel
                        </button>
                    @endif
                </div>
            </div>
        </div>

        {{-- Table Section --}}
        <div class="lg:col-span-2">
            <div class="bg-white/80 backdrop-blur-xl shadow-xl rounded-3xl border border-white/50 overflow-hidden">
                <div class="p-4 border-b border-gray-100 flex items-center gap-4 bg-gray-50/30">
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" wire:model.debounce.400ms="search" class="w-full rounded-xl border-gray-200 bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm py-2 pl-9" placeholder="Search plants...">
                    </div>
                    <select wire:model="perPage" class="rounded-xl border-gray-200 bg-white text-sm py-2 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        <option value="10">10 per page</option>
                        <option value="25">25 per page</option>
                        <option value="50">50 per page</option>
                    </select>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100">
                                <th class="py-3 px-6 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Plant Name</th>
                                <th class="py-3 px-6 text-right font-semibold text-gray-600 uppercase tracking-wider text-xs">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                        @forelse($plants as $p)
                            <tr class="hover:bg-blue-50/30 transition-colors group">
                                <td class="py-3.5 px-6 font-medium text-gray-900 group-hover:text-blue-600 transition-colors">
                                    {{ $p->name }}
                                </td>
                                <td class="py-3.5 px-6 text-right">
                                    <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button type="button" wire:click="edit('{{ $p->id }}')" class="p-1.5 rounded-lg hover:bg-blue-50 text-blue-600 transition-colors" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        </button>
                                        <button type="button" wire:click="delete('{{ $p->id }}')" 
                                            onclick="return confirm('Hapus plant ini?')"
                                            class="p-1.5 rounded-lg hover:bg-red-50 text-red-600 transition-colors" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="py-8 text-center text-gray-500 bg-gray-50/30">
                                    <div class="flex flex-col items-center gap-2">
                                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <span class="text-sm font-medium">No plants found</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                @if($plants->hasPages())
                    <div class="p-4 border-t border-gray-100 bg-gray-50/30">
                        {{ $plants->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
