<dl class="grid grid-cols-2 gap-px bg-zinc-200 dark:bg-zinc-700 sm:grid-cols-4">
    <div class="bg-white px-4 py-3 dark:bg-zinc-900">
        <dt class="text-xs text-zinc-500 dark:text-zinc-400">Full name</dt>
        <dd class="mt-0.5 text-sm font-medium text-zinc-900 dark:text-white">
            {{ \App\Support\ClubMemberStaffAccess::formatField($clubMember->full_name) }}
        </dd>
    </div>
    <div class="bg-white px-4 py-3 dark:bg-zinc-900">
        <dt class="text-xs text-zinc-500 dark:text-zinc-400">VRM</dt>
        <dd class="mt-0.5 text-sm font-medium text-zinc-900 dark:text-white">{{ \App\Support\ClubMemberStaffAccess::formatField($clubMember->vrm) }}</dd>
    </div>
    <div class="bg-white px-4 py-3 dark:bg-zinc-900">
        <dt class="text-xs text-zinc-500 dark:text-zinc-400">Make</dt>
        <dd class="mt-0.5 text-sm font-medium text-zinc-900 dark:text-white">{{ \App\Support\ClubMemberStaffAccess::formatField($clubMember->make) }}</dd>
    </div>
    <div class="bg-white px-4 py-3 dark:bg-zinc-900">
        <dt class="text-xs text-zinc-500 dark:text-zinc-400">Model</dt>
        <dd class="mt-0.5 text-sm font-medium text-zinc-900 dark:text-white">{{ \App\Support\ClubMemberStaffAccess::formatField($clubMember->model) }}</dd>
    </div>
    <div class="bg-white px-4 py-3 dark:bg-zinc-900">
        <dt class="text-xs text-zinc-500 dark:text-zinc-400">Year</dt>
        <dd class="mt-0.5 text-sm font-medium text-zinc-900 dark:text-white">{{ \App\Support\ClubMemberStaffAccess::formatField($clubMember->year) }}</dd>
    </div>
    <div class="bg-white px-4 py-3 dark:bg-zinc-900">
        <dt class="text-xs text-zinc-500 dark:text-zinc-400">Partner</dt>
        <dd class="mt-0.5 text-sm font-medium text-zinc-900 dark:text-white">{{ $clubMember->partner?->companyname ?? '—' }}</dd>
    </div>
    <div class="bg-white px-4 py-3 dark:bg-zinc-900">
        <dt class="text-xs text-zinc-500 dark:text-zinc-400">T&amp;C agreed</dt>
        <dd class="mt-0.5 text-sm font-medium text-zinc-900 dark:text-white">{{ $clubMember->tc_agreed ? 'Yes' : 'No' }}</dd>
    </div>
    <div class="bg-white px-4 py-3 dark:bg-zinc-900">
        <dt class="text-xs text-zinc-500 dark:text-zinc-400">Email sent</dt>
        <dd class="mt-0.5 text-sm font-medium text-zinc-900 dark:text-white">{{ $clubMember->email_sent ? 'Yes' : 'No' }}</dd>
    </div>
</dl>
