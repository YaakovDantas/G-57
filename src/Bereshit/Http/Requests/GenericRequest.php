<?php

namespace Bereshit\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenericRequest extends FormRequest
{
    public function messages()
    {
        return [];
    }

    public function generics()
    {
        return NULL;
    }

    public function especifics()
    {
        return [];
    }
}