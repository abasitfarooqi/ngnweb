<div>
    @if($member)
        <div class="border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800">
            <div class="px-5 py-4 border-b border-zinc-200 dark:border-zinc-700 flex items-center justify-between">
                <h2 class="text-base font-semibold text-zinc-900 dark:text-white">Club Membership</h2>
                <flux:badge :color="$member->is_active ? 'green' : 'zinc'" size="sm">{{ $member->is_active ? 'Active' : 'Inactive' }}</flux:badge>
            </div>

            <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-5">
                <div>
                    <dt class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Full Name</dt>
                    <dd class="mt-1 text-sm text-zinc-900 dark:text-white">{{ $member->full_name ?? '—' }}</dd>
                </div>

                <div>
                    <dt class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Email</dt>
                    <dd class="mt-1 text-sm text-zinc-900 dark:text-white">{{ $member->email ?? '—' }}</dd>
                </div>

                <div>
                    <dt class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Phone</dt>
                    <dd class="mt-1 text-sm text-zinc-900 dark:text-white">{{ $member->phone ?? '—' }}</dd>
                </div>

                <div>
                    <dt class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Make</dt>
                    <dd class="mt-1 text-sm text-zinc-900 dark:text-white">{{ $member->make ?? '—' }}</dd>
                </div>

                <div>
                    <dt class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Model</dt>
                    <dd class="mt-1 text-sm text-zinc-900 dark:text-white">{{ $member->model ?? '—' }}</dd>
                </div>

                <div>
                    <dt class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Year</dt>
                    <dd class="mt-1 text-sm text-zinc-900 dark:text-white">{{ $member->year ?? '—' }}</dd>
                </div>

                <div>
                    <dt class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">VRM</dt>
                    <dd class="mt-1 text-sm text-zinc-900 dark:text-white">{{ $member->vrm ?? '—' }}</dd>
                </div>
            </div>
        </div>
    @else
        <div class="border border-dashed border-zinc-300 dark:border-zinc-600 p-8 text-center">
            <flux:icon name="star" variant="outline" class="w-8 h-8 mx-auto text-zinc-400 dark:text-zinc-500 mb-3" />
            <h3 class="text-base font-semibold text-zinc-900 dark:text-white">Not a Club Member</h3>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">This customer does not have a club membership.</p>
        </div>
    @endif
</div>
