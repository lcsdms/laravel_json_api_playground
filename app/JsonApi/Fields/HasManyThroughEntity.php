<?php


namespace App\JsonApi\Fields;


use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany as EloquentHasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany as EloquentMorphMany;
use Illuminate\Http\Request;
use LaravelJsonApi\Eloquent\Contracts\FillableToMany;
use LaravelJsonApi\Eloquent\Fields\Concerns\ReadOnly;
use LaravelJsonApi\Eloquent\Fields\Relations\HasMany;
use LaravelJsonApi\Eloquent\Fields\Relations\HasManyThrough;
use Neomerx\JsonApi\Exceptions\LogicException;

class HasManyThroughEntity extends HasManyThrough implements FillableToMany
{

    private const KEEP_DETACHED_MODELS = 0;
    private const DELETE_DETACHED_MODELS = 1;
    private const FORCE_DELETE_DETACHED_MODELS = 2;

    /**
     * Flag for how to detach models from the relationship.
     *
     * @var int
     */
    private int $detachMode = self::KEEP_DETACHED_MODELS;

    use ReadOnly;

    public static function make(string $fieldName, string $relation = null): HasManyThrough
    {
        return new self($fieldName, $relation);
    }

    public function fill(Model $model, array $identifiers): void
    {
        throw new \Exception("Method not implemented");
        // TODO: Implement fill() method.
    }

    public function sync(Model $model, array $identifiers): iterable
    {
        throw new \Exception("Method not implemented");
        // TODO: Implement sync() method.
    }

    public function attach(Model $model, array $identifiers): iterable
    {
        throw new \Exception("Method not implemented");
        // TODO: Implement attach() method.
    }

    /**
     * @inheritDoc
     */
    public function detach(Model $model, array $identifiers): iterable
    {
        $models = $this->findMany($identifiers);
        $this->doDetach($model, $models);
        $model->unsetRelation($this->relationName());

        return $models;
        // TODO: Implement detach() method.
        //todo implementar a regra de deletar os registros no detach também, conforme hasMany
    }


    /**
     * Detach models from the relationship.
     *
     * @param Model $model
     * @param EloquentCollection $remove
     */
    private function doDetach(Model $model, EloquentCollection $remove): void
    {
        if (self::KEEP_DETACHED_MODELS === $this->detachMode) {
            $this->setInverseToNull($model, $remove);
            return;
        }

        $this->deleteRelatedModels($remove);
    }

    /**
     * Keep models that are detached by setting the inverse relationship column(s) to `null`.
     *
     * @return $this
     */
    public function keepDetachedModels(): self
    {
        $this->detachMode = self::KEEP_DETACHED_MODELS;

        return $this;
    }

    /**
     * Delete models that are detached using the `Model::delete()` method.
     *
     * @return $this
     */
    public function deleteDetachedModels(): self
    {
        $this->detachMode = self::DELETE_DETACHED_MODELS;

        return $this;
    }

    /**
     * Force delete models that are detached using the `Model::forceDelete()` method.
     *
     * @return $this
     */
    public function forceDeleteDetachedModels(): self
    {
        $this->detachMode = self::FORCE_DELETE_DETACHED_MODELS;

        return $this;
    }

    /**
     * Detach models by setting the inverse relation to `null`.
     *
     * @param Model $model
     * @param EloquentCollection $remove
     */
    private function setInverseToNull(Model $model, EloquentCollection $remove): void
    {
        throw new \Exception('method not tested');
        $relation = $this->getRelation($model);

        /** @var Model $model */
        foreach ($remove as $model) {
            if ($relation instanceof EloquentMorphMany) {
                $model->setAttribute($relation->getMorphType(), null);
            }

            $model->setAttribute($relation->getForeignKeyName(), null)->save();
        }
    }

    /*
    * @param Model $model
    * @return EloquentHasMany|EloquentMorphMany
    */
    private function getRelation(Model $model)
    {
        $relation = $model->entity->{$this->relationName()}();

        if ($relation instanceof EloquentHasMany || $relation instanceof EloquentMorphMany) {
            return $relation;
        }

        throw new LogicException(sprintf(
            'Expecting relation %s on model %s to be a has-many or morph-many relation.',
            $this->relationName(),
            get_class($model)
        ));
    }

    /**
     * Detach models by deleting (or force deleting) the related models.
     *
     * @param EloquentCollection $remove
     */
    private function deleteRelatedModels(EloquentCollection $remove): void
    {
        /** @var Model $model */
        foreach ($remove as $model) {
            if (self::FORCE_DELETE_DETACHED_MODELS === $this->detachMode) {
                $model->forceDelete();
                continue;
            }

            $model->delete();
        }
    }
}
