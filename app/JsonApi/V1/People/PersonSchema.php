<?php

namespace App\JsonApi\V1\People;

use App\JsonApi\Fields\BelongsToManyThroughEntity;
use App\JsonApi\Fields\HasManyThroughEntity;
use App\Models\Person;
use LaravelJsonApi\Eloquent\Contracts\Paginator;
use LaravelJsonApi\Eloquent\Fields\DateTime;
use LaravelJsonApi\Eloquent\Fields\ID;
use LaravelJsonApi\Eloquent\Fields\Str;
use LaravelJsonApi\Eloquent\Filters\Where;
use LaravelJsonApi\Eloquent\Filters\WhereIn;
use LaravelJsonApi\Eloquent\Pagination\PagePagination;
use LaravelJsonApi\Eloquent\Schema;

class PersonSchema extends Schema
{

    /**
     * The model the schema corresponds to.
     *
     * @var string
     */
    public static string $model = Person::class;

    /**
     * Get the resource fields.
     *
     * @return array
     */
    public function fields(): array
    {
        return [
            ID::make(),
            Str::make('name'),
            Str::make('socialName', 'social_name'),
            Str::make('documentNumber', 'document_number'),
            DateTime::make('birthDate','birth_date'),
            DateTime::make('createdAt','created_at')->sortable()->readOnly(),
            DateTime::make('updatedAt','updated_at')->sortable()->readOnly(),
            DateTime::make('deletedAt','deleted_at')->sortable()->readOnly(),
            HasManyThroughEntity::make('emails')->deleteDetachedModels(),
            BelongsToManyThroughEntity::make('relationships')
        ];
    }

    /**
     * Get the resource filters.
     *
     * @return array
     */
    public function filters(): array
    {
        return [
            WhereIn::make('id', $this->idColumn()),
            Where::make('name')
                ->using('ilike')
                ->deserializeUsing(fn ($value) => $value . '%'),
            Where::make('socialName')
                ->using('ilike')
                ->deserializeUsing(fn ($value) => $value . '%')
        ];
    }

    /**
     * Get the resource paginator.
     *
     * @return Paginator|null
     */
    public function pagination(): ?Paginator
    {
        return PagePagination::make();
    }

    public function authorizable(): bool
    {
        return false;
    }


}
