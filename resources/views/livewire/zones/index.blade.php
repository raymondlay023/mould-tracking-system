<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-xl font-semibold">Master Zone / Line</h1>
        <button type="button" wire:click="createNew" class="px-3 py-2 rounded bg-gray-900 text-white text-sm">+ New</button>
    </div>

    @if (session('success'))
        <div class="mb-4 p-3 rounded bg-green-50 text-green-800 text-sm">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white shadow-sm rounded p-4">
            <h2 class="font-semibold mb-3">{{ $zoneId ? 'Edit Zone' : 'Create Zone' }}</h2>

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
                    <label class="text-sm">Code *</label>
                    <input type="text" wire:model.defer="code" class="mt-1 w-full rounded border-gray-300" placeholder="e.g. Z1">
                    @error('code') <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="text-sm">Name *</label>
                    <input type="text" wire:model.defer="name" class="mt-1 w-full rounded border-gray-300" placeholder="e.g. Zone Injection A">
                    @error('name') <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="flex gap-2 pt-2">
                    <button type="button" wire:click="save" class="px-4 py-2 rounded bg-blue-600 text-white text-sm">
                        {{ $zoneId ? 'Update' : 'Save' }}
                    </button>
                    @if($zoneId)
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
                    <th class="py-2">Plant</th>
                    <th>Code</th>
                    <th>Name</th>
                    <th class="text-right">Actions</th>
                </tr></thead>
                <tbody>
                @forelse($zones as $z)
                    <tr class="border-b">
                        <td class="py-2">{{ $z->plant?->name }}</td>
                        <td class="font-medium">{{ $z->code }}</td>
                        <td>{{ $z->name }}</td>
                        <td class="text-right py-2">
                            <button type="button" wire:click="edit('{{ $z->id }}')" class="text-blue-600">Edit</button>
                            <button type="button" wire:click="delete('{{ $z->id }}')" onclick="return confirm('Hapus zone ini?')"
                                class="text-red-600 ml-3">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="py-6 text-center text-gray-500">No data</td></tr>
                @endforelse
                </tbody>
            </table>

            <div class="mt-3">{{ $zones->links() }}</div>
        </div>
    </div>
</div>
