<x-layout title="Garage" header="Garage Vehicles">

    <h3>All Vehicles registered</h3>
    <a href="{{route('vehicles.create')}}">
        <button>Register new Vehicle</button>
    </a>

    @if($vehicles->isEmpty())
        <p>No vehicles registered yet! register your first vehicle</p>

    @endif

    <ul>
        @foreach($vehicles as $vehicle)

            <li class="vehicle-card">
                <p>Brand: {{$vehicle->brand}} - Model: {{$vehicle->model}} - Year: {{$vehicle->year}} </p>
                @if($vehicle->isDue())
                    <span class="badge">Need oil Change</span>
                @endif
                <a href="{{route('vehicles.show', $vehicle)}}">
                    <button>View Details</button>
                </a>
            </li>
        @endforeach
    </ul>
</x-layout>

<style>
    .vehicle-card {
        display: flex;
        align-content: center;
        border: solid black 1px;
        padding: 5px;
    }

    .vehicle-card a {
        margin-top: 15px;
        margin-left: 10px;
    }

    .badge {
        color: red;
        margin-top: 15px;
        margin-left: 15px;
    }
</style>
