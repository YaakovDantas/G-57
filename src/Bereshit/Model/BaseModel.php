<?php

namespace Bereshit\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Database\Factories\UserFactory;

class BaseModel extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        $model_path = get_called_class();
        $model_name_position = strrpos($model_path, '\\');

        $class_name = substr($model_path, $model_name_position + 1);

        $factory = '\\Database\\Factories\\'.$class_name.'Factory';
        return $factory::new();
    }
}