<?php


namespace App\JsonApi\Fields;


use App\Models\Entity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use LaravelJsonApi\Eloquent\Contracts\FillableToOne;
use LaravelJsonApi\Eloquent\Fields\Concerns\ReadOnly;
use LaravelJsonApi\Eloquent\Fields\Relations\HasOneThrough;
use LaravelJsonApi\Spec\Values\ToOne;

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
        //todo refatorar para usar uma trait aqui
        $entity = \App\Models\Entity::select(['id'])
            ->where('entity_type', 'PERSON')
            ->where('entity_id', 1)
            ->firstOrFail();
        $model->entity_id = $entity->id;
        //todo é necessário implementar aqui a lógica de detach do hasOne para deletar
    }

    public function associate(Model $model, ?array $identifier): ?Model
    {
        throw new \Exception("Method not implemented");
    }

}
