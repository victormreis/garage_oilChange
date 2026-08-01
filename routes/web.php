<?php

use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('vehicles.index');
});

Route::resource('vehicles', VehicleController::class);

Route::get('vehicles/{vehicle}/changeoil', [VehicleController::class, 'showChangeOil'])->name('vehicles.changeoilForm');
Route::post('vehicles/{vehicle}/changeoil', [VehicleController::class, 'changeOil'])->name('vehicles.changeoil');



