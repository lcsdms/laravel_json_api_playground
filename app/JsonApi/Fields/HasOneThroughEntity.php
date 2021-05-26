<?php


namespace App\JsonApi\Fields;


use Illuminate\Database\Eloquent\Model;
use LaravelJsonApi\Eloquent\Contracts\FillableToOne;
use LaravelJsonApi\Eloquent\Fields\Concerns\ReadOnly;
use LaravelJsonApi\Eloquent\Fields\Relations\HasOneThrough;

class HasOneThroughEntity extends HasOneThrough implements FillableToOne
{
    use ReadOnly;


    public static function make(string $fieldName, string $relation = null): HasOneThrough
    {
        return new self($fieldName, $relation);
    }


    public function mustExist(): bool
    {
        return false;
    }

    public function fill(Model $model, ?array $identifier): void
    {
        $relatedModel = $this->find($identifier);
        $model->entity_id = $relatedModel->entity->id;
    }

    public function associate(Model $model, ?array $identifier): ?Model
    {
        throw new \Exception("Method not implemented");
    }

}
