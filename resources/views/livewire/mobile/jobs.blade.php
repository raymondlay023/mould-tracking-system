<div class="space-y-6">
    <div>
        <h1 class="text-xl font-bold text-slate-900">Active Jobs</h1>
        <p class="text-sm text-slate-500">Currently running production runs</p>
    </div>

    <div class="space-y-3">
        @forelse($activeRuns as $run)
            <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-100 flex justify-between items-center">
                <div>
                    <div class="font-bold text-slate-900">{{ $run->mould->code ?? 'Unknown Mould' }}</div>
                    <div class="text-xs text-slate-500">{{ $run->machine->code ?? 'Unknown Machine' }} • {{ $run->operator_name ?? 'Unknown Operator' }}</div>
                </div>
                <div class="text-right">
                    <span class="inline-flex items-center px-2 py-1 rounded bg-green-100 text-green-700 text-xs font-bold">
                        RUNNING
                    </span>
                    <div class="text-xs text-slate-400 mt-1">{{ $run->start_ts?->format('H:i') ?? '--' }}</div>
                </div>
            </div>
        @empty
            <div class="text-center py-8 text-slate-400 bg-white rounded-xl border border-dashed border-slate-200">
                <p class="text-sm">No active jobs.</p>
            </div>
        @endforelse
    </div>
</div>
