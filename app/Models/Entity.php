<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entity extends Model
{
    use HasFactory;

    protected $dateFormat = 'Y-m-d H:i:sO';
    protected $fillable = [
        'entity_type',
        'entity_id',
        'origin_id',
        'status_id',
        'status_reason'
    ];

    public function entity()
    {
        return $this->morphTo()->withTrashed();
    }

    public function person()
    {
        return $this->morphTo()->withTrashed();
    }

    public function addresses()
    {
        return $this->morphMany('App\Models\Address', 'model');
    }

    /**
     * Gets the entity main Address
     */
    public function getMainAddress()
    {
        return $this
            ->addresses()
            ->where('is_main_address', true)
            ->with('repository')
            ->first();
    }

    public function origin()
    {
        return $this->belongsTo(Origin::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function phones()
    {
        return $this->hasMany(Phone::class);
    }

    public function emails()
    {
        return $this->hasMany(Email::class);
    }

    public function relationships()
    {
        return $this->belongsToMany(Relationship::class);
    }

    public function classifications()
    {
        return $this->belongsToMany(Classification::class);
    }
}
