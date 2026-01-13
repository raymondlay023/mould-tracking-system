<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-xl font-semibold">Setup Events</h1>
        <button type="button" wire:click="createNew" class="px-3 py-2 rounded bg-gray-900 text-white text-sm">+
            New</button>
    </div>

    @if (session('success'))
        <div class="mb-4 p-3 rounded bg-green-50 text-green-800 text-sm">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white shadow-sm rounded p-4">
            <h2 class="font-semibold mb-3">{{ $setupId ? 'Edit Setup' : 'Create Setup' }}</h2>

            <div class="space-y-3">
                <div>
                    <label class="text-sm">Mould *</label>
                    <select wire:model.defer="mould_id" class="mt-1 w-full rounded border-gray-300">
                        <option value="">-- pilih mould --</option>
                        @foreach ($moulds as $m)
                            <option value="{{ $m->id }}">{{ $m->code }} - {{ $m->name }}</option>
                        @endforeach
                    </select>
                    @error('mould_id')
                        <div class="text-red-600 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="text-sm">Machine *</label>
                    <select wire:model.defer="machine_id" class="mt-1 w-full rounded border-gray-300">
                        <option value="">-- pilih machine --</option>
                        @foreach ($machines as $mc)
                            <option value="{{ $mc->id }}">{{ $mc->code }} - {{ $mc->name }}</option>
                        @endforeach
                    </select>
                    @error('machine_id')
                        <div class="text-red-600 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-sm">Start *</label>
                        <input type="datetime-local" wire:model.defer="start_ts"
                            class="mt-1 w-full rounded border-gray-300">
                        @error('start_ts')
                            <div class="text-red-600 text-xs mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label class="text-sm">End *</label>
                        <input type="datetime-local" wire:model.defer="end_ts"
                            class="mt-1 w-full rounded border-gray-300">
                        @error('end_ts')
                            <div class="text-red-600 text-xs mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-sm">Target (min)</label>
                        <input type="number" min="0" wire:model.defer="target_min"
                            class="mt-1 w-full rounded border-gray-300">
                    </div>
                    <div>
                        <label class="text-sm">Actual (min)</label>
                        <input type="number" min="0" wire:model.defer="actual_min"
                            class="mt-1 w-full rounded border-gray-300">
                    </div>
                </div>

                <div>
                    <label class="text-sm">Loss Reason</label>
                    <input type="text" wire:model.defer="loss_reason" class="mt-1 w-full rounded border-gray-300">
                </div>

                <div>
                    <label class="text-sm">Operator</label>
                    <input type="text" wire:model.defer="operator_name" class="mt-1 w-full rounded border-gray-300">
                </div>

                <div>
                    <label class="text-sm">Notes</label>
                    <textarea wire:model.defer="notes" class="mt-1 w-full rounded border-gray-300" rows="2"></textarea>
                </div>

                <div class="flex gap-2 pt-2">
                    <button type="button" wire:click="save" class="px-4 py-2 rounded bg-blue-600 text-white text-sm">
                        {{ $setupId ? 'Update' : 'Save' }}
                    </button>
                    @if ($setupId)
                        <button type="button" wire:click="createNew"
                            class="px-4 py-2 rounded border text-sm">Cancel</button>
                    @endif
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 bg-white shadow-sm rounded p-4">
            <div class="flex items-center gap-3 mb-3">
                <input type="text" wire:model.debounce.400ms="search" class="w-full rounded border-gray-300"
                    placeholder="Search mould/machine...">
                <select wire:model="perPage" class="rounded border-gray-300">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>

            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left border-b">
                        <th class="py-2">Mould</th>
                        <th>Machine</th>
                        <th>End</th>
                        <th>Actual</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($setups as $s)
                        <tr class="border-b">
                            <td class="py-2">{{ $s->mould?->code }}</td>
                            <td>{{ $s->machine?->code }}</td>
                            <td>{{ $s->end_ts }}</td>
                            <td>{{ $s->actual_min ?? '-' }} min</td>
                            <td class="text-right py-2">
                                <button type="button" wire:click="edit('{{ $s->id }}')"
                                    class="text-blue-600">Edit</button>
                                <button type="button" wire:click="delete('{{ $s->id }}')"
                                    onclick="return confirm('Hapus setup ini?')"
                                    class="text-red-600 ml-3">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-gray-500">No data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">{{ $setups->links() }}</div>
        </div>
    </div>
</div>
