<div>
    <!-- Top Nav -->
    <div class="bg-white/80 backdrop-blur-md border-b border-slate-150 px-4 py-3 flex items-center justify-between sticky top-0 z-40 shadow-sm">
        <h1 class="font-bold text-lg text-slate-800">Maintenance Tasks</h1>
    </div>

    <!-- Content -->
    <div class="p-4 space-y-4 pb-24">
        @if (session('success'))
            <div class="p-3 bg-green-50 border border-green-100 text-green-700 text-sm rounded-xl font-medium">
                {{ session('success') }}
            </div>
        @endif

        @forelse($tasks as $task)
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden relative">
                @if($task->status === 'IN_PROGRESS')
                    <div class="absolute top-0 left-0 w-1 h-full bg-amber-500"></div>
                @elseif($task->status === 'APPROVED')
                    <div class="absolute top-0 left-0 w-1 h-full bg-blue-500"></div>
                @elseif($task->status === 'REQUESTED')
                    <div class="absolute top-0 left-0 w-1 h-full bg-gray-400"></div>
                @else
                    <div class="absolute top-0 left-0 w-1 h-full bg-purple-500"></div>
                @endif
                
                <div class="p-4 pl-5">
                    <div class="flex justify-between items-start mb-2">
                        <div class="font-bold text-slate-900 text-lg">{{ $task->mould->code }}</div>
                        <div class="flex flex-col items-end gap-1">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $task->type === 'PM' ? 'bg-blue-50 text-blue-600' : 'bg-red-50 text-red-600' }}">
                                {{ $task->type }}{{ $task->pm_subtype ? ' - ' . $task->pm_subtype : '' }}
                            </span>
                            @if($task->status === 'IN_PROGRESS')
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-600">IN PROGRESS</span>
                            @elseif($task->status === 'IN_REVIEW')
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-50 text-purple-600">IN REVIEW</span>
                            @elseif($task->status === 'REQUESTED')
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-600">REQUESTED</span>
                            @endif
                        </div>
                    </div>
                    
                    <p class="text-sm text-slate-600 mb-4">{{ $task->description }}</p>
                    
                    <div class="flex items-center gap-2 mt-2 pt-3 border-t border-slate-50">
                        @if($task->status === 'APPROVED')
                            <button wire:click="startWork('{{ $task->id }}')" class="flex-1 py-2 bg-blue-600 text-white font-bold text-sm rounded-xl hover:bg-blue-700 transition-colors">
                                Start Work
                            </button>
                        @elseif($task->status === 'REQUESTED')
                            <button wire:click="startWork('{{ $task->id }}')" onclick="return confirm('Supervisor has not approved this yet. Are you sure you want to bypass and start?')" class="flex-1 py-2 bg-slate-800 text-white font-bold text-sm rounded-xl hover:bg-slate-900 transition-colors">
                                Bypass & Start
                            </button>
                        @elseif($task->status === 'IN_PROGRESS')
                            <a href="{{ route('mobile.jobs.complete', $task) }}" class="flex-1 py-2 bg-green-600 text-white font-bold text-sm rounded-xl text-center hover:bg-green-700 transition-colors block">
                                Complete Task
                            </a>
                        @elseif($task->status === 'IN_REVIEW')
                            <div class="w-full text-center text-xs font-semibold text-slate-400 py-2">
                                Awaiting Supervisor Sign-Off
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-12 px-4 bg-slate-50 border border-slate-100 rounded-2xl">
                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-3 shadow-sm">
                    <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                </div>
                <h3 class="font-bold text-slate-700">No Active Tasks</h3>
                <p class="text-sm text-slate-500 mt-1">There are currently no approved maintenance tasks assigned.</p>
            </div>
        @endforelse
    </div>
</div>
