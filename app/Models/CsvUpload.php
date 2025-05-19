<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CsvUpload extends Model
{
    use HasFactory;

    const STATUS = [
        'pending' => 1,
        'processing' => 2,
        'completed' => 3,
        'failed' => 4,
    ];

    protected $fillable = [
        'path',
        'status_id'
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
}
