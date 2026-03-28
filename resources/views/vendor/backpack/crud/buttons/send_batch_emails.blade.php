<a href="{{ url('/send-batch-emails') }}" class="btn btn-sm btn-default" title="Send Batch Emails" onclick="event.preventDefault(); document.getElementById('send-batch-emails-form').submit();">
    <i class="la la-paper-plane"></i> Send Batch Emails
</a>

<form id="send-batch-emails-form" action="{{ url('/send-batch-emails') }}" method="GET" style="display: none;">
    @csrf
</form>
