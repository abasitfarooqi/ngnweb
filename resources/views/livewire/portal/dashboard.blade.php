<div>
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Dashboard</h1>

    @if(session('success'))
        <flux:callout variant="success" icon="check-circle" class="mb-5">
            <flux:callout.text>{{ session('success') }}</flux:callout.text>
        </flux:callout>
    @endif

    {{-- Verification status --}}
    @if($profile)
        <flux:callout
            variant="{{ $profile->verification_status === 'verified' ? 'success' : 'warning' }}"
            icon="{{ $profile->verification_status === 'verified' ? 'check-circle' : 'exclamation-triangle' }}"
            class="mb-6"
        >
            <flux:callout.heading>
                @if($profile->verification_status === 'draft') Complete Your Profile
                @elseif($profile->verification_status === 'submitted') Documents Under Review
                @elseif($profile->verification_status === 'verified') Verified Until {{ $profile->verification_expires_at?->format('d M Y') }}
                @elseif($profile->verification_status === 'expired') Verification Expired
                @else {{ ucfirst($profile->verification_status) }} @endif
            </flux:callout.heading>
            <flux:callout.text>
                @if($profile->verification_status === 'draft') Complete your profile and upload required documents to access all services.
                @elseif($profile->verification_status === 'submitted') Our team is reviewing your documents. Usually 1–2 business days.
                @elseif($profile->verification_status === 'verified') Your account is fully verified and you can access all services.
                @elseif($profile->verification_status === 'expired') Your verification has expired. Please update your documents.
                @endif
            </flux:callout.text>
            @if($profile->verification_status !== 'verified')
                <div class="mt-3">
                    <flux:button href="{{ route('account.documents') }}" variant="filled" size="sm" class="bg-brand-red text-white">
                        Upload Documents
                    </flux:button>
                </div>
            @endif
        </flux:callout>
    @endif

    {{-- Active services --}}
    @if($profile && ($activeRental || $upcomingMOT || $upcomingDelivery))
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            @if($activeRental)
                <flux:card class="p-5 border-l-4 border-green-500">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wide text-green-600 mb-1">Active Rental</p>
                            @foreach($activeRental->activeItems as $item)
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $item->motorbike->make ?? '' }} {{ $item->motorbike->model ?? '' }}</p>
                                <p class="text-xs text-gray-500">Reg: {{ $item->motorbike->reg_no ?? 'N/A' }}</p>
                            @endforeach
                            <p class="text-xs text-gray-500 mt-1">Started: {{ $activeRental->start_date->format('d M Y') }}</p>
                        </div>
                        <flux:badge color="green" class="text-xs">Active</flux:badge>
                    </div>
                    <a href="{{ route('account.rentals') }}" class="mt-3 text-xs text-brand-red hover:underline block">View Details →</a>
                </flux:card>
            @endif

            @if($upcomingMOT)
                <flux:card class="p-5 border-l-4 border-blue-500">
                    <p class="text-xs font-bold uppercase tracking-wide text-blue-600 mb-1">Upcoming MOT</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $upcomingMOT->vehicle_registration }}</p>
                    <p class="text-xs text-gray-500">{{ $upcomingMOT->date_of_appointment->format('d M Y') }} at {{ $upcomingMOT->start->format('H:i') }}</p>
                    <p class="text-xs text-gray-500">{{ $upcomingMOT->branch->name ?? 'Branch TBC' }}</p>
                    <a href="{{ route('account.bookings') }}" class="mt-3 text-xs text-brand-red hover:underline block">View Booking →</a>
                </flux:card>
            @endif

            @if($upcomingDelivery)
                <flux:card class="p-5 border-l-4 border-orange-500">
                    <p class="text-xs font-bold uppercase tracking-wide text-orange-600 mb-1">Recovery/Delivery</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">VRM: {{ $upcomingDelivery->vrm ?? 'TBC' }}</p>
                    <p class="text-xs text-gray-500">Pickup: {{ $upcomingDelivery->pickup_date?->format('d M Y') }}</p>
                    <a href="{{ route('account.recovery') }}" class="mt-3 text-xs text-brand-red hover:underline block">Track Request →</a>
                </flux:card>
            @endif
        </div>
    @endif

    {{-- Quick actions --}}
    <h2 class="text-sm font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-4">Quick Actions</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach([
            ['icon'=>'document-text', 'title'=>'Upload Documents',    'sub'=>'Manage your documents',     'route'=>route('account.documents')],
            ['icon'=>'calendar',      'title'=>'Book MOT',            'sub'=>'Schedule your MOT test',    'route'=>route('account.bookings')],
            ['icon'=>'clock',         'title'=>'Start Rental Booking','sub'=>'Browse available bikes',    'route'=>route('account.rentals')],
            ['icon'=>'banknotes',     'title'=>'Apply for Finance',   'sub'=>'Finance applications',      'route'=>route('account.finance')],
            ['icon'=>'arrow-path',    'title'=>'Recurring Payments',  'sub'=>'Rental and finance history', 'route'=>route('account.payments.recurring')],
            ['icon'=>'bolt',          'title'=>'Request Recovery',    'sub'=>'Breakdown assistance',      'route'=>route('account.recovery')],
            ['icon'=>'star',          'title'=>'NGN Club',            'sub'=>'Membership & credits',      'route'=>route('account.club')],
        ] as $action)
            <a href="{{ $action['route'] }}" class="group">
                <flux:card class="p-5 hover:border-brand-red transition">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gray-100 dark:bg-gray-800 group-hover:bg-brand-red flex items-center justify-center transition">
                            <flux:icon name="{{ $action['icon'] }}" class="h-5 w-5 text-brand-red group-hover:text-white transition" />
                        </div>
                        <div>
                            <p class="font-semibold text-sm text-gray-900 dark:text-white group-hover:text-brand-red transition">{{ $action['title'] }}</p>
                            <p class="text-xs text-gray-500">{{ $action['sub'] }}</p>
                        </div>
                    </div>
                </flux:card>
            </a>
        @endforeach
    </div>
</div>
