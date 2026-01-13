<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-xl font-semibold">Master Machine</h1>
        <button type="button" wire:click="createNew" class="px-3 py-2 rounded bg-gray-900 text-white text-sm">+ New</button>
    </div>

    @if (session('success'))
        <div class="mb-4 p-3 rounded bg-green-50 text-green-800 text-sm">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white shadow-sm rounded p-4">
            <h2 class="font-semibold mb-3">{{ $machineId ? 'Edit Machine' : 'Create Machine' }}</h2>

            <div class="space-y-3">
                <div>
                    <label class="text-sm">Plant *</label>
                    <select wire:model.defer="plant_id" class="mt-1 w-full rounded border-gray-300">
                        <option value="">-- pilih plant --</option>
                        @foreach($plants as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                    @error('plant_id') <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="text-sm">Zone/Line (optional)</label>
                    <select wire:model.defer="zone_id" class="mt-1 w-full rounded border-gray-300">
                        <option value="">-- none --</option>
                        @foreach($zones as $z)
                            <option value="{{ $z->id }}">{{ $z->code }} - {{ $z->name }}</option>
                        @endforeach
                    </select>
                    @error('zone_id') <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="text-sm">Code *</label>
                    <input type="text" wire:model.defer="code" class="mt-1 w-full rounded border-gray-300" placeholder="e.g. MC-001">
                    @error('code') <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="text-sm">Name *</label>
                    <input type="text" wire:model.defer="name" class="mt-1 w-full rounded border-gray-300" placeholder="e.g. Injection 180T">
                    @error('name') <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-sm">Tonnage (T)</label>
                        <input type="number" min="0" wire:model.defer="tonnage_t" class="mt-1 w-full rounded border-gray-300">
                        @error('tonnage_t') <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="pt-6">
                        <label class="text-sm flex items-center gap-2">
                            <input type="checkbox" wire:model.defer="plc_connected">
                            PLC Connected
                        </label>
                        @error('plc_connected') <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="flex gap-2 pt-2">
                    <button type="button" wire:click="save" class="px-4 py-2 rounded bg-blue-600 text-white text-sm">
                        {{ $machineId ? 'Update' : 'Save' }}
                    </button>
                    @if($machineId)
                        <button type="button" wire:click="createNew" class="px-4 py-2 rounded border text-sm">Cancel</button>
                    @endif
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 bg-white shadow-sm rounded p-4">
            <div class="flex items-center gap-3 mb-3">
                <input type="text" wire:model.debounce.400ms="search" class="w-full rounded border-gray-300" placeholder="Search code/name...">
                <select wire:model="perPage" class="rounded border-gray-300">
                    <option value="10">10</option><option value="25">25</option><option value="50">50</option>
                </select>
            </div>

            <table class="min-w-full text-sm">
                <thead><tr class="text-left border-b">
                    <th class="py-2">Code</th>
                    <th>Name</th>
                    <th>Plant</th>
                    <th>Zone</th>
                    <th class="text-right">Actions</th>
                </tr></thead>
                <tbody>
                @forelse($machines as $m)
                    <tr class="border-b">
                        <td class="py-2 font-medium">{{ $m->code }}</td>
                        <td>{{ $m->name }}</td>
                        <td>{{ $m->plant?->name }}</td>
                        <td>{{ $m->zone?->code }}</td>
                        <td class="text-right py-2">
                            <button type="button" wire:click="edit('{{ $m->id }}')" class="text-blue-600">Edit</button>
                            <button type="button" wire:click="delete('{{ $m->id }}')" onclick="return confirm('Hapus machine ini?')"
                                class="text-red-600 ml-3">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-6 text-center text-gray-500">No data</td></tr>
                @endforelse
                </tbody>
            </table>

            <div class="mt-3">{{ $machines->links() }}</div>
        </div>
    </div>
</div>
