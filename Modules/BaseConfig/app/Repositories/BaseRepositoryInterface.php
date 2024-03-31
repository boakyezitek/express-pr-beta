<?php

namespace Modules\BaseConfig\Repositories;

use Illuminate\Database\Eloquent\Model;

/**
 * Interface BaseRepositoryInterface
 *
 * @package Modules\BaseConfig\Repositories
 */

interface BaseRepositoryInterface
{
    public function all();

    public function find($id);

    public function create(array $data);

    public function update(Model $model, array $data);

    public function delete(Model $model);
}
