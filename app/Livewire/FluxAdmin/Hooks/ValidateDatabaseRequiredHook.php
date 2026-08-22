<?php

namespace App\Livewire\FluxAdmin\Hooks;

use App\Support\FluxAdminSchemaRules;
use Livewire\ComponentHook;

class ValidateDatabaseRequiredHook extends ComponentHook
{
    public function call($method, $params, $returnEarly, $metadata, $componentContext)
    {
        if ($method !== 'save') {
            return;
        }

        if (! str_contains($this->component::class, 'Livewire\\FluxAdmin\\')) {
            return;
        }

        $modelClass = FluxAdminSchemaRules::modelClassFor($this->component);
        if ($modelClass === null) {
            return;
        }

        $bagName = null;
        if (property_exists($this->component, 'form') && is_array($this->component->form)) {
            $bagName = 'form';
        } elseif (property_exists($this->component, 'formData') && is_array($this->component->formData)) {
            $bagName = 'formData';
        }

        if ($bagName === null) {
            return;
        }

        $rules = FluxAdminSchemaRules::rulesForBag($modelClass, $this->component->{$bagName}, $bagName);
        if ($rules === []) {
            return;
        }

        validator(
            [$bagName => $this->component->{$bagName}],
            $rules,
            FluxAdminSchemaRules::messages($rules)
        )->validate();
    }
}
