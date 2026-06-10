<div class="max-w-7xl mx-auto px-4 py-8">

    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-gray-900 to-gray-600 tracking-tight">
                Trial Events
            </h1>
            <p class="text-gray-500 mt-1 text-sm">Track trial runs, capture parameters, and manage GO / NO-GO approvals.</p>
        </div>
        <button type="button" wire:click="createNew"
            class="group flex items-center gap-2 px-5 py-2.5 rounded-full bg-gray-900 text-white text-sm font-medium shadow-lg shadow-gray-200 hover:shadow-xl hover:bg-black transition-all transform hover:-translate-y-0.5">
            <svg class="w-4 h-4 text-gray-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span>New Trial</span>
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
                    <div class="w-1 h-6 bg-violet-500 rounded-full"></div>
                    {{ $trialId ? 'Edit Trial' : 'New Trial' }}
                </h2>

                <div class="space-y-4">

                    {{-- Mould --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Mould <span class="text-red-500">*</span>
                        </label>
                        <select wire:model.defer="mould_id"
                            class="w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 transition-all appearance-none text-sm">
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
                            class="w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 transition-all appearance-none text-sm">
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
                                class="w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 transition-all text-sm">
                            @error('start_ts')
                                <div class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                End <span class="text-red-500">*</span>
                            </label>
                            <input type="datetime-local" wire:model.defer="end_ts"
                                class="w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 transition-all text-sm">
                            @error('end_ts')
                                <div class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Purpose --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Purpose</label>
                        <input type="text" wire:model.defer="purpose"
                            placeholder="e.g. New material, parameter tuning"
                            class="w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 transition-all text-sm">
                    </div>

                    {{-- Parameters --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Parameters</label>
                        <textarea wire:model.defer="parameters" rows="3"
                            placeholder="Temp, pressure, speed, cooling, etc."
                            class="w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 transition-all text-sm resize-none"></textarea>
                    </div>

                    {{-- Notes --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea wire:model.defer="notes" rows="2"
                            placeholder="Additional remarks…"
                            class="w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 transition-all text-sm resize-none"></textarea>
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-3 pt-4 border-t border-gray-100">
                        <button type="button" wire:click="save" wire:loading.attr="disabled"
                            class="flex-1 px-4 py-2.5 rounded-xl bg-violet-600 text-white text-sm font-medium shadow-md shadow-violet-100 hover:bg-violet-700 hover:shadow-lg transition-all disabled:opacity-60">
                            <span wire:loading.remove wire:target="save">{{ $trialId ? 'Update Trial' : 'Save Trial' }}</span>
                            <span wire:loading wire:target="save">Saving…</span>
                        </button>
                        @if ($trialId)
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
                                class="pl-9 w-full rounded-xl border-gray-200 bg-white focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 transition-all placeholder-gray-400 shadow-sm text-sm"
                                placeholder="Search mould, machine…">
                        </div>
                        <select wire:model="perPage"
                            class="rounded-xl border-gray-200 bg-white focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 text-sm py-2 shadow-sm">
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
                                <th class="px-5 py-3">Purpose</th>
                                <th class="px-5 py-3">Approval</th>
                                <th class="px-5 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($trials as $t)
                                @php $tz = auth()->user()?->timezone ?? 'UTC'; @endphp
                                <tr class="group hover:bg-violet-50/30 transition-colors">
                                    <td class="px-5 py-4">
                                        <div class="font-semibold text-gray-900">{{ $t->mould?->code ?? '–' }}</div>
                                        <div class="text-xs text-gray-400 mt-0.5">{{ $t->mould?->name ?? '' }}</div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="font-medium text-gray-800">{{ $t->machine?->code ?? '–' }}</div>
                                        <div class="text-xs text-gray-400 mt-0.5">{{ $t->machine?->name ?? '' }}</div>
                                    </td>
                                    <td class="px-5 py-4 text-gray-700">
                                        <div class="font-medium">{{ $t->start_ts?->setTimezone($tz)->format('d M Y, H:i') ?? '–' }}</div>
                                        <div class="text-xs text-gray-400 mt-0.5">
                                            to {{ $t->end_ts?->setTimezone($tz)->format('H:i') ?? '–' }}
                                            @if($t->start_ts && $t->end_ts)
                                                <span class="ml-1 text-gray-300">·</span>
                                                <span class="ml-1">{{ $t->end_ts->diffInMinutes($t->start_ts) }} min</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-gray-700 max-w-[160px]">
                                        <div class="truncate" title="{{ $t->purpose }}">{{ $t->purpose ?: '–' }}</div>
                                    </td>
                                    <td class="px-5 py-4">
                                        @if (!$t->approved)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-100">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                                                Pending
                                            </span>
                                        @elseif ($t->approved_go)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-700 border border-green-100">
                                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                                GO
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-100">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                                NO-GO
                                            </span>
                                        @endif
                                        @if ($t->approved)
                                            <div class="text-xs text-gray-400 mt-1">
                                                {{ $t->approved_by }}
                                                <span class="text-gray-300">·</span>
                                                {{ $t->approved_at?->setTimezone($tz)->format('d M Y') }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                        <div class="flex items-center justify-end gap-1 opacity-60 group-hover:opacity-100 transition-opacity">
                                            <button type="button" wire:click="edit('{{ $t->id }}')"
                                                class="p-1.5 rounded-lg text-blue-600 hover:bg-blue-50 transition-colors text-xs font-medium">
                                                Edit
                                            </button>
                                            @if (!$t->approved)
                                                <button type="button" wire:click="approveGo('{{ $t->id }}')"
                                                    class="p-1.5 rounded-lg text-green-700 hover:bg-green-50 transition-colors text-xs font-medium">
                                                    GO
                                                </button>
                                                <button type="button" wire:click="approveNoGo('{{ $t->id }}')"
                                                    class="p-1.5 rounded-lg text-red-600 hover:bg-red-50 transition-colors text-xs font-medium">
                                                    NO-GO
                                                </button>
                                            @endif
                                            <button type="button" wire:click="delete('{{ $t->id }}')"
                                                onclick="return confirm('Delete this trial?')"
                                                class="p-1.5 rounded-lg text-red-500 hover:bg-red-50 transition-colors text-xs font-medium">
                                                Del
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
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                                                </svg>
                                            </div>
                                            <p class="text-sm font-medium text-gray-500">No trial events found</p>
                                            <p class="text-xs mt-1">Try adjusting your search or add a new trial.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="p-4 border-t border-gray-100 bg-white/40 rounded-b-3xl">
                    {{ $trials->links() }}
                </div>

            </div>
        </div>

    </div>
</div>
