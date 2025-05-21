<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class File extends Model
    {
        use HasFactory;

        protected $fillable = [
            'filename',
            'path',
            'size',
            'mime_type',
            'fileable_id',
            'fileable_type',
        ];

        protected $casts = [
            'size' => 'integer',
        ];


        public function fileable()
        {
            return $this->morphTo();
        }


    }
