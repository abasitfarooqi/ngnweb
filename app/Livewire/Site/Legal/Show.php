<?php

namespace App\Livewire\Site\Legal;

use Livewire\Component;

class Show extends Component
{
    public string $slug = 'terms';

    public string $title = '';

    public string $content = '';

    private static array $pages = [
        'terms' => [
            'title' => 'Terms & Conditions',
            'content' => <<<'HTML'
                <h2>Introduction</h2>
                <p>These Terms & Conditions apply to the website www.ngnmotors.co.uk owned by NGN and govern your use of our services. By accessing this website you accept these terms in full.</p>

                <h2>Website Policy</h2>
                <p>NGN takes a proactive approach to user privacy and ensures the necessary steps are taken to protect the privacy of its users throughout their visit to this site. This website complies with all UK national laws and requirements for user privacy.</p>

                <h2>Use of the Website</h2>
                <p>You agree to use this website only for lawful purposes and in a manner that does not infringe the rights of others. You must not misuse our website by knowingly introducing viruses or other material that is malicious or technologically harmful.</p>

                <h2>Intellectual Property</h2>
                <p>All content on this site, including but not limited to text, graphics, logos, images and software, is the property of NGN and is protected by applicable intellectual property laws. Unauthorised use is prohibited.</p>

                <h2>Rental & Service Agreements</h2>
                <p>All motorcycle rentals and services are subject to separate rental agreements signed at the time of booking. Customers must hold a valid UK driving licence appropriate for the class of vehicle rented. Full insurance details are required before any rental begins.</p>

                <h2>Payment Terms</h2>
                <p>Payment is required at the time of booking or upon completion of service unless otherwise agreed in writing. NGN Motors accepts card payments and bank transfers. All prices include VAT where applicable.</p>

                <h2>Limitation of Liability</h2>
                <p>NGN Motors shall not be liable for any indirect or consequential loss arising from the use of this website or our services beyond the cost of the service provided. Nothing in these terms excludes our liability for death or personal injury caused by negligence.</p>

                <h2>Governing Law</h2>
                <p>These terms are governed by English law. Any disputes shall be subject to the exclusive jurisdiction of the courts of England and Wales.</p>

                <h2>Changes to Terms</h2>
                <p>We reserve the right to update these Terms & Conditions at any time. Continued use of this website after any changes constitutes acceptance of the new terms.</p>

                <h2>Contact</h2>
                <p>NGN, 9–13 Catford Hill, London SE6 4NU. Email: <a href="mailto:enquiries@neguinhomotors.co.uk">enquiries@neguinhomotors.co.uk</a></p>
            HTML,
        ],
    ];

    public function mount(string $slug = 'terms'): void
    {
        $this->slug = $slug;
        $page = static::$pages[$slug] ?? static::$pages['terms'];
        $this->title   = $page['title'];
        $this->content = $page['content'];
    }

    public function render()
    {
        return view('livewire.site.legal.show')
            ->layout('components.layouts.public', [
                'title' => $this->title . ' | NGN Motors',
            ]);
    }
}
