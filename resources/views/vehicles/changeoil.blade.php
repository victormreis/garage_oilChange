<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Register Oil Change</title>
</head>
<body>

<h3>Register new oil Change</h3>

<form action="{{route('vehicles.changeoil', $vehicle)}}" method="POST">
    @csrf

    <label for="odometer">Odometer</label>
    <input type="text" name="odometer" id="odometer" value="{{$vehicle->mileage}}">
    @error('odometer')
    <span>{{$message}}</span>
    @enderror

    <label for="date">Change Date</label>
    <input type="date" name="date" id="date" value="{{old('date')}}">
    @error('date')
    <span style="color: red">{{$message}}</span>
    @enderror


    <button>Change Oil</button>

    <br>
    <br>
    <a href="{{route('vehicles.show', $vehicle)}}">Back to Vehicle Details</a>

</form>
</body>
</html>
