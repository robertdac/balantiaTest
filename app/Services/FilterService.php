<?php

    namespace App\Services;

    use Illuminate\Support\Facades\DB;

    class FilterService
    {
        protected $filterMap = [
            'duplicates' => 'csv_duplicates_summary',
            'errors' => 'csv_errors_summary',
            'gaps' => 'csv_dates_range_gaps_summary',
        ];

        public function buildQuery(string $filter, int $uploadCsvId): ?\Illuminate\Database\Query\Builder
        {
            if (!array_key_exists($filter, $this->filterMap)) {
                return null;
            }

            return DB::table($this->filterMap[$filter])
                ->where('csv_uploads_id', $uploadCsvId)
                ->orderBy('csv_id');
        }
    }
