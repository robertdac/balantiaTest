<?php

namespace App\Jobs;

use App\Models\CsvUpload;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class InsertCsvErrorsSummaryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $uploadId;

    public function __construct(CsvUpload $CsvUpload)
    {
        $this->uploadId = $CsvUpload->id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DB::table('csv_errors_summary')->insertUsing(
            ['csv_uploads_id', 'csv_id', 'start_date', 'end_date', 'total_errors'],
            DB::table('csv_records')
                ->select(
                    DB::raw("{$this->uploadId} as csv_uploads_id"),
                    'csv_id',
                    DB::raw('MIN(fecha_desde) as start_date'),
                    DB::raw('MAX(fecha_hasta) as end_date'),
                    DB::raw('COUNT(*) as total_errors')
                )
                ->where('csv_uploads_id', $this->uploadId)
                ->whereColumn('fecha_desde', '>', 'fecha_hasta')
                ->groupBy('csv_id')
        );
    }
}
