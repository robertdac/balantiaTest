<?php

    namespace App\Console\Commands;

    use Carbon\Carbon;
    use Illuminate\Console\Command;

    class GenerateCsv extends Command
    {
        /**
         * The name and signature of the console command.
         *
         * @var string
         */
        protected $signature = 'generate:csv {records=10000000}';

        /**
         * The console command description.
         *
         * @var string
         */
        protected $description = 'Generates a CSV with duplicate records, errors, and date gaps';

        protected  $letters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];

        /**
         * Create a new command instance.
         *
         * @return void
         */
        public function __construct()
        {
            parent::__construct();
        }

        public function handle()
        {
            $records = (int)$this->argument('records');
            $filename = storage_path('app/datos_' . $records . '.csv');

            $this->info("Generating CSV with $records records in $filename");

            $handle = fopen($filename, 'w');

            fputcsv($handle, ['id', 'zona', 'fecha_desde', 'fecha_hasta'], ';');

            $maxId = intval($records / 10);

            $startBase = Carbon::create(2018, 1, 1);
            $endBase = Carbon::create(2021, 12, 31);

            for ($i = 0; $i < $records; $i++) {
                $id = rand(90, 90 + $maxId);
                $zona = $this->letters[array_rand($this->letters)];

                $fechaDesde = $this->randomDate($startBase, $endBase);

                if (rand(1, 100) <= 5) {
                    $fechaHasta = $fechaDesde->copy()->subDays(rand(1, 30));
                } else {
                    $fechaHasta = $fechaDesde->copy()->addDays(rand(1, 60));
                }

                fputcsv($handle, [
                    $id,
                    $zona,
                    $fechaDesde->format('Y-m-d'),
                    $fechaHasta->format('Y-m-d'),
                ], ';');


                if ($i > 0 && $i % 100000 === 0) {
                    $this->info("Generated $i records...");
                }
            }

            fclose($handle);

            $this->info("File generated successfully in: $filename");
            return 0;
        }


     protected function randomDate($start, $end)
    {
        $diff = $end->diffInDays($start);
        $randomDays = rand(0, $diff);
        return $start->copy()->addDays($randomDays);
    }
    }
