<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateOilChangeRequest;
use App\Http\Requests\SaveVehicleRequest;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vehicles = Vehicle::query()->with('oilChanges')->get();

        return view('vehicles.index', compact('vehicles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('vehicles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SaveVehicleRequest $request)
    {

        Vehicle::create($request->validated());

        return redirect(route('vehicles.index'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Vehicle $vehicle)
    {
        $metrics = $vehicle->getMetrics();

        return view('vehicles.show', [
            'vehicle' => $vehicle,
            'kmSince' => $metrics['kmSince'],
            'daySince' => $metrics['daySince'],
            'isDue' => $vehicle->isDue(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vehicle $vehicle)
    {
        return view('vehicles.edit', compact('vehicle'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SaveVehicleRequest $request, Vehicle $vehicle)
    {
        $vehicle->update($request->validated());

        return redirect(route('vehicles.show', $vehicle));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();

        return redirect(route('vehicles.index'));
    }

    public function showChangeOil(Vehicle $vehicle)
    {
        return view('vehicles.changeoil', compact('vehicle'));

    }


    public function changeOil(CreateOilChangeRequest $request, Vehicle $vehicle)
    {

        $vehicle->oilChanges()->create($request->validated());

        return redirect(route('vehicles.show', $vehicle));

    }
}
