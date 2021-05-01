<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{

    use HasFactory;
    protected $dateFormat = 'Y-m-d H:i:sO';

    protected $fillable = [
        'name',
        'trade_name',
        'foundation_date',
        'document_number'
    ];

    protected $casts = [
        'foundation_date' => 'date',
    ];

    public function entity()
    {
        return $this->morphOne('App\Models\Entity', 'entity');
    }

    public function emails()
    {
        return $this->hasManyThrough(Email::class, Entity::class, 'entity_id', 'entity_id');
    }

}
