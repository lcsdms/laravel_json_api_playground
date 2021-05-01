<?php

namespace App\Observers;

use App\Models\Company;
use App\Models\Entity;

class CompanyObserver
{
    public function created(Company $company)
    {
        $entity = new Entity();
        $company->entity()->save($entity);
    }
}
