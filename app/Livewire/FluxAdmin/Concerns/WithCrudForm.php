<?php

namespace App\Livewire\FluxAdmin\Concerns;

use App\Support\FluxAdminFormPayload;
use Illuminate\Database\Eloquent\Model;

/**
 * Manages a generic form-data array so create/update pages don't need bespoke
 * wiring for every field. Implementers provide model class, validation rules,
 * and optional hooks. Validation messages use British English spellings.
 */
trait WithCrudForm
{
    /** @var array<string, mixed> */
    public array $formData = [];

    public ?int $recordId = null;

    public function fillFromModel(Model $model): void
    {
        $this->recordId = $model->getKey();
        $this->formData = array_merge($this->formData, FluxAdminFormPayload::formAttributesFromModel($model));
    }

    /**
     * Subclass must return Model class name.
     *
     * @return class-string<Model>
     */
    abstract protected function formModel(): string;

    /**
     * @return array<string, mixed>
     */
    abstract protected function formRules(): array;

    /**
     * @return array<string, string>
     */
    protected function formMessages(): array
    {
        return [];
    }

    protected function beforeSave(array $attributes): array
    {
        return $attributes;
    }

    protected function afterSave(Model $model): void
    {
        //
    }

    public function save(): Model
    {
        $validator = validator(
            ['formData' => $this->formData],
            ['formData' => 'array'] + $this->ruleKeysOf($this->formRules()),
            $this->formMessages(),
        );

        $validator->validate();

        $modelClass = $this->formModel();
        /** @var Model $model */
        $model = $this->recordId
            ? $modelClass::query()->findOrFail($this->recordId)
            : new $modelClass;

        $attributes = $this->beforeSave($this->filterFormDataToModel($model));
        $model->fill($attributes)->save();

        $this->recordId = $model->getKey();
        $this->afterSave($model);

        return $model;
    }

    /**
     * Only return attributes that exist on the model's fillable list to avoid MassAssignmentException.
     *
     * @return array<string, mixed>
     */
    protected function filterFormDataToModel(Model $model): array
    {
        return FluxAdminFormPayload::onlyPersistable($model, $this->formData);
    }

    /**
     * Rewrite rule keys to `formData.*` so validator targets nested array.
     * Guards against rules that were already written with the `formData.` prefix.
     *
     * @param  array<string, mixed>  $rules
     * @return array<string, mixed>
     */
    protected function ruleKeysOf(array $rules): array
    {
        $result = [];
        foreach ($rules as $key => $value) {
            $normalised = str_starts_with($key, 'formData.') ? $key : 'formData.' . $key;
            $result[$normalised] = $value;
        }

        return $result;
    }
}
