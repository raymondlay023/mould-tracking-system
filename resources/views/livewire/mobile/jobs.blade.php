<div class="space-y-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Active Jobs</h1>
        <p class="text-sm text-gray-500">Currently running production runs</p>
    </div>

    <div class="space-y-3">
        @forelse($activeRuns as $run)
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex justify-between items-center">
                <div>
                    <div class="font-bold text-gray-900">{{ $run->mould->code ?? 'Unknown Mould' }}</div>
                    <div class="text-xs text-gray-500">{{ $run->machine->code ?? 'Unknown Machine' }} • {{ $run->operator_name ?? 'Unknown Operator' }}</div>
                </div>
                <div class="text-right">
                    <span class="inline-flex items-center px-2 py-1 rounded bg-green-100 text-green-700 text-xs font-bold">
                        RUNNING
                    </span>
                    <div class="text-xs text-gray-400 mt-1">{{ $run->start_ts?->format('H:i') ?? '--' }}</div>
                </div>
            </div>
        @empty
            <div class="text-center py-8 text-gray-400 bg-white rounded-xl border border-dashed border-gray-200">
                <p class="text-sm">No active jobs.</p>
            </div>
        @endforelse
    </div>
</div>
