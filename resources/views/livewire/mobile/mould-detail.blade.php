<div class="space-y-6">
    {{-- Header --}}
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <div class="flex justify-between items-start">
            <div>
                 <span class="inline-flex items-center px-2 py-1 rounded text-xs font-bold bg-blue-100 text-blue-700 mb-2">
                    {{ $mould->status }}
                </span>
                <h1 class="text-2xl font-bold text-gray-900">{{ $mould->code }}</h1>
                <p class="text-sm text-gray-500">{{ $mould->name }}</p>
            </div>
            <div class="text-right">
                <div class="text-xs text-gray-400">Location</div>
                <div class="font-medium text-gray-700">{{ $mould->location ?? 'N/A' }}</div>
            </div>
        </div>
    </div>

    {{-- Actions --}}
    <div class="space-y-3">
        @if($activeRun)
            <div class="bg-green-50 border border-green-200 p-4 rounded-xl">
                <h3 class="font-bold text-green-800 mb-1">Currently Running</h3>
                <p class="text-sm text-green-700 mb-3">
                    Machine: <strong>{{ $activeRun->machine->code }}</strong><br>
                    Started: {{ $activeRun->start_ts->format('H:i') }}
                </p>
                <a href="#" class="block w-full text-center bg-green-600 text-white py-3 rounded-lg font-bold shadow-sm">
                    View Run
                </a>
            </div>
        @else
             {{-- Start Run Button --}}
             <button class="w-full bg-blue-600 text-white py-4 rounded-xl font-bold shadow-md flex items-center justify-center gap-2 active:scale-95 transition-transform">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Start Production Run
            </button>
        @endif

        {{-- Move Button --}}
        <button class="w-full bg-white border border-gray-200 text-gray-700 py-4 rounded-xl font-bold shadow-sm flex items-center justify-center gap-2 active:scale-95 transition-transform">
             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
            Update Location
        </button>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 gap-4">
        <div class="bg-white p-3 rounded-lg border border-gray-100 text-center">
            <div class="text-xs text-gray-400">Total Shots</div>
            <div class="font-mono font-bold text-lg">{{ number_format($mould->total_shots) }}</div>
        </div>
        <div class="bg-white p-3 rounded-lg border border-gray-100 text-center">
            <div class="text-xs text-gray-400">Next PM</div>
            <div class="font-mono font-bold text-lg text-{{ $mould->next_pm_due ? 'red' : 'gray' }}-600">
                {{ number_format($mould->pm_interval_shot - ($mould->total_shots - $mould->last_pm_at_shot)) }}
            </div>
        </div>
    </div>
</div>
