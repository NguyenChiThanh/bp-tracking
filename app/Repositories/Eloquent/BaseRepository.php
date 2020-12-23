<?php

namespace App\Repositories\Eloquent;

use DB;
use Exception;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection;

abstract class BaseRepository implements RepositoryInterface
{
    public $tableName;

    /**
     * @return EloquentBuilder
     */
    abstract public function getQueryEloquent(): EloquentBuilder;

    /**
     * @return QueryBuilder
     */
    public function getQueryBuilder(): QueryBuilder
    {
        return DB::table($this->tableName);
    }

    /**
     * @param $id
     * @param array $columns
     * @return Model | null
     */
    public function find($id, $columns = ['*']): Model
    {
        return $this->getQueryEloquent()->find($id, $columns);
    }

    /**
     * @param array $ids
     * @param array $columns
     * @return Collection
     */
    public function findMany($ids, $columns = ['*'])
    {
        return $this->getQueryEloquent()->findMany($ids, $columns);
    }

    /**
     * @param $id
     * @param array $columns
     * @return Model if found
     * @throws Exception if not found
     */
    public function findOrFail($id, $columns = ['*']): Model
    {
        return $this->getQueryEloquent()->findOrFail($id, $columns);
    }

    /**
     * Delete model.
     *
     * @param Model $model
     * @return bool
     * @throws Exception
     */
    public function delete(Model $model): bool
    {
        return $model->delete();
    }

    /**
     * Delete model by id.
     *
     * @param int $id
     * @return bool
     * @throws Exception
     */
    public function deleteById($id)
    {
        $model = $this->findOrFail($id);

        return $model->delete();
    }
}
