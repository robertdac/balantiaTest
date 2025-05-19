<?php

    namespace App\Jobs;

    use App\Models\CsvUpload;
    use http\Exception\InvalidArgumentException;
    use Illuminate\Bus\Queueable;
    use Illuminate\Contracts\Queue\ShouldBeUnique;
    use Illuminate\Contracts\Queue\ShouldQueue;
    use Illuminate\Foundation\Bus\Dispatchable;
    use Illuminate\Queue\InteractsWithQueue;
    use Illuminate\Queue\SerializesModels;

    class UpdateCsvUploadStatusJob implements ShouldQueue
    {
        use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

        protected $status_id;
        protected $csvUpload;

        /**
         * Create a new job instance.
         *
         * @return void
         */
        public function __construct(CsvUpload $csvUpload, int $status_id)
        {
            $this->csvUpload = $csvUpload;
            $this->status_id = $status_id;
        }

        /**
         * Execute the job.
         *
         * @return void
         */
        public function handle()
        {
            $this->csvUpload->update(['status_id' => $this->status_id]);
        }
    }
