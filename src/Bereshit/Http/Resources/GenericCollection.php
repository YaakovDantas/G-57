<?php

namespace Bereshit\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class GenericCollection extends ResourceCollection
{
    protected $classResource;

    public function setResource($classResource)
    {
        $this->classResource = $classResource;
        return $this;
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($item) {
            return new $this->classResource($item);
        });
        return $collect;
    }
}