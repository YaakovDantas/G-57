<?php

namespace Bereshit\Repositories;

use Bereshit\Contracts\RepositoryContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

abstract class BaseRepository implements RepositoryContract
{
    /**
     * @var \Illuminate\Database\Eloquent\Model $model
     */
    protected $model;

    public function all()
    {
        return $this->model->all();
    }

    /**
     * @param array $data
     * @param int $id
     * @return mixed
     */
    public function update($data, $id)
    {
        $obj = $this->find($id);
        $obj->forceFill($data);
        if ($obj->isDirty()) {
            $obj->save();
        }

        return $this->model->find($id);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function store($data)
    {
        return $this->model->create($data);
    }

    /**
     * @param int $id
     * @param array $with
     * @return mixed
     */
    public function find($id, array $with = [])
    {
        return $this->model->with($with)->find($id);
    }

    /**
     * @param array $where
     * @param array $with
     */
    public function findOneByWhere(array $where = [], array $with = [])
    {
        return $this->model
            ->with($with)
            ->where($where)
            ->first();
    }


    /**
     * @param array $where
     * @param array $with
     */
    public function findAllByWhere(array $where = [], array $with = [])
    {
        return $this->model
            ->with($with)
            ->where($where)
            ->get();
    }

    /**
     * @param int $id
     * @param array $with
     * @return mixed
     */
    public function findOrFail($id, array $with = [])
    {
        return $this->model->with($with)->findOrFail($id);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function destroy($id)
    {
        return $this->find($id)->delete();
    }

    /**
     * @param Builder $query
     * @param array $filters
     * @return Builder
     */
    public function makeQuery(Builder $query, $filters = [])
    {
        return $query;
    }

    /**
     * @param array $fields
     * @return mixed
     */
    public function getAllSelectFields($fields = ["*"])
    {
        return $this->model->select($fields)->get();
    }

    public function setModel(string $model)
    {
        $this->model = app()->make($model);
    }

    public function getModel()
    {
        return $this->model;
    }

    public function beginTransaction()
    {
        DB::beginTransaction();
    }

    public function commit()
    {
        DB::commit();
    }

    public function rollBack()
    {
        DB::rollBack();
    }
}