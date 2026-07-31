@props([
    'title',
    'header'
])
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{$title}} - Garage</title>

    @fonts
    @vite(['resources/css/app.css'])
</head>
<body>
<header class="site-header">
    <div class="container">
        <a href="{{route('vehicles.index')}}" class="brand">🔧 Garage</a>
    </div>
</header>

<main class="container">
    <h1 class="page-title">{{$header}}</h1>

    {{$slot}}
</main>
</body>
</html>
