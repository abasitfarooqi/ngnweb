<div>
{{-- Hero --}}
<div class="bg-gradient-to-r from-brand-red to-red-700 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">NGN Partner Terms & Conditions</h1>
    </div>
</div>

{{-- Content --}}
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="prose prose-lg dark:prose-invert max-w-none">

        <ul>
            <li>Partners must comply with all applicable laws and regulations.</li>
            <li>Partnership benefits are non-transferable.</li>
            <li>Only the partner's associated phone number will be eligible for credits.</li>
            <li>Partners must maintain confidentiality of business information.</li>
            <li>Partnership applications are subject to approval based on fleet size and company reputation.</li>
            <li>Partners will earn <strong>17.5% credit</strong> on each £1 spent on repairs, maintenance, accessories and MOT.</li>
            <li>Partners will earn <strong>2% credit</strong> on each £1 spent on all motorbike purchases. Registered businesses trading for 6 months or more will get <strong>4% credit</strong>.</li>
            <li>Credits will be available <strong>48 hours</strong> after purchase.</li>
            <li>Credits will expire after <strong>6 months</strong> of being added to the account.</li>
            <li>All NGN Club policies and terms regarding credit usage apply to partners.</li>
            <li>Credits can only be earned on purchases made after partnership approval.</li>
            <li>NGN reserves the right to terminate a partnership at any time.</li>
            <li>All data is processed in accordance with GDPR regulations.</li>
            <li>Changes to these terms and conditions may occur with notice.</li>
        </ul>

    </div>

    <div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700">
        <p class="text-sm text-gray-600 dark:text-gray-400">Last updated: {{ date('F Y') }}</p>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
            If you have any questions, please <a href="/contact" class="text-brand-red hover:text-red-700">contact us</a>.
        </p>
        <div class="mt-6">
            <a href="{{ route('ngnpartner.subscribe') }}"
               class="inline-flex items-center gap-2 px-6 py-3 bg-brand-red text-white font-semibold hover:bg-red-700 transition">
                Register as a Partner
            </a>
        </div>
    </div>
</div>
</div>
