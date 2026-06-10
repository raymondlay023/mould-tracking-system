<div class="max-w-7xl mx-auto px-4 py-8">

    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-indigo-900 to-indigo-600 tracking-tight">Parts Registry</h1>
            <p class="text-sm text-gray-500 mt-1">Manage registered parts and their mould associations</p>
        </div>
        @can('view_admin_panel')
            <button type="button" wire:click="createNew"
                class="px-5 py-2.5 rounded-xl bg-indigo-700 text-white font-medium hover:bg-indigo-800 transition-all shadow-lg shadow-indigo-200 transform hover:-translate-y-0.5 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Part
            </button>
        @endcan
    </div>

    {{-- Flash message --}}
    @if (session('success'))
        <div class="mb-6 p-4 rounded-2xl bg-green-50 text-green-800 text-sm border border-green-100 flex items-center gap-3 shadow-sm"
             x-data="{ show: true }" x-show="show">
            <div class="shrink-0 w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div class="flex-1 font-medium">{{ session('success') }}</div>
            <button @click="show = false" class="text-green-600 hover:text-green-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- ─── Form Panel ─── --}}
        @can('view_admin_panel')
        <div class="bg-white/80 backdrop-blur-xl shadow-xl rounded-3xl border border-white/50 p-6 h-fit sticky top-24">
            <div class="flex items-center gap-3 mb-6 border-b border-gray-100 pb-4">
                <div class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <h2 class="text-lg font-bold text-gray-900">
                    {{ $partId ? 'Edit Part' : 'Create New Part' }}
                </h2>
            </div>

            <div class="space-y-4">

                {{-- Mould --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                        Mould <span class="text-red-500">*</span>
                    </label>
                    <div class="relative group">
                        <select wire:model.defer="mould_id"
                            class="w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all text-sm py-2.5 pl-4 pr-10 appearance-none">
                            <option value="">-- Select Mould --</option>
                            @foreach ($moulds as $m)
                                <option value="{{ $m->id }}">{{ $m->code }} — {{ $m->name }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500 group-hover:text-indigo-500 transition-colors">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                    @error('mould_id')
                        <div class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Part Number --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                        Part Number <span class="text-red-500">*</span>
                    </label>
                    <input type="text" wire:model.defer="part_number"
                        class="w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all text-sm py-2.5 px-4 placeholder-gray-400"
                        placeholder="e.g. PT-00123">
                    @error('part_number')
                        <div class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Part Name --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                        Part Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" wire:model.defer="part_name"
                        class="w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all text-sm py-2.5 px-4 placeholder-gray-400"
                        placeholder="e.g. Housing Cover Left">
                    @error('part_name')
                        <div class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Cavity Number --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                        Cavity Number
                    </label>
                    <input type="number" min="1" wire:model.defer="cavity_number"
                        class="w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all text-sm py-2.5 px-4 placeholder-gray-400"
                        placeholder="e.g. 1">
                    @error('cavity_number')
                        <div class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Actions --}}
                <div class="flex gap-3 pt-4">
                    <button type="button" wire:click="save"
                        class="flex-1 py-2.5 rounded-xl bg-indigo-600 text-white font-semibold text-sm shadow-md shadow-indigo-200 hover:shadow-lg hover:bg-indigo-700 transition-all transform hover:-translate-y-0.5">
                        {{ $partId ? 'Update Part' : 'Save Part' }}
                    </button>
                    @if ($partId)
                        <button type="button" wire:click="createNew"
                            class="px-4 py-2.5 rounded-xl border border-gray-200 text-gray-600 font-medium text-sm hover:bg-gray-50 transition-colors">
                            Cancel
                        </button>
                    @endif
                </div>
            </div>
        </div>
        @endcan

        {{-- ─── Table Section ─── --}}
        <div class="{{ auth()->user()->can('view_admin_panel') ? 'lg:col-span-2' : 'lg:col-span-3' }}">
            <div class="bg-white/80 backdrop-blur-xl shadow-xl rounded-3xl border border-white/50 overflow-hidden">

                {{-- Filters Bar --}}
                <div class="p-4 border-b border-gray-100 flex flex-col sm:flex-row items-stretch sm:items-center gap-3 bg-gray-50/30">
                    {{-- Search --}}
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text" wire:model.live.debounce.400ms="search"
                            class="w-full rounded-xl border-gray-200 bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm py-2 pl-9"
                            placeholder="Search part number or name…">
                    </div>

                    {{-- Mould filter --}}
                    <div class="relative">
                        <select wire:model="mouldFilter"
                            class="rounded-xl border-gray-200 bg-white text-sm py-2 pl-4 pr-8 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 appearance-none">
                            <option value="">All Moulds</option>
                            @foreach ($moulds as $m)
                                <option value="{{ $m->id }}">{{ $m->code }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-gray-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>

                    {{-- Per page --}}
                    <select wire:model="perPage"
                        class="rounded-xl border-gray-200 bg-white text-sm py-2 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                        <option value="15">15 per page</option>
                        <option value="25">25 per page</option>
                        <option value="50">50 per page</option>
                    </select>
                </div>

                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100">
                                <th class="py-3 px-5 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Part Number</th>
                                <th class="py-3 px-5 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Part Name</th>
                                <th class="py-3 px-5 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Mould</th>
                                <th class="py-3 px-5 text-center font-semibold text-gray-600 uppercase tracking-wider text-xs">Cavity</th>
                                @can('view_admin_panel')
                                    <th class="py-3 px-5 text-right font-semibold text-gray-600 uppercase tracking-wider text-xs">Actions</th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse ($parts as $part)
                                <tr class="hover:bg-indigo-50/30 transition-colors group">

                                    {{-- Part Number --}}
                                    <td class="py-3.5 px-5">
                                        <span class="font-bold text-gray-900 group-hover:text-indigo-700 transition-colors tracking-wide font-mono text-[13px]">
                                            {{ $part->part_number }}
                                        </span>
                                    </td>

                                    {{-- Part Name --}}
                                    <td class="py-3.5 px-5">
                                        <span class="text-gray-700">{{ $part->part_name }}</span>
                                    </td>

                                    {{-- Mould --}}
                                    <td class="py-3.5 px-5">
                                        @if ($part->mould)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 text-xs font-semibold">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>
                                                </svg>
                                                {{ $part->mould->code }}
                                            </span>
                                            <div class="text-[11px] text-gray-400 mt-0.5 pl-0.5">{{ $part->mould->name }}</div>
                                        @else
                                            <span class="text-gray-400 text-xs italic">—</span>
                                        @endif
                                    </td>

                                    {{-- Cavity --}}
                                    <td class="py-3.5 px-5 text-center">
                                        @if ($part->cavity_number)
                                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-slate-100 text-slate-600 text-xs font-bold">
                                                {{ $part->cavity_number }}
                                            </span>
                                        @else
                                            <span class="text-gray-300">—</span>
                                        @endif
                                    </td>

                                    {{-- Actions --}}
                                    @can('view_admin_panel')
                                        <td class="py-3.5 px-5 text-right">
                                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <button type="button" wire:click="edit('{{ $part->id }}')"
                                                    class="p-1.5 rounded-lg hover:bg-indigo-50 text-indigo-600 transition-colors" title="Edit">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                              d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                                    </svg>
                                                </button>
                                                <button type="button"
                                                    wire:click="delete('{{ $part->id }}')"
                                                    onclick="return confirm('Delete this part? This cannot be undone.')"
                                                    class="p-1.5 rounded-lg hover:bg-red-50 text-red-500 transition-colors" title="Delete">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    @endcan
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ auth()->user()->can('view_admin_panel') ? 5 : 4 }}"
                                        class="py-16 text-center text-gray-400">
                                        <div class="flex flex-col items-center gap-3">
                                            <svg class="w-12 h-12 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                            <p class="text-sm font-medium text-gray-400">No parts found</p>
                                            <p class="text-xs text-gray-300">
                                                {{ $search || $mouldFilter ? 'Try adjusting your filters.' : 'Add your first part using the form.' }}
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($parts->hasPages())
                    <div class="p-4 border-t border-gray-100 bg-gray-50/30">
                        {{ $parts->links() }}
                    </div>
                @endif
            </div>

            {{-- Stats strip --}}
            <p class="mt-3 text-xs text-gray-400 text-right px-1">
                Showing {{ $parts->firstItem() ?? 0 }}–{{ $parts->lastItem() ?? 0 }}
                of {{ $parts->total() }} part(s)
            </p>
        </div>

    </div>
</div>
