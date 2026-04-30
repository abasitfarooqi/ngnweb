<div>
    <x-flux-admin::data-table title="Careers" description="Job openings published on the careers page.">
        <x-slot:actions>
            <flux:button size="sm" variant="primary" icon="plus" wire:click="openCreate" class="!rounded-none">New job</flux:button>
        </x-slot:actions>
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search title or location…">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.is_active" placeholder="Active">
                        <flux:select.option value="">Any</flux:select.option>
                        <flux:select.option value="1">Active</flux:select.option>
                        <flux:select.option value="0">Inactive</flux:select.option>
                    </flux:select>
                </div>
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.employment_type" placeholder="Employment">
                        <flux:select.option value="">Any</flux:select.option>
                        <flux:select.option value="Full-time">Full-time</flux:select.option>
                        <flux:select.option value="Part-time">Part-time</flux:select.option>
                        <flux:select.option value="Contract">Contract</flux:select.option>
                        <flux:select.option value="Temporary">Temporary</flux:select.option>
                    </flux:select>
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Job title</flux:table.column>
                <flux:table.column>Location</flux:table.column>
                <flux:table.column>Type</flux:table.column>
                <flux:table.column>Salary</flux:table.column>
                <flux:table.column>Posted</flux:table.column>
                <flux:table.column>Expires</flux:table.column>
                <flux:table.column>Active</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="cr-{{ $r->id }}">
                        <flux:table.cell class="text-zinc-900 dark:text-white">{{ $r->job_title }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->location }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->employment_type }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->salary }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $r->job_posted ? \Carbon\Carbon::parse($r->job_posted)->format('d M Y') : '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $r->expire_date ? \Carbon\Carbon::parse($r->expire_date)->format('d M Y') : '—' }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:switch :checked="(bool) $r->is_active" wire:click="toggleActive({{ $r->id }})" />
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-1">
                                <flux:button size="xs" variant="ghost" wire:click="openEdit({{ $r->id }})" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                                <flux:button size="xs" variant="ghost" wire:click="delete({{ $r->id }})" wire:confirm="Delete this job?" icon="trash" class="!rounded-none text-red-600 dark:text-red-400">Delete</flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="8" class="text-center py-8 text-zinc-500 dark:text-zinc-400">No jobs.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-slot:footer>{{ $rows->links() }}</x-slot:footer>
    </x-flux-admin::data-table>

    <flux:modal wire:model.self="showForm" class="md:w-[720px]">
        <form wire:submit.prevent="saveForm" class="space-y-4">
            <flux:heading size="lg">{{ $recordId ? 'Edit job' : 'New job' }}</flux:heading>
            <x-flux-admin::field-group label="Job title" :error="$errors->first('formData.job_title')" required>
                <flux:input wire:model="formData.job_title" />
            </x-flux-admin::field-group>
            <x-flux-admin::field-group label="Description" :error="$errors->first('formData.description')" hint="HTML allowed — sanitised on save.">
                <flux:textarea wire:model="formData.description" rows="6" />
            </x-flux-admin::field-group>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-flux-admin::field-group label="Employment type" :error="$errors->first('formData.employment_type')">
                    <flux:select wire:model="formData.employment_type" placeholder="— Select —">
                        <flux:select.option value="Full-time">Full-time</flux:select.option>
                        <flux:select.option value="Part-time">Part-time</flux:select.option>
                        <flux:select.option value="Contract">Contract</flux:select.option>
                        <flux:select.option value="Temporary">Temporary</flux:select.option>
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Location" :error="$errors->first('formData.location')">
                    <flux:input wire:model="formData.location" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Salary" :error="$errors->first('formData.salary')">
                    <flux:input wire:model="formData.salary" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Contact email" :error="$errors->first('formData.contact_email')">
                    <flux:input type="email" wire:model="formData.contact_email" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Date posted" :error="$errors->first('formData.job_posted')">
                    <flux:input type="date" wire:model="formData.job_posted" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Expires on" :error="$errors->first('formData.expire_date')">
                    <flux:input type="date" wire:model="formData.expire_date" />
                </x-flux-admin::field-group>
            </div>
            <flux:checkbox wire:model="formData.is_active" label="Active" />
            <div class="flex justify-end gap-2">
                <flux:button type="button" variant="ghost" wire:click="$set('showForm', false)" class="!rounded-none">Cancel</flux:button>
                <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
