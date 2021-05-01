<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    use HasFactory;

    protected $fillable = [
        'entity_id',
        'address',
        'isMainEmailAddress'
    ];

    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

}
