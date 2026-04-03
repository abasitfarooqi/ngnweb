@component('mail::message')
# MOT Reminder Notification

Hello {{ $motDetails['customer_name'] }},

Your MOT is due on {{ $motDetails['mot_due_date']->format('Y-m-d') }}.

Please book your MOT as soon as possible to avoid any last-minute hassles.

@component('mail::button', ['url' => url('/')])
Book Now
@endcomponent

Thank you for using our application!

@endcomponent 