<?php

use App\Http\Controllers\VehicleController;
use App\Http\Controllers\VehicleImportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('vehicles.index');
});

Route::get('vehicles/import', [VehicleImportController::class, 'create'])->name('vehicles.import');
Route::post('vehicles/import', [VehicleImportController::class, 'store'])->name('vehicles.import-store');

Route::resource('vehicles', VehicleController::class);

Route::get('vehicles/{vehicle}/changeoil', [VehicleController::class, 'showChangeOil'])->name('vehicles.changeoilForm');
Route::post('vehicles/{vehicle}/changeoil', [VehicleController::class, 'changeOil'])->name('vehicles.changeoil');



