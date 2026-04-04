{{-- Alias for the standard NGN mail shell. Prefer <x-emails.base> or emails.templates.universal in new code. --}}
@props(['subject' => 'NGN Motors'])
<x-emails.base :subject="$subject">
    {{ $slot ?? '' }}
</x-emails.base>
