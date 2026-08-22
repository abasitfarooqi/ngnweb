<?php

namespace App\Livewire\FluxAdmin\Concerns;

use App\Support\FluxAdminRequiredColumn;
use App\Support\FluxAdminUniqueViolation;
use App\Support\UploadLimit;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Livewire\Features\SupportFileUploads\FileNotPreviewableException;

trait CatchesUniqueConstraintViolations
{
    public function exceptionCatchesUniqueConstraintViolations($e, $stopPropagation): void
    {
        if (FluxAdminUniqueViolation::matches($e)) {
            FluxAdminUniqueViolation::applyToComponent($this, $e);
            $stopPropagation();

            return;
        }

        if (FluxAdminRequiredColumn::matches($e)) {
            FluxAdminRequiredColumn::applyToComponent($this, $e);
            $stopPropagation();

            return;
        }

        if ($e instanceof PostTooLargeException || str_contains($e->getMessage(), 'POST data is too large')) {
            $message = 'That file is too large for this server. Use a file under '.UploadLimit::label().'.';
            if (method_exists($this, 'addError')) {
                $this->addError('videoFile', $message);
                $this->addError('video', $message);
            }
            if (method_exists($this, 'dispatch')) {
                $this->dispatch('flux-admin:toast', type: 'error', message: $message);
            }
            $stopPropagation();

            return;
        }

        if ($e instanceof FileNotPreviewableException) {
            $message = 'That file type cannot be previewed here. The upload can still be saved.';
            if (method_exists($this, 'addError')) {
                $this->addError('form', $message);
            }
            if (method_exists($this, 'dispatch')) {
                $this->dispatch('flux-admin:toast', type: 'error', message: $message);
            }
            $stopPropagation();
        }
    }
}
