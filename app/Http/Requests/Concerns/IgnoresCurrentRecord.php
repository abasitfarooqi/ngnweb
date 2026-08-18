<?php

namespace App\Http\Requests\Concerns;

trait IgnoresCurrentRecord
{
    protected function uniqueIgnoreId(): mixed
    {
        foreach (['id', 'customer', 'club_member', 'clubMember'] as $key) {
            $value = $this->route($key);
            if ($value === null || $value === '') {
                continue;
            }

            return is_object($value) && method_exists($value, 'getKey')
                ? $value->getKey()
                : $value;
        }

        return $this->id ?? $this->input('id');
    }
}
