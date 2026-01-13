<div class="max-w-7xl mx-auto px-4 py-6">
    <h1 class="text-xl font-semibold mb-4">Audit Log</h1>

    <div class="bg-white shadow-sm rounded p-4 mb-4 flex items-center gap-3">
        <input type="text" wire:model.debounce.400ms="search" class="w-full rounded border-gray-300"
            placeholder="Search description/log/subject...">
        <select wire:model="perPage" class="rounded border-gray-300">
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
        </select>
    </div>

    <div class="bg-white shadow-sm rounded p-4 overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left border-b">
                    <th class="py-2">Time</th>
                    <th>Action</th>
                    <th>By</th>
                    <th>Subject</th>
                    <th>IP</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($logs as $l)
                    <tr class="border-b">
                        <td class="py-2">{{ $l->created_at }}</td>
                        <td>{{ $l->description }}</td>
                        <td>{{ optional($l->causer)->name ?? '-' }}</td>
                        <td>
                            {{ class_basename($l->subject_type) }}
                            @if ($l->subject_id)
                                #{{ $l->subject_id }}
                            @endif
                        </td>
                        <td>{{ $l->properties['ip'] ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-3">
            {{ $logs->links() }}
        </div>
    </div>
</div>
