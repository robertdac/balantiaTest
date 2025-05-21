<?php

    namespace App\Listeners;

    use App\Events\CsvUploadStatusChanged;
    use App\Jobs\ImportCsvRecordsJob;
    use App\Jobs\InsertCsvDateGapsSummaryJob;
    use App\Jobs\InsertCsvDuplicatesSummaryJob;
    use App\Jobs\InsertCsvErrorsSummaryJob;
    use App\Jobs\UpdateCsvUploadStatusJob;
    use Illuminate\Contracts\Queue\ShouldQueue;
    use Illuminate\Queue\InteractsWithQueue;
    use Illuminate\Support\Facades\Bus;
    use Illuminate\Support\Facades\Log;

    class HandleCsvUploadStatusChange
    {
        public function handle(CsvUploadStatusChanged $event)
        {
            $status = $event->newStatus;

            if ($status === $event->csvUpload::STATUS['files_stored']) {

                $csvUpload = $event->csvUpload;

                $firstFileId = $csvUpload->files()->orderBy('id')->first()->id;

                $jobs = [];
                $csvUpload->files()
                    ->where('id', '>', $firstFileId)
                    ->chunk(100, function($chunk) use (&$jobs) {
                        $jobs[] = new ImportCsvRecordsJob($chunk);
                    });

                $jobs[] = new InsertCsvDuplicatesSummaryJob($csvUpload);
                $jobs[] = new InsertCsvErrorsSummaryJob($csvUpload);
                $jobs[] = new InsertCsvDateGapsSummaryJob($csvUpload);
                $jobs[] = new UpdateCsvUploadStatusJob($csvUpload, $csvUpload::STATUS['completed']);
                Bus::chain($jobs)->catch(function(\Throwable $e) use ($csvUpload) {
                    Log::error('A job chain failed for HandleCsvUploadStatusChange ' . $csvUpload->id, [
                        'message' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);

                    $csvUpload->update(['status_id' => $csvUpload::STATUS['failed']]);
                })->dispatch();


            }
        }
    }
