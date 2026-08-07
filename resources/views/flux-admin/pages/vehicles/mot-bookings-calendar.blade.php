<div x-data="motBookingCalendarPage()" x-init="init()">
    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-1">
                <a href="{{ route('flux-admin.mot-bookings.index') }}" wire:navigate class="hover:text-zinc-700 dark:hover:text-zinc-200 transition">MOT bookings</a>
                <span>/</span>
                <span>Calendar</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">MOT calendar</h1>
            <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Click a booking to edit. Drag on empty time to create a slot.</p>
        </div>
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <div class="min-w-[12rem]">
                <flux:select wire:model.live="branchId" placeholder="All branches">
                    <flux:select.option value="">All branches</flux:select.option>
                    @foreach($branches as $branch)
                        <flux:select.option value="{{ $branch->id }}">{{ $branch->name }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>
            <a href="{{ route('flux-admin.mot-bookings.index') }}" wire:navigate>
                <flux:button size="sm" variant="ghost" icon="table-cells" class="!rounded-none">List view</flux:button>
            </a>
            <a href="{{ route('flux-admin.mot-bookings.create') }}" wire:navigate>
                <flux:button size="sm" variant="primary" icon="plus" class="!rounded-none">New booking</flux:button>
            </a>
        </div>
    </div>

    <div class="border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900">
        <div wire:ignore id="mot-booking-calendar-root" class="mot-booking-calendar-root min-h-[36rem]"></div>
    </div>
</div>

@assets
<link href="{{ asset('assets/libs/fullcalendar/main.min.css') }}" rel="stylesheet">
<script src="{{ asset('assets/libs/fullcalendar/main.min.js') }}"></script>
<script src="{{ asset('assets/libs/fullcalendar/locales/en-gb.js') }}"></script>
@endassets

@script
<script>
Alpine.data('motBookingCalendarPage', () => ({
    calendar: null,
    init() {
        this.$nextTick(() => this.mountCalendar());

        this.$wire.on('mot-booking-calendar-refetch', () => {
            this.calendar?.refetchEvents();
        });
    },
    mountCalendar() {
        const root = document.getElementById('mot-booking-calendar-root');
        if (!root || typeof FullCalendar === 'undefined') {
            return;
        }

        if (this.calendar) {
            this.calendar.destroy();
        }

        const editUrl = @js(route('flux-admin.mot-bookings.edit', ['motBooking' => '__ID__']));
        const createUrl = @js(route('flux-admin.mot-bookings.create'));

        this.calendar = new FullCalendar.Calendar(root, {
            initialView: 'timeGridWeek',
            locale: 'en-gb',
            firstDay: 1,
            height: 'auto',
            nowIndicator: true,
            allDaySlot: false,
            selectable: true,
            selectMirror: true,
            slotMinTime: '09:00:00',
            slotMaxTime: '17:30:00',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek',
            },
            events: (info, successCallback, failureCallback) => {
                $wire.fetchEvents(info.startStr, info.endStr)
                    .then(successCallback)
                    .catch(failureCallback);
            },
            eventClick(info) {
                const id = info.event.id;
                if (!id) {
                    return;
                }

                const url = editUrl.replace('__ID__', encodeURIComponent(id));
                if (window.Livewire?.navigate) {
                    Livewire.navigate(url);
                } else {
                    window.location.href = url;
                }
            },
            select(info) {
                const params = new URLSearchParams({
                    start: info.startStr,
                    end: info.endStr,
                });
                const url = `${createUrl}?${params.toString()}`;

                if (window.Livewire?.navigate) {
                    Livewire.navigate(url);
                } else {
                    window.location.href = url;
                }
            },
        });

        this.calendar.render();
    },
}));
</script>
@endscript

<style>
    .mot-booking-calendar-root .fc .fc-button {
        border-radius: 0 !important;
    }
    .mot-booking-calendar-root .fc .fc-toolbar-title {
        font-size: 1.125rem;
        font-weight: 600;
    }
    .mot-booking-calendar-root .fc .fc-event {
        border-radius: 0;
        cursor: pointer;
    }
    .mot-booking-calendar-root .fc .fc-col-header-cell-cushion,
    .mot-booking-calendar-root .fc .fc-timegrid-slot-label-cushion {
        font-size: 0.75rem;
    }
</style>
