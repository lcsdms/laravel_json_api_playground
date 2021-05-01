<?php

namespace App\Providers;

use App\Models\Person;
use App\Models\Company;
use App\Observers\CompanyObserver;
use App\Observers\PersonObserver;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        //Observers
        Person::observe(PersonObserver::class);
        Company::observe(CompanyObserver::class);

        Relation::morphMap([
            'ENTITY' => 'App\Models\Entity',
            'PERSON' => 'App\Models\Person',
            'COMPANY' => 'App\Models\Company'
        ]);
    }
}
