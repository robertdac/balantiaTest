<?php

namespace App\Jobs;

use App\Models\CsvUpload;
use App\Services\SplitCsvService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class SplitCsvJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

      protected $csvUpload;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(CsvUpload $csvUpload)
    {
        $this->csvUpload = $csvUpload;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */
    public function handle(SplitCsvService $splitCsvService)
    {
        $file = $this->csvUpload->files()->first();

        $paths = $splitCsvService->split($file->path,1000)->getAllpaths();
        $StorePath = storage_path('app/');


        $data = [];
        foreach ($paths as $path) {
            $data[] = [
                'filename' => File::basename($StorePath . $path),
                'path' => $path,
                'size' => File::size($StorePath . $path),
                'mime_type' => File::mimeType($StorePath . $path),
            ];
        }

        $this->csvUpload->files()->createMany($data);
        $this->csvUpload->update(['status_id' => CsvUpload::STATUS['files_stored']]);

    }


    /***
     * Handle a job failure.
     *
     * @param \Throwable $exception
     * @return void
     */
    public function failed(\Throwable $exception)
    {
        Log::error('SplitCsvJob failed: ' . $exception->getMessage(), [
            'csv_upload_id' => $this->csvUpload->id,
            'exception' => $exception,
        ]);

        $this->csvUpload->update(['status_id' => CsvUpload::STATUS['failed']]);
    }
}
