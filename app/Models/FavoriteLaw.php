<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FavoriteLaw extends Model
{
    protected $table = 'favorite_laws';

    protected $fillable = [
        'user_id',
        'law_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function law()
    {
        return $this->belongsTo(Law::class);
    }
}
