<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class EncryptCast implements CastsAttributes
{
    public function get($model, $key, $value, $attributes)
    {
        return $value ? decrypt($value) : null;
    }

    public function set($model, $key, $value, $attributes)
    {
        return $value ? encrypt($value) : null;
    }
}
