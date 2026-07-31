<x-layout title="Vehicle Details" header="Vehicle Details">

    <div class="card vehicle-details">
        <p>Brand: {{$vehicle->brand}}</p>
        <p>Model: {{$vehicle->model}}</p>
        <p>Year: {{$vehicle->year}}</p>
        <p>Mileage: {{$vehicle->mileage}}</p>

        <div class="actions">
            <a href="{{route('vehicles.edit', $vehicle)}}" class="btn">Edit Vehicle</a>
            <form action="{{route('vehicles.destroy', $vehicle)}}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Delete Vehicle</button>
            </form>
        </div>
    </div>

    @if($isDue)
        <p class="status-banner status-due">This Vehicle Need an Oil Change</p>
    @else
        <p class="status-banner status-ok">
            This Vehicle Don't Need an Oil Change
            <span class="status-remaining">
                Remaining: {{5000 -$kmSince}} Km or {{180 - (int)$daySince}} Days, whichever comes first!
            </span>
        </p>
    @endif

    <h3 class="section-title">Oil Change History</h3>
    <div class="actions">
        <a href="{{route('vehicles.changeoilForm', $vehicle)}}" class="btn">Add new Oil Change</a>
    </div>

    @if($vehicle->getLastChange() == null)
        <p class="empty-state">No oil Change registered yet!</p>
    @else
        <ul class="history-list">
            @foreach($vehicle->oilchanges as $change)
                <li class="history-item">
                    <span>Oil Change Date: {{$change->date->format('d-m-Y')}}</span>
                    <span>Oil Change Mileage: {{$change->odometer}}</span>
                </li>
            @endforeach
        </ul>
    @endif

    <hr>
    <a href="{{route('vehicles.index')}}" class="link-back">Back to homePage</a>
</x-layout>
