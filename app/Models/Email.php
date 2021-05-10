<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    use HasFactory;

    protected $dateFormat = 'Y-m-d H:i:sO';
    protected $fillable = [
        'entity_id',
        'address',
        'is_main_email_address'
    ];

    protected $attributes = [
        'is_main_email_address' => false,
    ];

    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

    public function person()
    {
        return $this->hasOneThrough(
            Person::class,
            Entity::class,
            'id',
            'id',
            'entity_id',
            'entity_id'
        );
    }
}
