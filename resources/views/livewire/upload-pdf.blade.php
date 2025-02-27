<div class="p-6 bg-black rounded-lg shadow-md">
    <h2 class="text-lg font-bold mb-4">Subir Calendario Tributario (PDF)</h2>

    @if (session()->has('message'))
        <div class="p-2 mb-4 text-green-700 bg-green-200 rounded">
            {{ session('message') }}
        </div>
    @endif

    <form wire:submit.prevent="procesarPDF" class="space-y-4">
        <input type="file" wire:model="pdf" accept="application/pdf" class="block w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-300">
        
        @error('pdf')
            <span class="text-red-500 text-sm">{{ $message }}</span>
        @enderror

        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Subir y Procesar
        </button>
    </form>
</div>
