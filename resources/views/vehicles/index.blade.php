<x-layout title="Garage" header="Garage Vehicles">

    <h3 class="section-title">All Vehicles registered</h3>
    <div class="actions">
        <a href="{{route('vehicles.create')}}" class="btn">Register new Vehicle</a>
    </div>

    @if($vehicles->isEmpty())
        <p class="empty-state">No vehicles registered yet! register your first vehicle</p>
    @endif

    <ul class="vehicle-list">
        @foreach($vehicles as $vehicle)
            <li class="vehicle-card">
                <div class="vehicle-card-info">
                    <span>Brand: {{$vehicle->brand}} - Model: {{$vehicle->model}} - Year: {{$vehicle->year}}</span>
                    @if($vehicle->isDue())
                        <span class="badge badge-warning">Need oil Change</span>
                    @endif
                </div>
                <a href="{{route('vehicles.show', $vehicle)}}" class="btn">View Details</a>
            </li>
        @endforeach
    </ul>
</x-layout>
