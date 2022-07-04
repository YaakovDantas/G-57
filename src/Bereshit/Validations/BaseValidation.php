<?php

namespace Bereshit\Validations;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;

class BaseValidation
{
    public function passes($request, $data)
    {

        if (isset($request) && !empty($request)) {
            $classRequest = new $request();
            $messages = $classRequest->messages() ?? [];

            if (count($messages)) {
                $validator = Validator::make(
                    $data,
                    $classRequest->rules(),
                    $messages
                );
            } else {
                $validator = Validator::make($data, $classRequest->rules());
            }

            if ($validator->fails()) {
                dd($validator->messages()->messages());
            }
        }

        return true;
    }

    public function exists($model, $messages)
    {
        if (!$model) {
            throw new \Exception($messages);
        }

        return true;
    }
}