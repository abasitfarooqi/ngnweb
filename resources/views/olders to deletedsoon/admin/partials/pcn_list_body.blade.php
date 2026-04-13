@foreach ($pcnList as $pcn)
<tr>
    <td>{{ $pcn['customer_name'] }}</td>
    <td>{{ $pcn['reg_no'] }}</td>
    <td>{{ $pcn['pcn_number'] }}</td>
    <td>{{ number_format($pcn['amount'], 2) }}</td>
    <td>{{ $pcn['is_whatsapp_sent'] ? 'Yes' : 'No' }}</td>
    <td>{{ $pcn['whatsapp_last_reminder_sent_at'] }}</td>
    <td>
        <form action="{{ route('pcn-case.send-reminder', $pcn['id']) }}" method="POST" id="whatsappForm-{{ $pcn['id'] }}">
            @csrf
            <a href="{{ $pcn['whatsapp_url'] }}" target="_blank" class="btn btn-success"
               onclick="event.preventDefault(); document.getElementById('whatsappForm-{{ $pcn['id'] }}').submit(); window.open('{{ $pcn['whatsapp_url'] }}', '_blank');">
               Send Reminder WhatsApp
            </a>
        </form>
    </td>
</tr>
@endforeach
