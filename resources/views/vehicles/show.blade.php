<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Vehicle Details</title>
</head>
<body>

<h1>Vehicle Details</h1>
<hr>
<div class="vehicle-details">
    <p>Brand: {{$vehicle->brand}}</p>
    <p>Model: {{$vehicle->model}}</p>
    <p>Year: {{$vehicle->year}}</p>
    <p>Mileage: {{$vehicle->mileage}}</p>

    <div class="buttons">

        <a href="{{route('vehicles.edit', $vehicle)}}">
            <button>Edit Vehicle</button>
        </a>
        <form action="{{route('vehicles.destroy', $vehicle)}}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit">Delete Vehicle</button>
        </form>
    </div>
</div>
<hr>
@if($isDue)
    <p>This Vehicle  <span style="color: red">Need an Oil Change</span></p>
@else
    <p>This Vehicle  <span style="color: green">Don't Need </span> an Oil Change</p>
    <p>
        Remaining: {{5000 -$kmSince}} Km or  {{180 - (int)$daySince}} Days,  wichever comes first!
    </p>

@endif
<h3>Oil Change History </h3>
<a href="{{route('vehicles.changeoilForm', $vehicle)}}">
    <button>Add new Oil Change</button>
</a>
@if($vehicle->getLastChange() == null)
    <p>No oil Change registered yet!</p>
@else
    @foreach($vehicle->oilchanges as $change)
        <p>Oil Change Date: {{$change->date->format('d-m-Y')}}</p>
        <p>Oil Change Mileage: {{$change->odometer}}</p>
    @endforeach
@endif
<hr>
<a href="{{route('vehicles.index')}}">Back to homePage</a>
</body>
</html>

<style>
    .buttons {
        display: flex;
        gap: 10px;
    }

    .buttons form button {
        color: red;
    }
</style>
