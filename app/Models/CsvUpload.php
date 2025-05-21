<?php

    namespace App\Models;

    use App\Events\CsvUploadStatusChanged;
    use App\Events\FileCreated;
    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\HasMany;
    use Illuminate\Database\Eloquent\Relations\MorphMany;

    class CsvUpload extends Model
    {
        use HasFactory;

        const STATUS = [
            'pending' => 1,
            'processing' => 2,
            'files_stored' => 3,
            'completed' => 4,
            'failed' => 5,
        ];

        protected $fillable = [
            'path',
            'status_id'
        ];

        protected $dispatchesEvents = [
            'updated' => CsvUploadStatusChanged::class,
        ];

        public function getStatusNameAttribute()
        {
            $statusNames = array_flip(self::STATUS);

            return $statusNames[$this->status_id] ?? 'Unknown';
        }


    public function records(): \Illuminate\Database\Eloquent\Relations\HasMany
        {
            return $this->hasMany(CsvRecord::class, 'csv_uploads_id');
        }

        public function files(): MorphMany
        {
            return $this->morphMany(File::class, 'fileable');
        }

    }
