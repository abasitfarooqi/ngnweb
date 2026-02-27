<div>
    @php
        $titles = [
            'privacy'  => 'Privacy & Cookie Policy',
            'terms'    => 'Terms of Use',
            'shipping' => 'Shipping Policy',
            'refund'   => 'Refund Policy',
            'return'   => 'Return Policy',
        ];
        $title = $titles[$page] ?? 'Legal';
    @endphp

    <div class="ngn-page-header">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <h1 class="text-3xl font-black text-white">{{ $title }}</h1>
            <p class="text-zinc-400 text-sm mt-1">Last updated: {{ now()->format('F Y') }}</p>
        </div>
    </div>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12 prose prose-zinc max-w-none">
        @if($page === 'privacy')
            <h2>1. Introduction</h2>
            <p>NGN Motors Ltd ("we", "our", "us") is committed to protecting your personal data. This policy explains how we collect, use and store information when you use our website and services.</p>
            <h2>2. Data We Collect</h2>
            <p>We may collect your name, email address, phone number, vehicle registration and usage data when you contact us, book a service, or use our website.</p>
            <h2>3. How We Use Your Data</h2>
            <p>We use your data to process bookings, respond to enquiries, send service reminders and improve our website. We do not sell your data to third parties.</p>
            <h2>4. Cookies</h2>
            <p>We use essential cookies to operate our website and analytics cookies (with your consent) to understand how visitors use our site.</p>
            <h2>5. Your Rights</h2>
            <p>You have the right to access, correct or delete your personal data. Contact us at <a href="mailto:info@neguinhomotors.co.uk">info@neguinhomotors.co.uk</a>.</p>
            <h2>6. Contact</h2>
            <p>For any data protection queries, contact: NGN Motors Ltd, Unit 2, 15 Empress Avenue, London, E12 5HH.</p>

        @elseif($page === 'terms')
            <h2>1. Use of Website</h2>
            <p>By using the NGN Motors website, you agree to these terms. Do not use the website for unlawful purposes.</p>
            <h2>2. Intellectual Property</h2>
            <p>All content on this website is owned by NGN Motors Ltd and may not be reproduced without permission.</p>
            <h2>3. Limitation of Liability</h2>
            <p>NGN Motors is not liable for any indirect or consequential loss arising from use of this website or our services, except where required by law.</p>
            <h2>4. Governing Law</h2>
            <p>These terms are governed by the laws of England and Wales.</p>

        @elseif($page === 'refund')
            <h2>Refund Policy</h2>
            <p>If you are not satisfied with a service, please contact us within 14 days of the service date. We will assess your claim and, where appropriate, offer a partial or full refund or a rectification service at no additional cost.</p>
            <p>Deposits paid for rentals or services are non-refundable unless we cancel the appointment.</p>
            <p>For motorcycle purchases, statutory consumer rights apply under the Consumer Rights Act 2015.</p>

        @elseif($page === 'return')
            <h2>Return Policy</h2>
            <p>For physical products purchased through our store, you have 14 days from delivery to return items in original, unused condition.</p>
            <p>Contact us at <a href="mailto:info@neguinhomotors.co.uk">info@neguinhomotors.co.uk</a> to arrange a return. Return postage is at the buyer's expense unless the item is faulty.</p>

        @elseif($page === 'shipping')
            <h2>Shipping Policy</h2>
            <p>We offer delivery of accessories and parts purchased through our store to addresses within the UK. Delivery times are 2–5 working days unless stated otherwise.</p>
            <p>Motorcycle delivery within London is available and priced separately — contact us for a quote.</p>
        @endif
    </div>
</div>
