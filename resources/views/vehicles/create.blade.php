<x-layout title="Register new Vehicle" header="Add new Vehicle to the garage">

    <form action="{{route('vehicles.store')}}" method="POST" class="form">
        @csrf

        <div class="form-field">
            <label for="brand">Brand: </label>
            <input type="text" name="brand" id="brand">
            @error('brand')
            <span class="field-error">{{$message}}</span>
            @enderror
        </div>

        <div class="form-field">
            <label for="model">Model: </label>
            <input type="text" name="model" id="model">
            @error('model')
            <span class="field-error">{{$message}}</span>
            @enderror
        </div>

        <div class="form-field">
            <label for="year">Year: </label>
            <input type="number" name="year" id="year">
            @error('year')
            <span class="field-error">{{$message}}</span>
            @enderror
        </div>

        <div class="form-field">
            <label for="mileage">Mileage: </label>
            <input type="number" name="mileage" id="mileage">
            @error('mileage')
            <span class="field-error">{{$message}}</span>
            @enderror
        </div>

        <button type="submit" class="btn">Register Vehicle</button>
    </form>

    <a href="{{route('vehicles.index')}}" class="link-back">Back to homePage</a>
</x-layout>
