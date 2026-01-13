<div class="max-w-4xl mx-auto px-4 py-6">
    <h1 class="text-xl font-semibold mb-4">Mould Detail</h1>

    <div class="bg-white shadow-sm rounded p-4 space-y-2">
        <div><b>Code:</b> {{ $mould->code }}</div>
        <div><b>Name:</b> {{ $mould->name }}</div>
        <div><b>Cavities:</b> {{ $mould->cavities }}</div>
        <div><b>Customer:</b> {{ $mould->customer }}</div>
        <div><b>Resin:</b> {{ $mould->resin }}</div>
        <div><b>Tonnage:</b> {{ $mould->min_tonnage_t }} - {{ $mould->max_tonnage_t }}</div>
        <div><b>Status:</b> {{ $mould->status }}</div>
    </div>
</div>
