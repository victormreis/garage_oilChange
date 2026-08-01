<?php

namespace App\Filament\Resources\VehicleResource\Pages;

use App\Filament\Resources\VehicleResource;
use App\Jobs\ImportVehiclesFromCsv;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListVehicles extends ListRecords
{
    protected static string $resource = VehicleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('importCsv')
                ->label('Import CSV')
                ->icon('heroicon-o-arrow-up-tray')
                ->form([
                    FileUpload::make('csv')
                        ->label('CSV file')
                        ->disk('local')
                        ->directory('imports')
                        ->acceptedFileTypes(['text/csv', 'text/plain'])
                        ->required(),
                ])
                ->action(function (array $data) {
                    ImportVehiclesFromCsv::dispatch($data['csv']);

                    Notification::make()
                        ->title('Import started')
                        ->body('Vehicles will appear once processing finishes.')
                        ->success()
                        ->send();
                }),
        ];
    }
}
