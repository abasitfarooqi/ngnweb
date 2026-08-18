<?php

namespace App\Livewire\FluxAdmin\Concerns;

use App\Support\FluxAdminUniqueViolation;

trait CatchesUniqueConstraintViolations
{
    public function exceptionCatchesUniqueConstraintViolations($e, $stopPropagation): void
    {
        if (! FluxAdminUniqueViolation::matches($e)) {
            return;
        }

        FluxAdminUniqueViolation::applyToComponent($this, $e);
        $stopPropagation();
    }
}
