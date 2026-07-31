<x-layout title="Edit Vehicle Details" header="Edit Vehicle">

    <form action="{{route('vehicles.update', $vehicle)}}" method="POST" class="form">
        @csrf
        @method('PUT')

        <div class="form-field">
            <label for="brand">Brand: </label>
            <input type="text" name="brand" id="brand" value="{{$vehicle->brand}}">
            @error('brand')
            <span class="field-error">{{$message}}</span>
            @enderror
        </div>

        <div class="form-field">
            <label for="model">Model: </label>
            <input type="text" name="model" id="model" value="{{$vehicle->model}}">
            @error('model')
            <span class="field-error">{{$message}}</span>
            @enderror
        </div>

        <div class="form-field">
            <label for="year">Year: </label>
            <input type="number" name="year" id="year" value="{{$vehicle->year}}">
            @error('year')
            <span class="field-error">{{$message}}</span>
            @enderror
        </div>

        <div class="form-field">
            <label for="mileage">Mileage: </label>
            <input type="number" name="mileage" id="mileage" value="{{$vehicle->mileage}}">
            @error('mileage')
            <span class="field-error">{{$message}}</span>
            @enderror
        </div>

        <button type="submit" class="btn">Update Vehicle</button>
    </form>

    <a href="{{route('vehicles.show', $vehicle)}}" class="link-back">Back to Vehicle Details</a>
</x-layout>
