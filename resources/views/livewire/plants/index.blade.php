<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-xl font-semibold">Master Plant</h1>
        <button type="button" wire:click="createNew" class="px-3 py-2 rounded bg-gray-900 text-white text-sm">+ New</button>
    </div>

    @if (session('success'))
        <div class="mb-4 p-3 rounded bg-green-50 text-green-800 text-sm">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white shadow-sm rounded p-4">
            <h2 class="font-semibold mb-3">{{ $plantId ? 'Edit Plant' : 'Create Plant' }}</h2>

            <label class="text-sm">Name *</label>
            <input type="text" wire:model.defer="name" class="mt-1 w-full rounded border-gray-300" placeholder="e.g. Plant Jakarta">
            @error('name') <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror

            <div class="flex gap-2 pt-3">
                <button type="button" wire:click="save" class="px-4 py-2 rounded bg-blue-600 text-white text-sm">
                    {{ $plantId ? 'Update' : 'Save' }}
                </button>
                @if($plantId)
                    <button type="button" wire:click="createNew" class="px-4 py-2 rounded border text-sm">Cancel</button>
                @endif
            </div>
        </div>

        <div class="lg:col-span-2 bg-white shadow-sm rounded p-4">
            <div class="flex items-center gap-3 mb-3">
                <input type="text" wire:model.debounce.400ms="search" class="w-full rounded border-gray-300" placeholder="Search plant...">
                <select wire:model="perPage" class="rounded border-gray-300">
                    <option value="10">10</option><option value="25">25</option><option value="50">50</option>
                </select>
            </div>

            <table class="min-w-full text-sm">
                <thead><tr class="text-left border-b">
                    <th class="py-2">Name</th>
                    <th class="text-right">Actions</th>
                </tr></thead>
                <tbody>
                @forelse($plants as $p)
                    <tr class="border-b">
                        <td class="py-2">{{ $p->name }}</td>
                        <td class="text-right py-2">
                            <button type="button" wire:click="edit('{{ $p->id }}')" class="text-blue-600">Edit</button>
                            <button type="button" wire:click="delete('{{ $p->id }}')"
                                onclick="return confirm('Hapus plant ini?')"
                                class="text-red-600 ml-3">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="2" class="py-6 text-center text-gray-500">No data</td></tr>
                @endforelse
                </tbody>
            </table>

            <div class="mt-3">{{ $plants->links() }}</div>
        </div>
    </div>
</div>
