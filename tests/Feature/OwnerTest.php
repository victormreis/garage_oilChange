<?php

namespace Tests\Feature;

use App\Models\Owner;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OwnerTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_have_multiple_vehicles(): void
    {
        $owner = Owner::create([
            'name' => 'Victor',
            'email' => 'victor@example.com',
            'phone' => '555-0100',
        ]);

        $owner->vehicles()->create(['brand' => 'Toyota', 'model' => 'Corolla', 'year' => 2020, 'mileage' => 40000]);
        $owner->vehicles()->create(['brand' => 'Honda', 'model' => 'Civic', 'year' => 2022, 'mileage' => 10000]);

        $this->assertCount(2, $owner->fresh()->vehicles);
    }

    public function test_vehicle_owner_is_nullable(): void
    {
        $vehicle = Vehicle::create(['brand' => 'Ford', 'model' => 'Focus', 'year' => 2021, 'mileage' => 20000]);

        $this->assertNull($vehicle->owner);
    }

    public function test_vehicle_belongs_to_its_owner(): void
    {
        $owner = Owner::create(['name' => 'Ana', 'email' => 'ana@example.com', 'phone' => '555-0101']);
        $vehicle = $owner->vehicles()->create(['brand' => 'Mazda', 'model' => 'CX-5', 'year' => 2019, 'mileage' => 60000]);

        $this->assertTrue($vehicle->owner->is($owner));
    }
}
