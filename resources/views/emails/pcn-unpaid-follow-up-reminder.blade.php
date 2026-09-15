<h2>PCN customer payment follow-up</h2>
<p>Please ask the customer to follow up and pay the outstanding PCN amount.</p>
<ul>
    <li><strong>PCN:</strong> {{ $pcnCase->pcn_number }}</li>
    <li><strong>Customer:</strong> {{ trim(($pcnCase->customer?->first_name ?? '').' '.($pcnCase->customer?->last_name ?? '')) ?: 'Not linked' }}</li>
    <li><strong>Vehicle:</strong> {{ $pcnCase->motorbike?->reg_no ?? 'Not linked' }}</li>
    <li><strong>Amount:</strong> £{{ number_format((float) ($pcnCase->reduced_amount ?: $pcnCase->full_amount), 2) }}</li>
    <li><strong>Date of contravention:</strong> {{ $pcnCase->date_of_contravention?->format('d M Y') }}</li>
</ul>
<p>This reminder was generated automatically 12 days after the date of contravention, two days before the 14-day point.</p>
<p><a href="{{ config('services.pcn_reminders.communications_url') }}">Open the Communications page</a></p>
