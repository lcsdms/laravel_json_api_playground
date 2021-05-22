<?php


namespace App\Models\Traits;


use App\Models\Entity;

trait EntityTrait
{

    public function emails()
    {
        return $this->hasManyDeepFromRelations(
            $this->entity(),
            (new Entity)->emails()
        );
    }

    public function relationships()
    {
        return $this->hasManyDeepFromRelations(
            $this->entity(),
            (new Entity)->relationships()
        );
    }
}
