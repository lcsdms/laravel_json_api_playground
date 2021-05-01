<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    use HasFactory;

    protected $dateFormat = 'Y-m-d H:i:sO';

    protected $fillable = [
        'name',
        'social_name',
        'birth_date',
        'document_number'
    ];

    protected $casts = [
        'birth_date' => 'date',
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
