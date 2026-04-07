<div>
{{-- Hero --}}
<div class="relative bg-gray-900 text-white py-14 md:py-20 overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-br from-black via-gray-900 to-brand-red/30"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl md:text-5xl font-bold mb-4">Motorcycle Repair Services</h1>
        <nav class="text-sm text-gray-400" aria-label="Breadcrumb">
            <ol class="flex flex-wrap gap-2 list-none p-0 m-0 font-semibold">
                <li><a href="{{ route('site.home') }}" class="hover:text-white underline-offset-2">Home Page</a></li>
                <li aria-hidden="true">/</li>
                <li><span class="text-gray-300">Motorcycle Repair Services</span></li>
            </ol>
        </nav>
        <p class="mt-4 text-sm md:text-base text-gray-300 max-w-3xl">Workshop servicing, repairs and MOT across Catford, Tooting and Sutton — same detail as our legacy service pages, now in one place.</p>
        <div class="mt-6 flex flex-wrap gap-2 justify-start">
            <flux:button href="{{ route('all-services') }}" variant="outline" size="sm" class="border-white/40 text-white hover:bg-white/10">All services overview</flux:button>
            <flux:button href="{{ route('site.repairs.comparison') }}" variant="outline" size="sm" class="border-white/40 text-white hover:bg-white/10">Compare basic &amp; full</flux:button>
            <flux:button href="{{ route('site.repairs.repair-services') }}" variant="outline" size="sm" class="border-white/40 text-white hover:bg-white/10">Repair menu</flux:button>
            <flux:button href="{{ route('site.mot') }}" variant="outline" size="sm" class="border-white/40 text-white hover:bg-white/10">MOT</flux:button>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
        {{-- Basic Service summary (legacy copy) --}}
        <flux:card class="p-6 md:p-8 border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm">
            <h2 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white mb-6">Basic Service</h2>
            <ul class="space-y-6 text-gray-700 dark:text-gray-300">
                <li>
                    <h3 class="font-bold text-brand-red mb-2">Engine Maintenance</h3>
                    <ul class="list-none p-0 m-0 font-semibold text-sm space-y-1">
                        <li>Oil Change</li>
                        <li>Oil Filter Replacement</li>
                    </ul>
                </li>
                <li>
                    <h3 class="font-bold text-brand-red mb-2">Brakes</h3>
                    <ul class="list-none p-0 m-0 font-semibold text-sm space-y-1">
                        <li>Brake Check</li>
                        <li>Brake Fluid Inspection</li>
                        <li>Brake Operation Test</li>
                    </ul>
                </li>
                <li>
                    <h3 class="font-bold text-brand-red mb-2">Tires and Wheels</h3>
                    <ul class="list-none p-0 m-0 font-semibold text-sm space-y-1">
                        <li>Tire Pressure Check</li>
                        <li>Tire Condition Inspection</li>
                    </ul>
                </li>
            </ul>
            <div class="mt-6">
                <flux:button href="{{ route('site.repairs.basic') }}" variant="filled" class="bg-brand-red text-white hover:bg-brand-red-dark">
                    View Full Basic Service Details
                </flux:button>
            </div>
        </flux:card>

        {{-- Major Service summary (legacy copy) --}}
        <flux:card class="p-6 md:p-8 border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm">
            <h2 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white mb-6">Major Service</h2>
            <ul class="space-y-6 text-gray-700 dark:text-gray-300">
                <li>
                    <h3 class="font-bold text-brand-red mb-2">Complete Engine Service</h3>
                    <ul class="list-none p-0 m-0 font-semibold text-sm space-y-1">
                        <li>Oil & Filter Change</li>
                        <li>Air Filter Check/Replacement</li>
                        <li>Spark Plug Inspection</li>
                        <li>Fuel System Check</li>
                    </ul>
                </li>
                <li>
                    <h3 class="font-bold text-brand-red mb-2">Transmission and Drive</h3>
                    <ul class="list-none p-0 m-0 font-semibold text-sm space-y-1">
                        <li>Chain/Belt Maintenance</li>
                        <li>Gearbox Check</li>
                        <li>Clutch Adjustment</li>
                    </ul>
                </li>
                <li>
                    <h3 class="font-bold text-brand-red mb-2">Electrical System</h3>
                    <ul class="list-none p-0 m-0 font-semibold text-sm space-y-1">
                        <li>Battery Check</li>
                        <li>Lighting Inspection</li>
                        <li>Charging System Test</li>
                    </ul>
                </li>
            </ul>
            <div class="mt-6">
                <flux:button href="{{ route('site.repairs.full') }}" variant="filled" class="bg-brand-red text-white hover:bg-brand-red-dark">
                    View Full Major Service Details
                </flux:button>
            </div>
        </flux:card>
    </div>

    {{-- Why Choose NGN --}}
    <flux:card class="p-6 md:p-10 border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 mb-12">
        <h2 class="text-xl md:text-2xl font-bold text-center text-brand-red mb-8">Why Choose NGN for Your Motorcycle Service?</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
            <div>
                <h3 class="font-bold text-gray-900 dark:text-white mb-2">Expert Technicians</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Our certified mechanics have years of experience with all motorcycle brands</p>
            </div>
            <div>
                <h3 class="font-bold text-gray-900 dark:text-white mb-2">Quick Turnaround</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Most services completed within 24-48 hours</p>
            </div>
            <div>
                <h3 class="font-bold text-gray-900 dark:text-white mb-2">Competitive Pricing</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Quality service at reasonable rates with no hidden costs</p>
            </div>
        </div>
    </flux:card>

    <flux:callout variant="info" icon="map-pin" class="mb-12">
        <flux:callout.text><span class="font-semibold text-gray-900 dark:text-white">Full service (major) package</span> — For the complete checklist including suspension, cooling, exhaust and optional test ride, open <a href="{{ route('site.repairs.full') }}" class="text-brand-red font-semibold underline underline-offset-2">full service details</a> or the <a href="{{ route('site.repairs.comparison') }}" class="text-brand-red font-semibold underline underline-offset-2">side-by-side comparison</a>.</flux:callout.text>
    </flux:callout>

    <x-site.repairs.branches-cta-dark
        heading="Book your service today"
        intro="Keep your motorcycle in perfect running condition — call your nearest branch or use the enquiry form below."
    />

    {{-- Enquiry form --}}
    <div class="mt-14 max-w-3xl mx-auto">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Book a Repair or Service</h2>

        @if(session('success'))
            <flux:callout variant="success" icon="check-circle" class="mb-6">
                <flux:callout.text>{{ session('success') }}</flux:callout.text>
            </flux:callout>
        @endif

        <flux:card class="p-6 md:p-8 border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm">
            <form wire:key="repairs-index-enquiry-{{ $formNonce }}" wire:submit="submitEnquiry" class="space-y-4">
                <flux:field>
                    <flux:label>Service type *</flux:label>
                    <flux:select wire:model="selectedService" variant="listbox" placeholder="Select service…">
                        <flux:select.option value="Basic Service">Basic Service</flux:select.option>
                        <flux:select.option value="Major Service">Major Service</flux:select.option>
                        <flux:select.option value="Repairs / diagnostics">Repairs / diagnostics</flux:select.option>
                        <flux:select.option value="MOT">MOT</flux:select.option>
                        <flux:select.option value="Other">Other</flux:select.option>
                    </flux:select>
                    <flux:error name="selectedService" />
                </flux:field>

                <flux:field>
                    <flux:label>Preferred branch *</flux:label>
                    <flux:select wire:model="selectedBranch" variant="listbox" searchable placeholder="Choose a branch…">
                        @foreach($branches as $branch)
                            <flux:select.option value="{{ $branch->id }}">{{ $branch->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="selectedBranch" />
                </flux:field>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Full name *</flux:label>
                        <flux:input wire:model="name" />
                        <flux:error name="name" />
                    </flux:field>
                    <flux:field>
                        <flux:label>Phone *</flux:label>
                        <flux:input wire:model="phone" type="tel" />
                        <flux:error name="phone" />
                    </flux:field>
                </div>

                <flux:field>
                    <flux:label>Email *</flux:label>
                    <flux:input wire:model="email" type="email" />
                    <flux:error name="email" />
                </flux:field>

                <flux:field>
                    <flux:label>Registration number</flux:label>
                    <flux:input wire:model="regNo" class="uppercase tracking-wider font-semibold" placeholder="AB12 CDE" />
                    <flux:error name="regNo" />
                </flux:field>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Make</flux:label>
                        <flux:input wire:model="make" placeholder="e.g. Honda" />
                    </flux:field>
                    <flux:field>
                        <flux:label>Model</flux:label>
                        <flux:input wire:model="model" placeholder="e.g. CB650R" />
                    </flux:field>
                </div>

                <flux:field>
                    <flux:label>Describe the work or issue *</flux:label>
                    <flux:textarea wire:model="description" rows="4" placeholder="Please describe what you need…" />
                    <flux:error name="description" />
                </flux:field>

                <flux:button type="submit" variant="filled" class="w-full bg-brand-red text-white hover:bg-brand-red-dark">
                    Submit enquiry
                </flux:button>
            </form>
        </flux:card>
    </div>
</div>
</div>
