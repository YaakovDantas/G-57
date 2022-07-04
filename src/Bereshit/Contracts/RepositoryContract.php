<?php

namespace Bereshit\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface RepositoryContract
{
    /**
     * @param Builder $query
     * @param array $filters
     * @param mixed $filters
     */
    public function makeQuery(Builder $query, $filters = []);

    /**
     * @param mixed $data
     */
    public function store($data);

    /**
     * @param mixed $data
     * @param int $id
     */
    public function update($data, $id);

    /**
     * @param int $id
     * @param array $with
     */
    public function find($id, array $with = []);

    /**
     * @param int $id
     */
    public function destroy($id);
}