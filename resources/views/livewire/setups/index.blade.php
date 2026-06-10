<div class="max-w-7xl mx-auto px-4 py-8">

    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-gray-900 to-gray-600 tracking-tight">
                Setup Events
            </h1>
            <p class="text-gray-500 mt-1 text-sm">Log tooling setups, compare actual vs. target, and track operator notes.</p>
        </div>
        <button type="button" wire:click="createNew"
            class="group flex items-center gap-2 px-5 py-2.5 rounded-full bg-gray-900 text-white text-sm font-medium shadow-lg shadow-gray-200 hover:shadow-xl hover:bg-black transition-all transform hover:-translate-y-0.5">
            <svg class="w-4 h-4 text-gray-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span>New Setup</span>
        </button>
    </div>

    {{-- Flash Message --}}
    @if (session('success'))
        <div class="mb-6 p-4 rounded-2xl bg-green-50 text-green-800 text-sm border border-green-100 flex items-center gap-2 shadow-sm"
             x-data="{ show: true }" x-show="show">
            <svg class="w-5 h-5 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span class="font-medium">{{ session('success') }}</span>
            <button @click="show = false" class="ml-auto text-green-500 hover:text-green-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- FORM PANEL --}}
        <div class="lg:col-span-1">
            <div class="bg-white/80 backdrop-blur-xl shadow-sm rounded-3xl border border-white/50 p-6 sticky top-24">

                <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <div class="w-1 h-6 bg-blue-500 rounded-full"></div>
                    {{ $setupId ? 'Edit Setup' : 'New Setup' }}
                </h2>

                <div class="space-y-4">

                    {{-- Mould --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Mould <span class="text-red-500">*</span>
                        </label>
                        <select wire:model.defer="mould_id"
                            class="w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all appearance-none text-sm">
                            <option value="">Select mould</option>
                            @foreach ($moulds as $m)
                                <option value="{{ $m->id }}">{{ $m->code }} – {{ $m->name }}</option>
                            @endforeach
                        </select>
                        @error('mould_id')
                            <div class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Machine --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Machine <span class="text-red-500">*</span>
                        </label>
                        <select wire:model.defer="machine_id"
                            class="w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all appearance-none text-sm">
                            <option value="">Select machine</option>
                            @foreach ($machines as $mc)
                                <option value="{{ $mc->id }}">{{ $mc->code }} – {{ $mc->name }}</option>
                            @endforeach
                        </select>
                        @error('machine_id')
                            <div class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Start / End --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Start <span class="text-red-500">*</span>
                            </label>
                            <input type="datetime-local" wire:model.defer="start_ts"
                                class="w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm">
                            @error('start_ts')
                                <div class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                End <span class="text-red-500">*</span>
                            </label>
                            <input type="datetime-local" wire:model.defer="end_ts"
                                class="w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm">
                            @error('end_ts')
                                <div class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Target / Actual --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Target (min)</label>
                            <input type="number" min="0" wire:model.defer="target_min" placeholder="0"
                                class="w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Actual (min)</label>
                            <input type="number" min="0" wire:model.defer="actual_min" placeholder="0"
                                class="w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm">
                        </div>
                    </div>

                    {{-- Loss Reason --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Loss Reason</label>
                        <input type="text" wire:model.defer="loss_reason" placeholder="e.g. material change, tooling delay"
                            class="w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm">
                    </div>

                    {{-- Operator --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Operator</label>
                        <input type="text" wire:model.defer="operator_name" placeholder="Operator name"
                            class="w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm">
                    </div>

                    {{-- Notes --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea wire:model.defer="notes" rows="3" placeholder="Additional remarks..."
                            class="w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm resize-none"></textarea>
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-3 pt-4 border-t border-gray-100">
                        <button type="button" wire:click="save" wire:loading.attr="disabled"
                            class="flex-1 px-4 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-medium shadow-md shadow-blue-100 hover:bg-blue-700 hover:shadow-lg transition-all disabled:opacity-60">
                            <span wire:loading.remove wire:target="save">{{ $setupId ? 'Update Setup' : 'Save Setup' }}</span>
                            <span wire:loading wire:target="save">Saving…</span>
                        </button>
                        @if ($setupId)
                            <button type="button" wire:click="createNew"
                                class="px-4 py-2.5 rounded-xl border border-gray-200 text-gray-600 text-sm font-medium hover:bg-gray-50 transition-all">
                                Cancel
                            </button>
                        @endif
                    </div>

                </div>
            </div>
        </div>

        {{-- TABLE PANEL --}}
        <div class="lg:col-span-2">
            <div class="bg-white/80 backdrop-blur-xl shadow-sm rounded-3xl border border-white/50 flex flex-col">

                {{-- Search / Per-page --}}
                <div class="p-4 border-b border-gray-100 bg-white/40 rounded-t-3xl sticky top-0 z-10 backdrop-blur-md">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="relative flex-1">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input type="text" wire:model.live.debounce.400ms="search"
                                class="pl-9 w-full rounded-xl border-gray-200 bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all placeholder-gray-400 shadow-sm text-sm"
                                placeholder="Search mould, machine, operator…">
                        </div>
                        <select wire:model="perPage"
                            class="rounded-xl border-gray-200 bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm py-2 shadow-sm">
                            <option value="10">10 rows</option>
                            <option value="25">25 rows</option>
                            <option value="50">50 rows</option>
                        </select>
                    </div>
                </div>

                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50/60 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 border-b border-gray-100">
                                <th class="px-5 py-3">Mould</th>
                                <th class="px-5 py-3">Machine</th>
                                <th class="px-5 py-3">Window</th>
                                <th class="px-5 py-3">Actual / Target</th>
                                <th class="px-5 py-3">Operator</th>
                                <th class="px-5 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($setups as $s)
                                @php $tz = auth()->user()?->timezone ?? 'UTC'; @endphp
                                <tr class="group hover:bg-blue-50/30 transition-colors">
                                    <td class="px-5 py-4">
                                        <div class="font-semibold text-gray-900">{{ $s->mould?->code ?? '–' }}</div>
                                        <div class="text-xs text-gray-400 mt-0.5">{{ $s->mould?->name ?? '' }}</div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="font-medium text-gray-800">{{ $s->machine?->code ?? '–' }}</div>
                                        <div class="text-xs text-gray-400 mt-0.5">{{ $s->machine?->name ?? '' }}</div>
                                    </td>
                                    <td class="px-5 py-4 text-gray-700">
                                        <div class="font-medium">{{ $s->start_ts?->setTimezone($tz)->format('d M Y, H:i') ?? '–' }}</div>
                                        <div class="text-xs text-gray-400 mt-0.5">
                                            to {{ $s->end_ts?->setTimezone($tz)->format('H:i') ?? '–' }}
                                            @if($s->start_ts && $s->end_ts)
                                                <span class="ml-1 text-gray-300">·</span>
                                                <span class="ml-1">{{ $s->end_ts->diffInMinutes($s->start_ts) }} min</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        @php
                                            $actual = $s->actual_min;
                                            $target = $s->target_min;
                                            $over = $actual && $target && $actual > $target;
                                        @endphp
                                        <span class="font-semibold {{ $over ? 'text-red-600' : 'text-gray-800' }}">
                                            {{ $actual ?? '–' }}
                                        </span>
                                        <span class="text-gray-400"> / {{ $target ?? '–' }} min</span>
                                        @if($over)
                                            <div class="text-xs text-red-400 mt-0.5">+{{ $actual - $target }} min over</div>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-gray-700">
                                        {{ $s->operator_name ?: '–' }}
                                        @if($s->loss_reason)
                                            <div class="text-xs text-amber-500 mt-0.5 truncate max-w-[120px]" title="{{ $s->loss_reason }}">
                                                ⚠ {{ $s->loss_reason }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2 opacity-60 group-hover:opacity-100 transition-opacity">
                                            <button type="button" wire:click="edit('{{ $s->id }}')"
                                                class="p-1.5 rounded-lg text-blue-600 hover:bg-blue-50 transition-colors text-xs font-medium">
                                                Edit
                                            </button>
                                            <button type="button" wire:click="delete('{{ $s->id }}')"
                                                onclick="return confirm('Delete this setup?')"
                                                class="p-1.5 rounded-lg text-red-500 hover:bg-red-50 transition-colors text-xs font-medium">
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">
                                        <div class="flex flex-col items-center justify-center py-16 text-center text-gray-400">
                                            <div class="bg-gray-50 rounded-full p-6 mb-4">
                                                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                                </svg>
                                            </div>
                                            <p class="text-sm font-medium text-gray-500">No setup events found</p>
                                            <p class="text-xs mt-1">Try adjusting your search or add a new setup.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="p-4 border-t border-gray-100 bg-white/40 rounded-b-3xl">
                    {{ $setups->links() }}
                </div>

            </div>
        </div>

    </div>
</div>
