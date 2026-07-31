<?php

namespace Tests\Feature;

use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehicleControllerTest extends TestCase
{
    use RefreshDatabase;

    private function makeVehicle(array $overrides = []): Vehicle
    {
        return Vehicle::create(array_merge([
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'year' => 2020,
            'mileage' => 50000,
        ], $overrides));
    }

    public function test_index_displays_all_vehicles(): void
    {
        $vehicle = $this->makeVehicle();

        $response = $this->get(route('vehicles.index'));

        $response->assertOk();
        $response->assertViewIs('vehicles.index');
        $response->assertSee($vehicle->brand);
    }

    public function test_create_displays_form(): void
    {
        $response = $this->get(route('vehicles.create'));

        $response->assertOk();
        $response->assertViewIs('vehicles.create');
    }

    public function test_store_creates_vehicle_with_valid_data(): void
    {
        $data = [
            'brand' => 'Honda',
            'model' => 'Civic',
            'year' => 2022,
            'mileage' => 15000,
        ];

        $response = $this->post(route('vehicles.store'), $data);

        $response->assertRedirect(route('vehicles.index'));
        $this->assertDatabaseHas('vehicles', $data);
    }

    public function test_store_fails_validation_when_required_fields_are_missing(): void
    {
        $response = $this->post(route('vehicles.store'), []);

        $response->assertSessionHasErrors(['brand', 'model', 'year', 'mileage']);
        $this->assertDatabaseCount('vehicles', 0);
    }

    public function test_store_fails_validation_when_year_and_mileage_are_not_integers(): void
    {
        $response = $this->post(route('vehicles.store'), [
            'brand' => 'Honda',
            'model' => 'Civic',
            'year' => 'not-a-year',
            'mileage' => 'not-a-number',
        ]);

        $response->assertSessionHasErrors(['year', 'mileage']);
    }

    public function test_show_displays_vehicle_details_and_metrics(): void
    {
        $vehicle = $this->makeVehicle();

        $response = $this->get(route('vehicles.show', $vehicle));

        $response->assertOk();
        $response->assertViewIs('vehicles.show');
        $response->assertViewHas('vehicle', fn ($v) => $v->is($vehicle));
        $response->assertViewHas('kmSince');
        $response->assertViewHas('daySince');
        $response->assertViewHas('isDue');
    }

    public function test_show_returns_404_for_nonexistent_vehicle(): void
    {
        $response = $this->get(route('vehicles.show', 999));

        $response->assertNotFound();
    }

    public function test_edit_displays_form_with_vehicle_data(): void
    {
        $vehicle = $this->makeVehicle();

        $response = $this->get(route('vehicles.edit', $vehicle));

        $response->assertOk();
        $response->assertViewIs('vehicles.edit');
        $response->assertSee($vehicle->brand);
    }

    public function test_update_updates_vehicle_with_valid_data(): void
    {
        $vehicle = $this->makeVehicle();

        $response = $this->put(route('vehicles.update', $vehicle), [
            'brand' => 'Ford',
            'model' => 'Focus',
            'year' => 2021,
            'mileage' => 60000,
        ]);

        $response->assertRedirect(route('vehicles.show', $vehicle));
        $this->assertDatabaseHas('vehicles', [
            'id' => $vehicle->id,
            'brand' => 'Ford',
            'model' => 'Focus',
            'year' => 2021,
            'mileage' => 60000,
        ]);
    }

    public function test_update_fails_validation_when_required_fields_are_missing(): void
    {
        $vehicle = $this->makeVehicle();

        $response = $this->put(route('vehicles.update', $vehicle), []);

        $response->assertSessionHasErrors(['brand', 'model', 'year', 'mileage']);
        $this->assertDatabaseHas('vehicles', ['id' => $vehicle->id, 'brand' => 'Toyota']);
    }

    public function test_destroy_deletes_vehicle(): void
    {
        $vehicle = $this->makeVehicle();

        $response = $this->delete(route('vehicles.destroy', $vehicle));

        $response->assertRedirect(route('vehicles.index'));
        $this->assertDatabaseMissing('vehicles', ['id' => $vehicle->id]);
    }

    public function test_destroy_also_deletes_related_oil_changes(): void
    {
        $vehicle = $this->makeVehicle();
        $oilChange = $vehicle->oilChanges()->create([
            'odometer' => 40000,
            'date' => now()->subMonth()->toDateString(),
        ]);

        $this->delete(route('vehicles.destroy', $vehicle));

        $this->assertDatabaseMissing('oil_checks', ['id' => $oilChange->id]);
    }

    public function test_show_change_oil_displays_form(): void
    {
        $vehicle = $this->makeVehicle();

        $response = $this->get(route('vehicles.changeoilForm', $vehicle));

        $response->assertOk();
        $response->assertViewIs('vehicles.changeoil');
    }

    public function test_change_oil_creates_oil_change_record_with_valid_data(): void
    {
        $vehicle = $this->makeVehicle();

        $data = [
            'odometer' => 55000,
            'date' => now()->toDateString(),
        ];

        $response = $this->post(route('vehicles.changeoil', $vehicle), $data);

        $response->assertRedirect(route('vehicles.show', $vehicle));
        $this->assertDatabaseHas('oil_checks', [
            'vehicle_id' => $vehicle->id,
            'odometer' => $data['odometer'],
            'date' => $data['date'].' 00:00:00',
        ]);
    }

    public function test_change_oil_fails_validation_when_required_fields_are_missing(): void
    {
        $vehicle = $this->makeVehicle();

        $response = $this->post(route('vehicles.changeoil', $vehicle), []);

        $response->assertSessionHasErrors(['odometer', 'date']);
    }

    public function test_change_oil_fails_validation_when_date_is_in_the_future(): void
    {
        $vehicle = $this->makeVehicle();

        $response = $this->post(route('vehicles.changeoil', $vehicle), [
            'odometer' => 55000,
            'date' => now()->addDay()->toDateString(),
        ]);

        $response->assertSessionHasErrors(['date']);
    }

    public function test_change_oil_fails_when_odometer_is_lower_than_last_change(): void
    {
        $vehicle = $this->makeVehicle();
        $vehicle->oilChanges()->create([
            'odometer' => 45000,
            'date' => now()->subMonth()->toDateString(),
        ]);

        $response = $this->post(route('vehicles.changeoil', $vehicle), [
            'odometer' => 44000,
            'date' => now()->toDateString(),
        ]);

        $response->assertSessionHasErrors(['odometer']);
    }

    public function test_change_oil_fails_when_date_is_before_last_change(): void
    {
        $vehicle = $this->makeVehicle();
        $vehicle->oilChanges()->create([
            'odometer' => 45000,
            'date' => now()->subDays(5)->toDateString(),
        ]);

        $response = $this->post(route('vehicles.changeoil', $vehicle), [
            'odometer' => 46000,
            'date' => now()->subDays(10)->toDateString(),
        ]);

        $response->assertSessionHasErrors(['date']);
    }
}
