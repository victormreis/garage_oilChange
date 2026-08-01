<?php

namespace Tests\Feature;

use App\Filament\Resources\VehicleResource\Pages\EditVehicle;
use App\Filament\Resources\VehicleResource\Pages\ListVehicles;
use App\Filament\Resources\VehicleResource\RelationManagers\OilChangesRelationManager;
use App\Jobs\ImportVehiclesFromCsv;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class VehicleResourceSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_vehicle_index_renders_with_import_button_and_columns(): void
    {
        $user = User::factory()->create();

        $vehicle = Vehicle::create(['brand' => 'Toyota', 'model' => 'Corolla', 'year' => 2020, 'mileage' => 40000]);
        $vehicle->oilChanges()->create(['odometer' => 35000, 'date' => now()->subDays(200)]);

        Livewire::actingAs($user)
            ->test(ListVehicles::class)
            ->assertOk()
            ->assertSeeText('Import CSV')
            ->assertSeeText('Toyota');
    }

    public function test_vehicle_index_query_count_stays_constant_regardless_of_row_count(): void
    {
        $user = User::factory()->create();

        for ($i = 0; $i < 15; $i++) {
            $vehicle = Vehicle::create(['brand' => "Brand{$i}", 'model' => 'X', 'year' => 2020, 'mileage' => 10000]);
            $vehicle->oilChanges()->create(['odometer' => 9000, 'date' => now()->subDays(10)]);
            $vehicle->oilChanges()->create(['odometer' => 9500, 'date' => now()->subDays(2)]);
        }

        $queryCount = 0;
        DB::listen(function () use (&$queryCount) {
            $queryCount++;
        });

        Livewire::actingAs($user)->test(ListVehicles::class)->assertOk();

        // With eager-loaded lastOilChange this should be a small constant number of
        // queries (vehicles + lastOilChange), not ~1 per row.
        $this->assertLessThan(15, $queryCount, "Expected a constant query count, got {$queryCount} (possible N+1).");
    }

    public function test_is_due_and_last_change_reflect_eager_loaded_relation(): void
    {
        $dueVehicle = Vehicle::create(['brand' => 'Honda', 'model' => 'Civic', 'year' => 2018, 'mileage' => 20000]);
        $dueVehicle->oilChanges()->create(['odometer' => 13000, 'date' => now()->subDays(200)]);

        $notDueVehicle = Vehicle::create(['brand' => 'Ford', 'model' => 'Focus', 'year' => 2022, 'mileage' => 5000]);
        $notDueVehicle->oilChanges()->create(['odometer' => 4000, 'date' => now()->subDays(10)]);

        $vehicles = Vehicle::with('lastOilChange')->get()->keyBy('brand');

        $this->assertTrue($vehicles['Honda']->isDue());
        $this->assertFalse($vehicles['Ford']->isDue());
        $this->assertEquals(13000, $vehicles['Honda']->getLastChange()->odometer);
    }

    public function test_relation_manager_tab_is_registered_on_edit_page(): void
    {
        $user = User::factory()->create();
        $vehicle = Vehicle::create(['brand' => 'Mazda', 'model' => 'CX-5', 'year' => 2021, 'mileage' => 15000]);

        // Relation manager tabs lazy-load their content, so the parent Edit page
        // only exposes the child component's mount payload, not its inner markup —
        // assert the tab is wired up rather than grepping for its (lazy) HTML.
        Livewire::actingAs($user)
            ->test(EditVehicle::class, ['record' => $vehicle->getRouteKey()])
            ->assertOk()
            ->assertSeeHtml('oil-changes-relation-manager');
    }

    public function test_oil_changes_relation_manager_lists_and_creates_records(): void
    {
        $user = User::factory()->create();
        $vehicle = Vehicle::create(['brand' => 'Mazda', 'model' => 'CX-5', 'year' => 2021, 'mileage' => 15000]);
        $existing = $vehicle->oilChanges()->create(['odometer' => 12000, 'date' => now()->subDays(30)]);

        $manager = Livewire::actingAs($user)->test(OilChangesRelationManager::class, [
            'ownerRecord' => $vehicle,
            'pageClass' => EditVehicle::class,
        ]);

        $manager->assertOk()->assertCanSeeTableRecords([$existing]);

        $manager->mountTableAction('create')
            ->setTableActionData(['date' => now()->format('Y-m-d'), 'odometer' => 15500])
            ->callMountedTableAction();

        $this->assertDatabaseHas('oil_checks', [
            'vehicle_id' => $vehicle->id,
            'odometer' => 15500,
        ]);
    }

    public function test_import_csv_button_dispatches_the_existing_import_job(): void
    {
        Bus::fake();
        Storage::fake('local');

        $user = User::factory()->create();
        $csv = UploadedFile::fake()->createWithContent(
            'vehicles.csv',
            "brand,model,year,mileage\nToyota,Yaris,2019,30000\n"
        );

        Livewire::actingAs($user)
            ->test(ListVehicles::class)
            ->mountAction('importCsv')
            ->setActionData(['csv' => $csv])
            ->callMountedAction()
            ->assertHasNoActionErrors();

        Bus::assertDispatched(ImportVehiclesFromCsv::class, function (ImportVehiclesFromCsv $job) {
            return Storage::disk('local')->exists($job->path)
                && str_starts_with($job->path, 'imports/');
        });
    }
}
