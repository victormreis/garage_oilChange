<?php

namespace App\Jobs;

use App\Models\Vehicle;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ImportVehiclesFromCsv implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private const CHUNK_SIZE =500;
    /**
     * Create a new job instance.
     */
    public function __construct(public string $path)
    {}


    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $fullPath = Storage::path($this->path);
        $handle = fopen($fullPath, 'r');

        $header = fgetcsv($handle);

        $chunk = [];
        $totalProcessed = 0;
        $totalFailed = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) !== count($header)) {
                $totalFailed++;
                continue;
            }
            $data = array_combine($header, $row);

            if (! $this->isValidRow($data)) {
                $totalFailed++;
                continue;
            }

            $chunk[] = [
                'brand' => $data['brand'],
                'model' => $data['model'],
                'year' => (int) $data['year'],
                'mileage' => (int) $data['mileage'],
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($chunk) >= self::CHUNK_SIZE) {
                $this->processChunk($chunk, $totalProcessed);
                $totalProcessed += count($chunk);
                $chunk = [];
            }
        }

        if (! empty($chunk)) {
            $this->processChunk($chunk, $totalProcessed);
            $totalProcessed += count($chunk);
        }

        fclose($handle);

        Log::info('CSV import finished', [
            'path' => $this->path,
            'total_processed' => $totalProcessed,
            'total_failed' => $totalFailed,
        ]);
    }

    private function isValidRow(array $data): bool
    {
        return ! empty($data['brand'])
            && ! empty($data['model'])
            && is_numeric($data['year'] ?? null)
            && is_numeric($data['mileage'] ?? null);
    }

    private function processChunk(array $chunk, int $totalProcessedSoFar): void
    {
        Vehicle::insert($chunk);

        Log::info('CSV import chunk processed', [
            'chunk_size' => count($chunk),
            'total_so_far' => $totalProcessedSoFar + count($chunk),
        ]);
    }
}
