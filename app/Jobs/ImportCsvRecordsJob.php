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
    use Illuminate\Support\Facades\Log;
    use PDO;

    class ImportCsvRecordsJob implements ShouldQueue
    {
        use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

        protected $file;
        public $timeout = 600;
        public $tries = 3;

        /**
         * Create a new job instance.
         *
         * @return void
         */
        public function __construct(Collection $file)
        {
            $this->file = $file;
        }

        /**
         * Execute the job.
         *
         * @return void
         */
        public function handle(): void
        {
            $tempTable = 'csv_records_tmp_' . uniqid();
            $this->createdTableTemp($tempTable);
            $this->loadDataIntoTempTable($tempTable);
            $this->transferDataToMainTable($tempTable);

        }


        public function failed(\Throwable $exception): void
        {
            $this->csvUpload->update(['status' => CsvUpload::STATUS['failed']]);
            Log::error('ImportCsvRecordsJob', [$exception->getMessage()]);
        }


        /**
         * Create a temporary table without indexes.
         *
         * @param string $tempTable The name of the temporary table to create.
         * @return void
         */
        protected function createdTableTemp(string $tempTable): void
        {
            DB::unprepared("
            CREATE TEMPORARY TABLE {$tempTable} (
                csv_id INT,
                zona VARCHAR(255),
                fecha_desde DATE,
                fecha_hasta DATE,
                csv_uploads_id INT,
                created_at DATETIME,
                updated_at DATETIME
            )
        ");
        }

        /**
         * Transfer data from the temporary table to the main table with indexes.
         *
         * @param string $tempTable The name of the temporary table.
         * @return void
         */
        protected function transferDataToMainTable(string $tempTable): void
        {
            DB::unprepared("
        INSERT INTO csv_records (csv_id, zona, fecha_desde, fecha_hasta, csv_uploads_id, created_at, updated_at)
        SELECT csv_id, zona, fecha_desde, fecha_hasta, csv_uploads_id, created_at, updated_at
        FROM {$tempTable}
    ");
        }

        /**
         *  Load CSV data quickly into the temporary table.
         *
         * @param string $tempTable
         * @return void
         */
        protected function loadDataIntoTempTable(string $tempTable): void
        {

            $this->file->each(function($item) use ($tempTable) {

                //
                $csvUploadId = (int)$item->fileable_id;
                $csvPath = addslashes(storage_path('app/' . $item->path));
                $loadSql = "
        LOAD DATA LOCAL INFILE '{$csvPath}'
        INTO TABLE {$tempTable}
        FIELDS TERMINATED BY ';'
        ENCLOSED BY '\"'
        LINES TERMINATED BY '\n'
        IGNORE 1 ROWS
        (csv_id, zona, fecha_desde, fecha_hasta)
        SET
            csv_uploads_id = {$csvUploadId},
            created_at = NOW(),
            updated_at = NOW()
    ";
                DB::unprepared($loadSql);


            });



        }
    }
