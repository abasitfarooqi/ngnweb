<div>
<div class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 py-3">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="/">Home</flux:breadcrumbs.item>
            <flux:breadcrumbs.item href="/careers">Careers</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ $career->job_title }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">{{ $career->job_title }}</h1>
        <p class="text-sm text-gray-500">Posted: {{ $career->job_posted ? $career->job_posted->format('d M Y') : 'N/A' }}</p>
    </div>

    <div class="prose dark:prose-invert max-w-none mb-10">
        {!! $career->description !!}
    </div>

    {{-- Application form --}}
    <flux:card class="p-8">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-5">Apply for This Role</h2>

        @if(session('success'))
            <flux:callout variant="success" icon="check-circle" class="mb-5">
                <flux:callout.text>{{ session('success') }}</flux:callout.text>
            </flux:callout>
        @endif

        <form wire:submit="submitApplication" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>First Name *</flux:label>
                    <flux:input wire:model="firstName" />
                    <flux:error name="firstName" />
                </flux:field>
                <flux:field>
                    <flux:label>Last Name *</flux:label>
                    <flux:input wire:model="lastName" />
                    <flux:error name="lastName" />
                </flux:field>
            </div>
            <flux:field>
                <flux:label>Email *</flux:label>
                <flux:input wire:model="email" type="email" />
                <flux:error name="email" />
            </flux:field>
            <flux:field>
                <flux:label>Phone *</flux:label>
                <flux:input wire:model="phone" type="tel" />
                <flux:error name="phone" />
            </flux:field>
            <flux:field>
                <flux:label>Cover Letter / Message *</flux:label>
                <flux:textarea wire:model="coverLetter" rows="5" placeholder="Tell us why you'd be a great fit…" />
                <flux:error name="coverLetter" />
            </flux:field>
            <flux:button type="submit" variant="filled" size="base" class="w-full bg-brand-red text-white hover:bg-brand-red-dark">
                Submit Application
            </flux:button>
        </form>
    </flux:card>
</div>
</div>
