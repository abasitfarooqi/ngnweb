<div class="space-y-6">
    <div>
        <flux:heading size="xl">DigitalOcean agent settings</flux:heading>
        <flux:text class="mt-1">Configure the LLM endpoint used by the in-app support agent. Values are stored in <span class="font-mono">system_settings</span> as JSON.</flux:text>
    </div>

    <div class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-6 max-w-3xl">
        <form wire:submit.prevent="save" class="space-y-4">
            <x-flux-admin::field-group label="Endpoint URL" :error="$errors->first('endpoint_url')" required>
                <flux:input type="url" wire:model="endpoint_url" placeholder="https://agent-xxxx.ondigitalocean.app/api/v1/chat/completions" />
            </x-flux-admin::field-group>
            <x-flux-admin::field-group label="Access key" :error="$errors->first('access_key')" required>
                <flux:input type="password" wire:model="access_key" viewable />
            </x-flux-admin::field-group>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <x-flux-admin::field-group label="Max tokens" :error="$errors->first('max_tokens')">
                    <flux:input type="number" wire:model="max_tokens" min="128" max="16384" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Temperature" :error="$errors->first('temperature')">
                    <flux:input type="number" step="0.1" wire:model="temperature" min="0" max="1" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Top-p" :error="$errors->first('top_p')">
                    <flux:input type="number" step="0.05" wire:model="top_p" min="0.1" max="1" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Top-k" :error="$errors->first('top_k')">
                    <flux:input type="number" wire:model="top_k" min="0" max="10" />
                </x-flux-admin::field-group>
            </div>

            <x-flux-admin::field-group label="Retrieval method" :error="$errors->first('retrieval_method')">
                <flux:select wire:model="retrieval_method">
                    <flux:select.option value="none">None</flux:select.option>
                    <flux:select.option value="rewrite">Rewrite</flux:select.option>
                    <flux:select.option value="step_back">Step back</flux:select.option>
                    <flux:select.option value="sub_queries">Sub-queries</flux:select.option>
                </flux:select>
            </x-flux-admin::field-group>

            <div class="flex justify-end gap-2 pt-2 border-t border-zinc-200 dark:border-zinc-800">
                <flux:button type="submit" variant="primary" icon="check" class="!rounded-none">Save settings</flux:button>
            </div>
        </form>
    </div>
</div>
