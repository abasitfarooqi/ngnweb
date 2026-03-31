@php
    $hasEmail = !empty($entry->email);
    $hasPhone = !empty($entry->phone);
@endphp

@if($hasEmail && $hasPhone)
    <form action="{{ route('backpack.customer.send-portal-credentials', ['customerId' => $entry->getKey()]) }}"
          method="POST"
          style="display:inline-block"
          onsubmit="return confirm('Send portal credentials to this customer now?');">
        @csrf
        <button type="submit" class="btn btn-sm btn-link" title="Send portal credentials">
            <i class="la la-paper-plane"></i> Send creds
        </button>
    </form>
@endif

