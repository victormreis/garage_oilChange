<x-layout title="Import via CSV">
    <div class="max-w-xl mx-auto mt-10">
        <h1 class="text-2xl font-bold mb-4">Import Vehicles Via CSV</h1>

        @if (session('status'))
            <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
                {{ session('status') }}
            </div>
        @endif

        @error('csv')
        <div class="bg-red-100 text-red-800 p-3 rounded mb-4">
            {{ $message }}
        </div>
        @enderror

        <form action="{{ route('vehicles.import-store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="file" name="csv" accept=".csv,.txt" class="mb-4">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
                Import
            </button>
        </form>
    </div>
</x-layout>
