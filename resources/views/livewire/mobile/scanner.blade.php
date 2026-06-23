<div>
    <div class="space-y-4" x-data="{ scanning: true, result: null, errorMessage: null }">
        <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-100">
            
            {{-- Tabs --}}
            <div class="flex border-b border-slate-200 mb-4">
                <button wire:click="$set('activeTab', 'scan')" class="flex-1 py-2 text-center text-sm font-medium border-b-2 transition-colors {{ $activeTab === 'scan' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
                    Camera Scan
                </button>
                <button wire:click="$set('activeTab', 'manual')" class="flex-1 py-2 text-center text-sm font-medium border-b-2 transition-colors {{ $activeTab === 'manual' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
                    Manual Search
                </button>
            </div>

            @if($activeTab === 'scan')
                <h1 class="text-xl font-bold text-slate-900 mb-2">Scan QR Code</h1>
                <p class="text-xs text-slate-500 mb-4">Point your camera at a Mould or Machine QR code.</p>

                {{-- Camera Viewport --}}
                <div id="reader" class="rounded-lg overflow-hidden bg-black aspect-square relative" wire:ignore>
                     {{-- Overlay or Loading State --}}
                     <div id="camera-loading" class="absolute inset-0 flex items-center justify-center text-white/50 text-sm z-10 pointer-events-none">
                         Initializing Camera...
                     </div>
                </div>
                
                {{-- Simulation for Desktop/Testing --}}
                <div class="mt-4 pt-4 border-t border-slate-100">
                    <label class="block text-xs font-medium text-slate-700 mb-1">Simulate Scan (Debug)</label>
                    <div class="flex gap-2">
                        <input type="text" x-model="result" placeholder="e.g. MOULD:uuid" class="flex-1 text-sm rounded-lg border-slate-300">
                        <button @click="$wire.handleScan(result)" class="bg-slate-800 text-white px-3 py-2 rounded-lg text-xs font-bold">GO</button>
                    </div>
                </div>
            @else
                <h1 class="text-xl font-bold text-slate-900 mb-2">Manual Selection</h1>
                <p class="text-xs text-slate-500 mb-4">Search for a mould or machine by its code or name.</p>
                
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                          <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input wire:model.live.debounce.300ms="search" type="text" class="pl-10 w-full text-sm rounded-lg border-slate-300 focus:ring-blue-500 focus:border-blue-500" placeholder="Search M-01 or Machine name...">
                </div>

                @if(strlen($search) >= 2)
                    <div class="mt-4 space-y-4">
                        @if($mouldResults->count() > 0)
                            <div>
                                <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Moulds</h3>
                                <div class="space-y-2">
                                    @foreach($mouldResults as $mould)
                                        <button wire:click="selectMould('{{ $mould->id }}')" class="w-full flex items-center justify-between p-3 rounded-lg border border-slate-200 hover:bg-slate-50 transition-colors text-left">
                                            <div>
                                                <div class="font-bold text-slate-900">{{ $mould->code }}</div>
                                                <div class="text-xs text-slate-500">{{ $mould->name }}</div>
                                            </div>
                                            <svg class="h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($machineResults->count() > 0)
                            <div>
                                <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Machines</h3>
                                <div class="space-y-2">
                                    @foreach($machineResults as $machine)
                                        <button wire:click="selectMachine('{{ $machine->id }}')" class="w-full flex items-center justify-between p-3 rounded-lg border border-slate-200 hover:bg-slate-50 transition-colors text-left">
                                            <div>
                                                <div class="font-bold text-slate-900">{{ $machine->code }}</div>
                                                <div class="text-xs text-slate-500">{{ $machine->name }}</div>
                                            </div>
                                            <svg class="h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($mouldResults->count() == 0 && $machineResults->count() == 0)
                            <div class="p-4 text-center text-sm text-slate-500 bg-slate-50 rounded-lg">
                                No moulds or machines found matching "{{ $search }}".
                            </div>
                        @endif
                    </div>
                @endif
            @endif
        </div>
    </div>

    @assets
        <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    @endassets

    @script
        <script>
            document.addEventListener('livewire:initialized', function () {
                let html5QrCode;

                const startCamera = () => {
                    if (html5QrCode) {
                        html5QrCode.stop().catch(e => console.log(e));
                    }
                    html5QrCode = new Html5Qrcode("reader");
                    html5QrCode.start(
                        { facingMode: "environment" }, 
                        { fps: 10, qrbox: { width: 250, height: 250 } },
                        (decodedText, decodedResult) => {
                            console.log('Code matched', decodedText);
                            html5QrCode.stop().then(() => {
                                $wire.handleScan(decodedText);
                            }).catch(err => {
                                $wire.handleScan(decodedText);
                            });
                        },
                        (errorMessage) => {
                            // ignore parse errors
                        }
                    ).then(() => {
                        const loadingEl = document.getElementById('camera-loading');
                        if(loadingEl) loadingEl.style.display = 'none';
                    }).catch((err) => {
                        console.warn("Camera start error:", err);
                        const loadingEl = document.getElementById('camera-loading');
                        if(loadingEl) {
                            loadingEl.style.pointerEvents = 'auto';
                            loadingEl.innerHTML = '<div class="text-center p-4"><p class="mb-2">Camera access denied.</p><button onclick="window.location.reload()" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-xs font-bold">Retry</button></div>';
                        }
                    });
                };

                // Watch for tab changes to start/stop camera
                Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
                    succeed(({ snapshot, effect }) => {
                        if (component.name === 'mobile.scanner') {
                            setTimeout(() => {
                                if (document.getElementById('reader')) {
                                    startCamera();
                                } else if (html5QrCode) {
                                    // Stop camera if we navigated to manual tab
                                    try { html5QrCode.stop(); } catch(e) {}
                                }
                            }, 50);
                        }
                    });
                });

                if (document.getElementById('reader')) {
                    startCamera();
                }
            });
        </script>
    @endscript
</div>
