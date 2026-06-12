<div>
    <div class="space-y-4" x-data="{ scanning: true, result: null, errorMessage: null }">
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <h1 class="text-xl font-bold text-gray-900 mb-2">Scan QR Code</h1>
        <p class="text-xs text-gray-500 mb-4">Point your camera at a Mould or Machine QR code.</p>

        {{-- Camera Viewport --}}
        <div id="reader" class="rounded-lg overflow-hidden bg-black aspect-square relative">
             {{-- Overlay or Loading State --}}
             <div class="absolute inset-0 flex items-center justify-center text-white/50 text-sm">
                 Initializing Camera...
             </div>
        </div>
        
        {{-- Simulation for Desktop/Testing --}}
        <div class="mt-4 pt-4 border-t border-gray-100">
            <label class="block text-xs font-medium text-gray-700 mb-1">Simulate Scan (Debug)</label>
            <div class="flex gap-2">
                <input type="text" x-model="result" placeholder="e.g. MOULD:uuid" class="flex-1 text-sm rounded-lg border-gray-300">
                <button @click="$wire.handleScan(result)" class="bg-gray-800 text-white px-3 py-2 rounded-lg text-xs font-bold">GO</button>
            </div>
        </div>
    </div>

    @assets
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    @endassets

    @script
    <script>
    document.addEventListener('livewire:initialized', function () {
        Livewire.hook('morph.added', function (params) {
            // Re-init if needed
        });

        const onScanSuccess = function (decodedText, decodedResult) {
            console.log('Code matched', decodedText);
            html5QrcodeScanner.clear();
            $wire.handleScan(decodedText);
        };

        const onScanFailure = function (error) {
        };

        if (document.getElementById('reader')) {
             const html5QrcodeScanner = new Html5QrcodeScanner(
                'reader',
                { fps: 10, qrbox: {width: 250, height: 250} },
                false
            );
            html5QrcodeScanner.render(onScanSuccess, onScanFailure);
        }
    });
    </script>
    @endscript
</div>
