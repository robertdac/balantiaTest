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

    class InsertCsvDateGapsSummaryJob implements ShouldQueue
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
            DB::table('csv_dates_range_gaps_summary')->insertUsing(
                ['csv_uploads_id', 'csv_id', 'start_date', 'end_date', 'total_errors'],
                DB::table(DB::raw("
        (
            SELECT
                {$this->uploadId} AS csv_uploads_id,
                csv_id,
                MIN(fecha_desde) AS start_date,
                MAX(fecha_hasta) AS end_date,
                COUNT(*) AS total_errors
            FROM (
                SELECT
                    csv_id,
                    fecha_desde,
                    fecha_hasta,
                    LAG(fecha_hasta) OVER (PARTITION BY csv_id ORDER BY fecha_desde) AS prev_date
                FROM csv_records
                WHERE csv_uploads_id = {$this->uploadId}
            ) AS registers
            WHERE prev_date IS NOT NULL
              AND fecha_desde > DATE_ADD(prev_date, INTERVAL 1 DAY)
            GROUP BY csv_id
        ) AS resumen
    "))
            );
        }
    }
