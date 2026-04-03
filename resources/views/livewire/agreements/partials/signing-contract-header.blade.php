@php
    $title = $title ?? 'MOTORCYCLE SALE AGREEMENT';
@endphp
<div class="agreement-brand-header-wrap">
    <div class="agreement-brand-header" role="group" aria-label="Company header">
        <div class="agreement-brand-header__logo">
            <x-agreements.theme-logo class="agreement-brand-header__logo-img w-full" />
        </div>
        <div class="agreement-brand-header__address">
            9-13 Catford Hill, <br>
            London, SE6 4NU<br>
            0203 409 5478 / 0208 314 1498<br>
            customerservice@neguinhomotors.co.uk<br>
            ngnmotors.co.uk
        </div>
        <div class="agreement-brand-header__title">
            {{ $title }}
        </div>
    </div>
</div>
