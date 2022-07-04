<?php

namespace Bereshit\Contracts;

interface ServiceContract
{
    /**
     * @param int $id
     */
    public function findObjectInRepository($id);

    /**
     * @param array $filters
     */
    public function all($filters = []);
}