<?php

namespace App\Http\Controllers;

use App\Jobs\ImportVehiclesFromCsv;
use Illuminate\Http\Request;

class VehicleImportController extends Controller
{
    public function create() {
        return view('vehicles.import');
    }

    public function store(Request $request) {
        $request->validate([
            'csv' => 'required|file|mimes:csv,txt'
        ]);

        $path = $request->file('csv')->store('imports');

        ImportVehiclesFromCsv::dispatch($path);

        return redirect()->route('vehicles.index')->
        with('status' ,'Import started successfully! Vehicles will be visible ASAP!');
    }
}
