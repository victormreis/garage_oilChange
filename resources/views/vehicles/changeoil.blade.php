<x-layout title="Register Oil Change" header="Register new oil Change">

    <form action="{{route('vehicles.changeoil', $vehicle)}}" method="POST" class="form">
        @csrf

        <div class="form-field">
            <label for="odometer">Odometer</label>
            <input type="text" name="odometer" id="odometer" value="{{$vehicle->mileage}}">
            @error('odometer')
            <span class="field-error">{{$message}}</span>
            @enderror
        </div>

        <div class="form-field">
            <label for="date">Change Date</label>
            <input type="date" name="date" id="date" value="{{old('date')}}">
            @error('date')
            <span class="field-error">{{$message}}</span>
            @enderror
        </div>

        <button class="btn">Change Oil</button>
    </form>

    <a href="{{route('vehicles.show', $vehicle)}}" class="link-back">Back to Vehicle Details</a>
</x-layout>
