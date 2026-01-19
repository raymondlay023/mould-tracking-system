<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Quality Assurance</h1>
            <p class="text-gray-500">Trial Validations and Defect Analysis</p>
        </div>
        <div class="space-x-2">
            <a href="{{ route('trials.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">Manage Trials</a>
             <a href="{{ route('moulds.index') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium">Mould Database</a>
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <div class="text-sm font-medium text-gray-500">Trials Pending</div>
                <div class="text-3xl font-bold text-amber-600 mt-2">{{ $pendingTrials->count() }}</div>
            </div>
            <div class="h-10 w-10 bg-amber-50 rounded-full flex items-center justify-center text-amber-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Pending Trials List --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
             <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/50 flex justify-between items-center">
                <h3 class="font-semibold text-gray-900">Trials Awaiting Approval</h3>
            </div>
            <div class="divide-y divide-gray-100 max-h-[400px] overflow-y-auto">
                @forelse($pendingTrials as $t)
                    <div class="p-4 flex items-center justify-between hover:bg-gray-50">
                        <div>
                             <div class="flex items-center gap-2 mb-1">
                                <span class="font-bold text-gray-900">{{ $t->mould->code }}</span>
                                <span class="text-xs px-2 py-0.5 rounded bg-gray-100 text-gray-500">{{ $t->machine->code }}</span>
                             </div>
                             <div class="text-xs text-gray-500">{{ Str::limit($t->purpose, 50) }}</div>
                        </div>
                         <div class="text-right flex items-center gap-3">
                             <div class="text-xs text-gray-400 mr-2">{{ $t->start_ts->diffForHumans() }}</div>
                             <a href="{{ route('trials.index', ['search' => $t->mould->code]) }}" class="px-3 py-1 bg-blue-50 text-blue-600 text-xs font-medium rounded-lg hover:bg-blue-100">
                                Review
                             </a>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center text-gray-400">
                        <div class="mx-auto w-12 h-12 bg-green-50 rounded-full flex items-center justify-center mb-3">
                            <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                         <p>No pending trials. Good job!</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Top High Defect Moulds --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
             <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/50">
                <h3 class="font-semibold text-gray-900">Highest Defect Rates (30d)</h3>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($topNg as $t)
                    @php
                        $pt = (int) $t->ok_sum + (int) $t->ng_sum;
                        $rate = $pt > 0 ? round(((int) $t->ng_sum / $pt) * 100, 1) : 0;
                        $color = $rate > 5 ? 'red' : ($rate > 2 ? 'amber' : 'blue');
                    @endphp
                    <div class="p-4 hover:bg-gray-50">
                        <div class="flex justify-between items-center mb-1">
                            <div>
                                <div class="font-medium text-sm text-gray-900">{{ $t->mould_code }}</div>
                                <div class="text-xs text-gray-500">{{ $t->mould_name }}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-sm text-{{$color}}-600">{{ $rate }}%</div>
                                <div class="text-xs text-gray-400">{{ number_format($t->ng_sum) }} NG</div>
                            </div>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-1.5 mt-2">
                            <div class="bg-{{$color}}-500 h-1.5 rounded-full" style="width: {{ min($rate * 5, 100) }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-400 text-sm">No quality data available.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
