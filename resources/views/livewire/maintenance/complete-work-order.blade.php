<div class="min-h-screen bg-slate-50 flex flex-col">
    <!-- Header -->
    <div class="bg-white border-b sticky top-0 z-10 px-4 py-3 flex items-center shadow-sm">
        @if(request()->routeIs('mobile.*'))
            <a href="{{ route('mobile.mould-detail', $event->mould_id) }}" class="mr-3 text-slate-500 hover:text-slate-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
        @else
            <a href="{{ route('maintenance.work-orders') }}" class="mr-3 text-slate-500 hover:text-slate-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
        @endif
        <div>
            <h1 class="text-lg font-bold text-slate-900 leading-tight">Complete Work Order</h1>
            <p class="text-xs text-slate-500">{{ $event->mould->code }} - {{ $event->type }}{{ $event->pm_subtype ? ' (' . $event->pm_subtype . ')' : '' }}</p>
        </div>
    </div>

    <!-- Body -->
    <div class="flex-1 overflow-y-auto p-4 max-w-5xl mx-auto w-full {{ $isMobile ? 'pb-40' : 'pb-24' }}">
        
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 mb-6">
            <h2 class="text-sm font-bold text-slate-800 mb-2">Job Details</h2>
            <p class="text-sm text-slate-600 mb-4">{{ $event->description }}</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Downtime (Minutes)</label>
                    <input type="number" wire:model.defer="downtimeMin" class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="0">
                    @error('downtimeMin') <span class="text-xs text-red-500 block mt-1 error-scroll-target">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Cost Estimate (Optional)</label>
                    <input type="number" step="0.01" wire:model.defer="cost" class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="0.00">
                    @error('cost') <span class="text-xs text-red-500 block mt-1 error-scroll-target">{{ $message }}</span> @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-slate-700 mb-1">Parts Used (Optional)</label>
                    <textarea wire:model.defer="partsUsed" rows="2" class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="e.g. O-ring, Heater band"></textarea>
                    @error('partsUsed') <span class="text-xs text-red-500 block mt-1 error-scroll-target">{{ $message }}</span> @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-slate-700 mb-1">Completion Notes (Optional)</label>
                    <textarea wire:model.defer="notes" rows="2" class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="Additional details about the work performed..."></textarea>
                    @error('notes') <span class="text-xs text-red-500 block mt-1 error-scroll-target">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        @if(count($checklist) > 0)
            <div class="bg-white rounded-xl shadow-sm border border-blue-200 p-4 mb-6">
                <h3 class="font-bold text-blue-900 text-base mb-4">Maintenance Checklist</h3>
                @error('checklist')
                    <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-600 font-bold error-scroll-target">
                        {{ $message }}
                    </div>
                @enderror

                @if(in_array($event->pm_subtype, ['DAILY', 'WEEKLY']))
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-700">
                            <thead class="bg-blue-50 text-blue-900">
                                <tr>
                                    <th class="p-3 rounded-tl-lg font-semibold">Point Check</th>
                                    <th class="p-3 font-semibold text-center w-20">Cleaning</th>
                                    <th class="p-3 font-semibold text-center w-20">Lubricate</th>
                                    <th class="p-3 rounded-tr-lg font-semibold">Remark</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($checklist as $index => $item)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="p-3 font-medium">{{ $item['task'] ?? '' }}</td>
                                    <td class="p-3 text-center">
                                        <input type="checkbox" wire:model="checklist.{{ $index }}.cleaning" class="w-5 h-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                                    </td>
                                    <td class="p-3 text-center">
                                        <input type="checkbox" wire:model="checklist.{{ $index }}.lubricate" class="w-5 h-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                                    </td>
                                    <td class="p-3">
                                        <input type="text" wire:model="checklist.{{ $index }}.remark" class="w-full text-sm rounded-md border-slate-200 p-2 focus:ring-blue-500 focus:border-blue-500 placeholder-slate-300" placeholder="Optional remark...">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                @elseif($event->pm_subtype === 'PPM')
                    @php
                        $groups = collect($checklist)->groupBy('group');
                    @endphp
                    @foreach($groups as $groupName => $items)
                        <div class="mb-8 last:mb-0">
                            <h4 class="font-bold text-slate-800 text-lg mb-3 flex items-center gap-2">
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm">{{ $groupName }}</span>
                            </h4>
                            <div class="overflow-x-auto border border-slate-200 rounded-lg">
                                <table class="w-full text-left text-sm text-slate-700 bg-white">
                                    <thead class="bg-slate-50 border-b border-slate-200">
                                        <tr>
                                            <th class="p-3 font-semibold w-1/3 min-w-[200px]">Check Item</th>
                                            <th class="p-3 font-semibold w-1/4 min-w-[150px]">Standard</th>
                                            <th class="p-3 font-semibold w-32">Status</th>
                                            <th class="p-3 font-semibold min-w-[150px]">Remark</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach($items as $originalIndex => $item)
                                        @php
                                            $idx = collect($checklist)->search(function ($val) use ($item) {
                                                return $val['task'] === $item['task'] && $val['group'] === $item['group'];
                                            });
                                        @endphp
                                        <tr class="hover:bg-slate-50 transition-colors">
                                            <td class="p-3 align-top">
                                                <div class="font-medium text-slate-900 mb-1">{{ $item['task'] ?? '' }}</div>
                                                <div class="text-xs text-slate-500 uppercase tracking-wide">{{ $item['methode_check'] ?? '' }}</div>
                                            </td>
                                            <td class="p-3 align-top text-slate-600">
                                                {{ $item['standard_value'] ?? '' }}
                                            </td>
                                            <td class="p-3 align-top">
                                                <div class="flex items-center gap-2">
                                                    <button type="button" 
                                                        wire:click="$set('checklist.{{ $idx }}.status', 'OK')"
                                                        class="flex-1 py-2 px-3 rounded-md text-sm font-bold transition-colors border {{ (isset($checklist[$idx]['status']) && $checklist[$idx]['status'] === 'OK') ? 'bg-green-100 border-green-500 text-green-700 shadow-inner' : 'bg-white border-slate-300 text-slate-500 hover:bg-slate-50' }}">
                                                        OK
                                                    </button>
                                                    <button type="button" 
                                                        wire:click="$set('checklist.{{ $idx }}.status', 'NG')"
                                                        class="flex-1 py-2 px-3 rounded-md text-sm font-bold transition-colors border {{ (isset($checklist[$idx]['status']) && $checklist[$idx]['status'] === 'NG') ? 'bg-red-100 border-red-500 text-red-700 shadow-inner' : 'bg-white border-slate-300 text-slate-500 hover:bg-slate-50' }}">
                                                        NG
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="p-3 align-top">
                                                <input type="text" wire:model.defer="checklist.{{ $idx }}.remark" class="w-full text-sm rounded-md border-slate-200 p-2 focus:ring-blue-500 focus:border-blue-500 mb-2" placeholder="Add note...">
                                                
                                                @if(isset($checklist[$idx]['status']) && $checklist[$idx]['status'] === 'NG')
                                                    @php
                                                        $hasPhoto = isset($photos[$idx]);
                                                    @endphp
                                                    <div class="mt-2 p-2 rounded-lg border relative transition-colors {{ $hasPhoto ? 'bg-green-50 border-green-300' : 'bg-red-50 border-dashed border-red-300' }}">
                                                        <div class="flex items-center justify-between mb-1">
                                                            <label class="block text-xs font-bold {{ $hasPhoto ? 'text-green-700' : 'text-red-700' }}">
                                                                @if($hasPhoto)
                                                                    <span class="flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Evidence Attached</span>
                                                                @else
                                                                    Photo Evidence Required
                                                                @endif
                                                            </label>
                                                            @if($hasPhoto)
                                                                <label class="text-[10px] text-blue-600 hover:underline cursor-pointer font-bold uppercase tracking-wider">
                                                                    Replace
                                                                    <input type="file" wire:model="photos.{{ $idx }}" accept="image/*" class="hidden">
                                                                </label>
                                                            @endif
                                                        </div>
                                                        
                                                        @if($hasPhoto && is_object($photos[$idx]) && method_exists($photos[$idx], 'temporaryUrl'))
                                                            <div class="mt-2 mb-1">
                                                                <img src="{{ $photos[$idx]->temporaryUrl() }}" class="h-16 w-16 object-cover rounded-md border border-green-200 shadow-sm" alt="Evidence Preview">
                                                            </div>
                                                        @endif
                                                        
                                                        @if(!$hasPhoto)
                                                            <input type="file" wire:model="photos.{{ $idx }}" accept="image/*" class="w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-red-100 file:text-red-700 hover:file:bg-red-200">
                                                        @endif
                                                        
                                                        <div wire:loading wire:target="photos.{{ $idx }}" class="text-xs text-blue-600 mt-1 font-medium">Uploading...</div>
                                                        @error('photos.'.$idx) <span class="text-xs text-red-600 block mt-1 error-scroll-target">{{ $message }}</span> @enderror
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach

                @else
                    {{-- Legacy / Basic PM --}}
                    <div class="space-y-2">
                        @foreach($checklist as $index => $item)
                            <label class="flex items-start gap-3 p-3 rounded-lg border border-slate-100 hover:bg-slate-50 cursor-pointer transition-colors bg-white shadow-sm">
                                <div class="flex-shrink-0 mt-0.5">
                                    <input type="checkbox" wire:model="checklist.{{ $index }}.completed" class="w-5 h-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                </div>
                                <span class="text-sm font-medium text-slate-700 leading-snug {{ !empty($item['completed']) ? 'line-through text-slate-400' : '' }}">
                                    {{ $item['task'] }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- Sticky Footer -->
    <div class="fixed {{ $isMobile ? 'bottom-16' : 'bottom-0' }} left-0 w-full bg-white border-t p-4 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] z-20 flex justify-end gap-3">
        @if($isMobile)
            <a href="{{ route('mobile.mould-detail', $event->mould_id) }}" class="px-6 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-semibold bg-white hover:bg-slate-50 transition-colors text-center">
                Cancel
            </a>
        @else
            <a href="{{ route('maintenance.work-orders') }}" class="px-6 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-semibold bg-white hover:bg-slate-50 transition-colors text-center">
                Cancel
            </a>
        @endif
        <button wire:click="save" wire:loading.attr="disabled" class="px-6 py-2.5 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 transition-colors shadow-sm disabled:opacity-50 disabled:cursor-not-allowed min-w-[120px]">
            <span wire:loading.remove wire:target="save">Complete Work</span>
            <span wire:loading wire:target="save">Saving...</span>
        </button>
    </div>

    @script
    <script>
        $wire.on('scrollToFirstError', () => {
            setTimeout(() => {
                const firstError = document.querySelector('.error-scroll-target');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }, 50);
        });
    </script>
    @endscript
</div>
