<?php

namespace Modules\BaseConfig\Repositories;

use Illuminate\Database\Eloquent\Model;

class BaseRepository implements BaseRepositoryInterface
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(Model $model, array $data)
    {
        $model->update($data);

        return $model;
    }

    public function delete(Model $model)
    {
        return $model->delete();
    }

    /**
     * Get a single staff member with the given ID.
     *
     * @param int $id
     * @return Model
     */

    public function getSingleModel($id) {
        return $this->model->query()
            ->where('id', $id)
            ->first();
    }

    /**
     * Attach related models to the given attribute.
     *
     * @param  Model $model
     * @param  array    $attributes
     * @param  array    $relatedModels
     * @return void
     */

     public function attachRelatedModels(Model $model, array $attributes, $relatedModels) {
        foreach ($relatedModels as $attributeKey => $relationshipName) {
            if (isset($attributes[$attributeKey])) {
                $model->{$relationshipName}()->attach($attributes[$attributeKey]);
            }
        }
    }

        /**
     * Sync related models for the given property.
     *
     * @param  Model $model
     * @param  array    $attributes
     * @param  array    $relatedModels
     * @return void
     */
    public function syncRelatedModels(Model $model, array $attributes, $relatedModels)
    {

        foreach ($relatedModels as $attributeKey => $relationshipName) {
            if (isset($attributes[$attributeKey])) {
                $model->{$relationshipName}()->sync($attributes[$attributeKey]);
            }
        }
    }

    /**
     * Detach related models from the given property.
     *
     * @param  Model $model
     * @param  array    $relatedModels
     * @return void
     */
    public function removeRelatedModels($model, $relatedModels)
    {
        foreach ($relatedModels as $attributeKey => $relationshipName) {
            if ($model->{$relationshipName}) {

                $relatedModels = $model->{$relationshipName};

                $relatedModelIds = $relatedModels->pluck('id')->all();
                $model->{$relationshipName}()->detach($relatedModelIds);
            }
        }
    }
}
