<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LawRelation extends Model
{
    protected $table = 'law_relations';

    protected $fillable = [
        'from_law_id',
        'to_law_id',
        'relation_type',
        'description',
    ];

    public function fromLaw()
    {
        return $this->belongsTo(Law::class, 'from_law_id');
    }

    public function toLaw()
    {
        return $this->belongsTo(Law::class, 'to_law_id');
    }
}
