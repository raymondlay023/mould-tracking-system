<div class="max-w-4xl mx-auto px-4 py-6">
    <h1 class="text-xl font-semibold mb-4">Import Mould (Excel/CSV)</h1>

    @if (session('success'))
        <div class="mb-4 p-3 rounded bg-green-50 text-green-800 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow-sm rounded p-4 mb-6">
        <div class="text-sm text-gray-600 mb-3">
            Format kolom wajib: <b>code, name, cavities</b>. Kolom opsional: customer, resin, min_tonnage_t, max_tonnage_t, pm_interval_shot, pm_interval_days, commissioned_at, status.
        </div>

        <div class="flex items-center gap-3">
            <input type="file" wire:model="file" class="text-sm">
            <label class="text-sm flex items-center gap-2">
                <input type="checkbox" wire:model="upsert">
                Update jika code sudah ada (upsert)
            </label>
            <button wire:click="import" class="px-4 py-2 rounded bg-blue-600 text-white text-sm">
                Import
            </button>
        </div>
        @error('file') <div class="text-red-600 text-xs mt-2">{{ $message }}</div> @enderror
        <div wire:loading class="text-sm text-gray-600 mt-2">Processing...</div>
    </div>

    @if($result)
        <div class="bg-white shadow-sm rounded p-4">
            <div class="grid grid-cols-3 gap-3 text-sm">
                <div class="p-3 rounded bg-gray-50">Inserted: <b>{{ $result['inserted'] }}</b></div>
                <div class="p-3 rounded bg-gray-50">Updated: <b>{{ $result['updated'] }}</b></div>
                <div class="p-3 rounded bg-gray-50">Failed: <b class="text-red-600">{{ $result['failed'] }}</b></div>
            </div>

            @if($result['failed'] > 0)
                <h2 class="font-semibold mt-5 mb-2">Error Detail</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left border-b">
                                <th class="py-2">Row</th>
                                <th>Code</th>
                                <th>Errors</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($result['errors'] as $e)
                                <tr class="border-b">
                                    <td class="py-2">{{ $e['row'] }}</td>
                                    <td>{{ $e['code'] }}</td>
                                    <td>
                                        <ul class="list-disc pl-5">
                                            @foreach($e['errors'] as $msg)
                                                <li class="text-red-700">{{ $msg }}</li>
                                            @endforeach
                                        </ul>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif
</div>
