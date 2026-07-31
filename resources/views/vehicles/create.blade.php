<x-layout title="Register new Vehicle" header="Add new Vehicle to the garage">

    <hr>

    <form action="{{route('vehicles.store')}}" method="POST">
        @csrf

        @error('brand')
        <span style="color: red">{{$message}}</span>
        @enderror
        <label for="brand">Brand: </label>
        <input type="text" name="brand" id="brand">


        @error('model')
        <span style="color: red">{{$message}}</span>
        @enderror
        <label for="model">Model: </label>
        <input type="text" name="model" id="model">

        @error('year')
        <span style="color: red">{{$message}}</span>
        @enderror

        <label for="year">Year: </label>
        <input type="number" name="year" id="year">

        @error('mileage')
        <span style="color: red">{{$message}}</span>
        @enderror
        <label for="mileage">Mileage: </label>
        <input type="number" name="mileage" id="mileage">


        <button type="submit">Register Vehicle</button>

    </form>

    <a href="{{route('vehicles.index')}}">Back to homePage</a>
</x-layout>


