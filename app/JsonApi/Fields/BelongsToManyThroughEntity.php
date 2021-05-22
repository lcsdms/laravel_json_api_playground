<?php


namespace App\JsonApi\Fields;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany as EloquentBelongsToMany;
use LaravelJsonApi\Eloquent\Contracts\FillableToMany;
use LaravelJsonApi\Eloquent\Fields\Relations\BelongsToMany;

class BelongsToManyThroughEntity extends BelongsToMany implements FillableToMany
{
    public static function make(string $fieldName, string $relation = null): BelongsToManyThroughEntity
    {
        return new self($fieldName,$relation);
    }

    public function sync(Model $model, array $identifiers): iterable
    {
        return parent::sync($model->entity, $identifiers);
    }

    public function detach(Model $model, array $identifiers): iterable
    {
        return parent::detach($model->entity, $identifiers);
    }

}
