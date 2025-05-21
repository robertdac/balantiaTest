<?php

    namespace App\Http\Requests;

    use Illuminate\Foundation\Http\FormRequest;

    class CsvUploadRequest extends FormRequest
    {
        protected $expectedHeader = ['id', 'zona', 'fecha_desde', 'fecha_hasta'];

        public function authorize()
        {
            return true;
        }

        public function rules()
        {
            return [
                'file' => 'required|file|mimes:csv,txt',
            ];
        }

        public function withValidator($validator)
        {
            $validator->after(function($validator) {
                if ($this->hasFile('file')) {
                    $file = $this->file('file');

                    if (!$this->validateCsvContent($file)) {
                        $validator->errors()->add('file', 'CSV must use ";" as separator and have header: ' . implode(';', $this->expectedHeader));
                    }
                }
            });
        }

        protected function validateCsvContent($file): bool
        {
            $lines = file($file->getRealPath(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            if (!$lines) {
                return false;
            }

            $header = str_getcsv(array_shift($lines), ';');
            if ($header !== $this->expectedHeader) {
                return false;
            }

            foreach ($lines as $line) {
                $row = str_getcsv($line, ';');
                if (count($row) !== count($this->expectedHeader)) {
                    return false;
                }
            }

            return true;
        }
    }
