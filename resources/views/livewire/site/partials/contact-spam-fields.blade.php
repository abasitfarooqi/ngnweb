<div aria-hidden="true" style="position:absolute;left:-10000px;width:1px;height:1px;overflow:hidden;">
    <label for="company_website">Website</label>
    <input id="company_website" type="text" wire:model="companyWebsite" tabindex="-1" autocomplete="off">
</div>
@error('form') <p class="text-danger">{{ $message }}</p> @enderror
@if (config('contact.captcha_enabled') === true)
    <div wire:ignore class="contact-captcha space-y-2">
        <div class="g-recaptcha" data-sitekey="{{ config('captcha.sitekey') }}"
             data-callback="ngnContactCaptchaCompleted"></div>
    </div>
    @once
        @push('scripts')
            <script src="https://www.google.com/recaptcha/api.js" async defer></script>
            <script>
                window.ngnContactCaptchaCompleted = token => {
                    const widget = [...document.querySelectorAll('.contact-captcha')]
                        .find(element => element.offsetParent !== null) || document.querySelector('.contact-captcha');
                    const component = widget?.closest('[wire\\:id]');
                    component && Livewire.find(component.getAttribute('wire:id'))?.set('captchaToken', token);
                };
                document.addEventListener('livewire:init', () => {
                    Livewire.on('contact-captcha-reset', () => window.grecaptcha?.reset());
                });
            </script>
        @endpush
    @endonce
    <style>
        .contact-captcha textarea.g-recaptcha-response {
            display: none !important;
        }
    </style>
@endif
