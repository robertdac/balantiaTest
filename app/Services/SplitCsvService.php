<?php

    namespace App\Services;

    use Illuminate\Support\Facades\Storage;
    use SplFileObject;
    use ZipArchive;

    class SplitCsvService
    {
        protected $outputFiles = [];

        /**
         * Splits a CSV file into smaller parts.
         *
         * @param string $path Path to the original file inside storage/app
         * @param int $linesPerFile Number of lines per split file
         * @return self
         * @throws \Exception
         */
        public function split(string $path, int $linesPerFile = 1000000): self
        {
            $fullPath = storage_path('app/' . $path);
            if (!file_exists($fullPath)) {
                throw new \Exception("file not found: $fullPath");
            }

            $csv = new SplFileObject($fullPath);
            $csv->setFlags(SplFileObject::READ_CSV);
            $csv->setCsvControl(',');

            $this->outputFiles = [];
            $lineCount = 0;
            $header = null;
            $outFile = null;

            while (!$csv->eof()) {
                $row = $csv->fgetcsv();
                if ($row === [null]) {
                    continue;
                }

                if ($header === null) {
                    $header = $row;
                    continue;
                }

                if ($lineCount % $linesPerFile === 0) {
                    if ($outFile) {
                        fclose($outFile);
                    }

                    $filename = "split/part_" . uniqid() . ".csv";
                    Storage::makeDirectory('split');
                    $outPath = storage_path("app/$filename");
                    $outFile = fopen($outPath, 'w');
                    fputcsv($outFile, $header);
                    $this->outputFiles[] = $filename;

                }

                fputcsv($outFile, $row);
                $lineCount++;
            }

            if ($outFile) {
                fclose($outFile);
            }

            return $this;
        }

        /**
         * Creates a ZIP file with the previously split files.
         *
         * @param string $zipFilename Name of the ZIP file inside storage/app
         * @return string Relative path of the generated ZIP
         * @throws \Exception
         */
        public function zip(string $zipFilename = 'split/output.zip'): string
        {
            $zipPath = storage_path('app/' . $zipFilename);
            Storage::makeDirectory(dirname($zipFilename));

            $zip = new ZipArchive;

            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw new \Exception("failed to create ZIP file: $zipPath");
            }

            foreach ($this->outputFiles as $file) {
                $fullFilePath = storage_path('app/' . $file);
                if (file_exists($fullFilePath)) {
                    $zip->addFile($fullFilePath, basename($file));
                }
            }

            $zip->close();

            return $zipFilename;
        }

        /**
         * Returns all generated file paths from the split operation.
         *
         * @return array List of relative paths to the split files
         */
        public function getAllPaths(): array
        {
            return $this->outputFiles;
        }


    }
