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
                    <th>Changes</th>
                    <th>IP</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($logs as $l)
                    <tr class="border-b">
                        <td class="py-2 text-gray-500">{{ $l->created_at->timezone(auth()->user()->timezone ?? 'Asia/Jakarta')->format('Y-m-d H:i') }}</td>
                        <td class="font-medium">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                {{ strtoupper($l->event) }}
                            </span>
                        </td>
                        <td>{{ optional($l->causer)->name ?? 'System' }}</td>
                        <td>
                            <span class="text-xs text-gray-400 block">{{ class_basename($l->subject_type) }}</span>
                            {{ $l->subject?->code ?? $l->subject?->name ?? $l->subject_id }}
                        </td>
                        <td class="text-xs font-mono w-1/3">
                            {{-- Changes --}}
                            @if($l->event === 'updated' && isset($l->properties['old']) && isset($l->properties['attributes']))
                                <div class="space-y-1">
                                    @foreach($l->properties['attributes'] as $key => $val)
                                        @if(isset($l->properties['old'][$key]) && $l->properties['old'][$key] != $val)
                                            <div class="flex gap-2 text-xs">
                                                <span class="text-gray-500 w-24 text-right">{{ $key }}:</span>
                                                <span class="text-red-500 line-through">{{ $l->properties['old'][$key] }}</span>
                                                <span class="text-green-600">-> {{ $val }}</span>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @elseif($l->event === 'created')
                                <span class="text-green-600">New Record Created</span>
                            @elseif($l->event === 'deleted')
                                <span class="text-red-600">Record Deleted</span>
                            @else
                                {{ json_encode($l->properties['attributes'] ?? $l->properties) }}
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
