<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-1">
                <a href="{{ route('flux-admin.survey-questions.index') }}" class="hover:text-zinc-700 dark:hover:text-zinc-200 transition">Survey Questions</a>
                <span>/</span>
                <span>{{ $surveyQuestion ? 'Edit' : 'New question' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $surveyQuestion ? 'Edit question #'.$surveyQuestion->id : 'New survey question' }}</h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flux-admin.survey-questions.index') }}">
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save question</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-5" novalidate>
        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Question details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-flux-admin::field-group label="Survey" required :error="$errors->first('form.survey_id')">
                    <flux:select wire:model="form.survey_id" placeholder="Select survey">
                        <flux:select.option value="">Select…</flux:select.option>
                        @foreach($surveys as $s)
                            <flux:select.option value="{{ $s->id }}">{{ $s->title }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Question type" required :error="$errors->first('form.question_type')">
                    <flux:select wire:model="form.question_type">
                        <flux:select.option value="text">Text</flux:select.option>
                        <flux:select.option value="radio">Radio</flux:select.option>
                        <flux:select.option value="checkbox">Checkbox</flux:select.option>
                        <flux:select.option value="select">Select</flux:select.option>
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Order" :error="$errors->first('form.order')">
                    <flux:input type="number" wire:model="form.order" min="0" />
                </x-flux-admin::field-group>
            </div>
            <div class="mt-4">
                <x-flux-admin::field-group label="Question text" required :error="$errors->first('form.question_text')">
                    <flux:textarea wire:model="form.question_text" rows="3" />
                </x-flux-admin::field-group>
            </div>
            <div class="mt-4">
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model="form.is_required" class="accent-zinc-900 dark:accent-zinc-200"> Required
                </label>
            </div>
        </div>
        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('flux-admin.survey-questions.index') }}">
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">Save question</flux:button>
        </div>
    </form>
</div>
