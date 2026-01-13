<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-xl font-semibold">Master Mould</h1>

        <button wire:click="createNew"
            class="px-3 py-2 rounded bg-gray-900 text-white text-sm">
            + New
        </button>
    </div>

    @if (session('success'))
        <div class="mb-4 p-3 rounded bg-green-50 text-green-800 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- FORM --}}
        <div class="lg:col-span-1">
            <div class="bg-white shadow-sm rounded p-4">
                <h2 class="font-semibold mb-3">
                    {{ $mouldId ? 'Edit Mould' : 'Create Mould' }}
                </h2>

                <div class="space-y-3">
                    <div>
                        <label class="text-sm">Code *</label>
                        <input type="text" wire:model.defer="code"
                            class="mt-1 w-full rounded border-gray-300"
                            placeholder="e.g. M-0001">
                        @error('code') <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label class="text-sm">Name *</label>
                        <input type="text" wire:model.defer="name"
                            class="mt-1 w-full rounded border-gray-300"
                            placeholder="e.g. Mould Cup 250ml">
                        @error('name') <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label class="text-sm">Cavities *</label>
                        <input type="number" min="1" wire:model.defer="cavities"
                            class="mt-1 w-full rounded border-gray-300">
                        @error('cavities') <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-sm">Min Tonnage</label>
                            <input type="number" min="0" wire:model.defer="min_tonnage_t"
                                class="mt-1 w-full rounded border-gray-300">
                            @error('min_tonnage_t') <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label class="text-sm">Max Tonnage</label>
                            <input type="number" min="0" wire:model.defer="max_tonnage_t"
                                class="mt-1 w-full rounded border-gray-300">
                            @error('max_tonnage_t') <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-sm">PM Shot Interval</label>
                            <input type="number" min="0" wire:model.defer="pm_interval_shot"
                                class="mt-1 w-full rounded border-gray-300">
                            @error('pm_interval_shot') <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label class="text-sm">PM Days Interval</label>
                            <input type="number" min="0" wire:model.defer="pm_interval_days"
                                class="mt-1 w-full rounded border-gray-300">
                            @error('pm_interval_days') <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="text-sm">Customer</label>
                        <input type="text" wire:model.defer="customer"
                            class="mt-1 w-full rounded border-gray-300">
                        @error('customer') <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label class="text-sm">Resin</label>
                        <input type="text" wire:model.defer="resin"
                            class="mt-1 w-full rounded border-gray-300">
                        @error('resin') <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label class="text-sm">Commissioned At</label>
                        <input type="date" wire:model.defer="commissioned_at"
                            class="mt-1 w-full rounded border-gray-300">
                        @error('commissioned_at') <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label class="text-sm">Status</label>
                        <select wire:model.defer="status" class="mt-1 w-full rounded border-gray-300">
                            @foreach($statusOptions as $opt)
                                <option value="{{ $opt }}">{{ $opt }}</option>
                            @endforeach
                        </select>
                        @error('status') <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="flex gap-2 pt-2">
                        <button wire:click="save"
                            class="px-4 py-2 rounded bg-blue-600 text-white text-sm">
                            {{ $mouldId ? 'Update' : 'Save' }}
                        </button>

                        @if($mouldId)
                            <button type="button" wire:click="createNew"
                                class="px-4 py-2 rounded border text-sm">
                                Cancel
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- LIST --}}
        <div class="lg:col-span-2">
            <div class="bg-white shadow-sm rounded p-4">
                <div class="flex items-center gap-3 mb-3">
                    <input type="text" wire:model.debounce.400ms="search"
                        class="w-full rounded border-gray-300"
                        placeholder="Search code/name/customer...">

                    <select wire:model="perPage" class="rounded border-gray-300">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left border-b">
                                <th class="py-2">Code</th>
                                <th>Name</th>
                                <th>Cav</th>
                                <th>Status</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($moulds as $m)
                                <tr class="border-b">
                                    <td class="py-2 font-medium">{{ $m->code }}</td>
                                    <td>{{ $m->name }}</td>
                                    <td>{{ $m->cavities }}</td>
                                    <td>{{ $m->status }}</td>
                                    <td class="text-right py-2">
                                        <button wire:click="edit('{{ $m->id }}')" class="text-blue-600">Edit</button>
                                        <button
                                            wire:click="delete('{{ $m->id }}')"
                                            onclick="return confirm('Hapus mould {{ $m->code }}?')"
                                            class="text-red-600 ml-3">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-gray-500">
                                        No data
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $moulds->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
