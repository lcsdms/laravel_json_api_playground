<?php

namespace App\Models;

use App\Models\Traits\EntityTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

class Person extends Model
{
    use HasFactory;
    use HasRelationships;
    use EntityTrait;

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


}
