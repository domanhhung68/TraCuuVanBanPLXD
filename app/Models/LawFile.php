<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LawFile extends Model
{
    protected $table = 'law_files';

    protected $fillable = [
        'law_id',
        'original_name',
        'stored_name',
        'file_path',
        'file_type',
        'file_size',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    public function law(): BelongsTo
    {
        return $this->belongsTo(Law::class);
    }
}
