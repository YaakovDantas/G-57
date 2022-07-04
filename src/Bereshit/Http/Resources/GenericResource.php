<?php

namespace Bereshit\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class GenericResource extends JsonResource
{
    public function toArray($request)
    {
        return collect($this->resource)->toArray();
    }

    public function toFormat($updated = false)
    {
        $data = $this->resource;
        return array_merge(GenericResource::toLog($updated), $data);
    }

    public static function toLog($updated = false)
    {
        $log = [
            "usuario_ult_alteracao"   => "",
            "id_pessoa_ult_alteracao" => 0,
        ];

        if ($updated) {
            $log["usuario_ult_alteracao"]  = Auth::user()->email; /* @phpstan-ignore-line */
            $log["id_pessoa_ult_alteracao"] = Auth::user()->id /* @phpstan-ignore-line */;
            $log["data_ult_alteracao"] = date("Y-m-d H:i:s.000");
        } else {
            $log["data_inclusao"]      = date("Y-m-d H:i:s.000");
            $log["id_pessoa_inclusao"] = Auth::user()->id;
            $log["usuario_inclusao"]   = Auth::user()->email; /* @phpstan-ignore-line */
        }

        return $log;
    }
}