<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-xl font-semibold">QR Batch - Mould</h1>
        <button onclick="window.print()" class="px-4 py-2 rounded bg-gray-900 text-white text-sm">
            Print
        </button>
    </div>

    <div class="bg-white shadow-sm rounded p-4 mb-4 flex items-center gap-3">
        <input type="text" wire:model.debounce.400ms="search"
            class="w-full rounded border-gray-300"
            placeholder="Search code/name...">
        <select wire:model="perPage" class="rounded border-gray-300">
            <option value="24">24</option>
            <option value="48">48</option>
            <option value="96">96</option>
        </select>
    </div>

    <style>
        @media print {
            .no-print { display:none !important; }
            body { background: #fff; }
        }
        .label {
            width: 240px;
            height: 140px;
            border: 1px dashed #ccc;
            border-radius: 10px;
            padding: 10px;
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .label .meta { font-size: 12px; line-height: 1.2; }
    </style>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($moulds as $m)
            <div class="label">
                <div>
                    {!! QrCode::size(90)->margin(1)->generate(route('moulds.show', $m->id)) !!}
                </div>
                <div class="meta">
                    <div><b>{{ $m->code }}</b></div>
                    <div>{{ $m->name }}</div>
                    <div>Cav: {{ $m->cavities }}</div>
                    <div>Status: {{ $m->status }}</div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-4 no-print">
        {{ $moulds->links() }}
    </div>
</div>
