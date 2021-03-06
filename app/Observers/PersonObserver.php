<?php

namespace App\Observers;

use App\Models\Entity;
use App\Models\Person;

class PersonObserver
{
    public function created(Person $person)
    {
        $entity = new Entity();
        $person->entity()->save($entity);
    }
}
