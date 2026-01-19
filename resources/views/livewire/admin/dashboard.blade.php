<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">System Administration</h1>
            <p class="text-gray-500">Master Data & Audit Logs</p>
        </div>
        <div class="space-x-2">
            <a href="{{ route('moulds.index') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium">Moulds</a>
            <a href="{{ route('machines.index') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium">Machines</a>
            <a href="{{ route('users.index') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium">User Management</a>
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="text-sm font-medium text-gray-500">Total Moulds</div>
            <div class="text-2xl font-bold text-gray-900 mt-2">{{ number_format($stats['moulds']) }}</div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="text-sm font-medium text-gray-500">Total Users</div>
            <div class="text-2xl font-bold text-gray-900 mt-2">{{ number_format($stats['users']) }}</div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="text-sm font-medium text-gray-500">Machines</div>
            <div class="text-2xl font-bold text-gray-900 mt-2">{{ number_format($stats['machines']) }}</div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="text-sm font-medium text-gray-500">Active Runs</div>
            <div class="text-2xl font-bold text-blue-600 mt-2">{{ number_format($stats['active_runs']) }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Activity Log --}}
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
             <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/50 flex justify-between items-center">
                <h3 class="font-semibold text-gray-900">Recent System Activity</h3>
                <span class="text-xs text-gray-400">Last 15 events</span>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($activities as $log)
                    <div class="p-4 hover:bg-gray-50 flex items-start gap-4">
                        <div class="mt-1">
                            <div class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 font-bold text-xs uppercase">
                                {{ substr($log->event, 0, 1) }}
                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between items-start">
                                <span class="text-sm font-medium text-gray-900">
                                    <span class="capitalize">{{ $log->event }}</span>
                                    <span class="text-gray-500 font-normal">on</span>
                                    {{ Str::afterLast($log->subject_type, '\\') }} #{{ $log->subject_id }}
                                </span>
                                <span class="text-xs text-gray-400 whitespace-nowrap">{{ $log->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                by <span class="font-medium text-gray-700">{{ $log->causer?->name ?? 'System' }}</span>
                                @if($log->description && $log->description !== $log->event)
                                    - {{ $log->description }}
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-400 text-sm">No activity logs found.</div>
                @endforelse
            </div>
        </div>

        {{-- User Distribution --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden h-fit">
             <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/50">
                <h3 class="font-semibold text-gray-900">User Distribution</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($usersByRole as $role)
                        <div>
                             <div class="flex justify-between items-end mb-1">
                                <span class="text-sm font-medium text-gray-700 capitalize">{{ $role->name }}</span>
                                <span class="text-sm font-bold text-gray-900">{{ $role->count }}</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                <div class="bg-indigo-500 h-2 rounded-full" style="width: {{ ($stats['users'] > 0) ? ($role->count / $stats['users']) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
