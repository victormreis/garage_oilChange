<x-layout title="Edit Vehicle Details" header="Edit Vehicle">
    <hr>

    <form action="{{route('vehicles.update', $vehicle)}}" method="POST">
        @csrf
        @method('PUT')

        @error('brand')
        <span style="color: red">{{$message}}</span>
        @enderror
        <label for="brand">Brand: </label>
        <input type="text" name="brand" id="brand" value="{{$vehicle->brand}}">


        @error('model')
        <span style="color: red">{{$message}}</span>
        @enderror
        <label for="model">Model: </label>
        <input type="text" name="model" id="model" value="{{$vehicle->model}}">

        @error('year')
        <span style="color: red">{{$message}}</span>
        @enderror

        <label for="year">Year: </label>
        <input type="number" name="year" id="year" value="{{$vehicle->year}}">

        @error('mileage')
        <span style="color: red">{{$message}}</span>
        @enderror
        <label for="mileage">Mileage: </label>
        <input type="number" name="mileage" id="mileage" value="{{$vehicle->mileage}}">


        <button type="submit">Update Vehicle</button>

    </form>

    <a href="{{route('vehicles.show', $vehicle)}}">Back to Vehicle Details</a>
</x-layout>

<style>
    form {
        display: flex;
        flex-direction: column;
        gap: 10px;

    }
</style>
