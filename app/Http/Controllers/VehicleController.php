<?php

namespace App\Http\Controllers;

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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'brand' => 'required',
            'model' => 'required',
            'year' => 'required|integer',
            'mileage' => 'required|integer',
        ]);

        $vehicle = Vehicle::create($validated);

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
    public function update(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'brand' => 'required',
            'model' => 'required',
            'year' => 'required|integer',
            'mileage' => 'required|integer',
        ]);

        $vehicle->update($validated);

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


    public function changeOil(Request $request, Vehicle $vehicle) {

        $validated = $request->validate([
            'odometer' => 'required|integer',
            'date' => 'required|date|before_or_equal:today',
        ]);

        $lastChange = $vehicle->getLastChange();

        if($lastChange) {
            if($lastChange->odometer > $validated['odometer']) {
                return back()->withErrors(['odometer' => 'The odometer must be less than or equal to the odometer on last change.']);
            }
            if($lastChange->date < $validated['date']) {
                return back()->withErrors(['date' => 'The date must be grater than or equal to the date on last change.']);
            }
        }

        $vehicle->oilChanges()->create($validated);

        return redirect(route('vehicles.show', $vehicle));

    }
}
