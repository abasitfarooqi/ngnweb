<?php

namespace App\Livewire\Site;

use Livewire\Component;

class Faq extends Component
{
    public string $search = '';

    public array $faqs = [
        [
            'category' => 'Rentals',
            'q' => 'What do I need to rent a motorcycle?',
            'a' => 'You need a valid motorcycle licence (or CBT certificate for 125cc), proof of address (utility bill or bank statement not older than 3 months), a valid photo ID (passport or driving licence), and a refundable security deposit.',
        ],
        [
            'category' => 'Rentals',
            'q' => 'How much is the weekly rental price?',
            'a' => 'Rental prices start from £80/week for 125cc motorcycles. The exact price depends on the model. You can see live pricing on our Rentals page or contact any branch.',
        ],
        [
            'category' => 'Rentals',
            'q' => 'Is insurance included in the rental?',
            'a' => 'Yes. All rental bikes include third-party, fire and theft insurance, road tax, and breakdown assistance. You must purchase a lock and chain from our shop.',
        ],
        [
            'category' => 'Rentals',
            'q' => 'Can I rent with just a CBT?',
            'a' => 'Yes, you can rent a 125cc motorcycle with a valid CBT certificate. Full licence holders have access to a wider range of bikes.',
        ],
        [
            'category' => 'Rentals',
            'q' => 'What is the minimum rental period?',
            'a' => 'Our standard rental is weekly. We may accommodate longer-term arrangements at discretion. Contact us to discuss your needs.',
        ],
        [
            'category' => 'MOT',
            'q' => 'How much does a motorcycle MOT cost?',
            'a' => 'MOT testing starts from £29.65 (the DVSA maximum fee). We offer competitive pricing across all three of our branches in Catford, Tooting, and Sutton.',
        ],
        [
            'category' => 'MOT',
            'q' => 'How long does an MOT take?',
            'a' => 'A standard motorcycle MOT typically takes 30-45 minutes. If minor advisories are found and you\'d like them rectified on the same day, allow extra time.',
        ],
        [
            'category' => 'MOT',
            'q' => 'Do I need to book an MOT in advance?',
            'a' => 'We recommend booking in advance to secure your preferred time, especially during busy periods. You can book online via our MOT booking page or call your nearest branch.',
        ],
        [
            'category' => 'Repairs & Servicing',
            'q' => 'What types of motorcycles do you service?',
            'a' => 'We service all makes and models with a focus on Japanese motorcycles (Honda, Yamaha, Kawasaki, Suzuki) and popular European brands. Contact us if you are unsure about your specific model.',
        ],
        [
            'category' => 'Repairs & Servicing',
            'q' => 'How much does a basic service cost?',
            'a' => 'Basic services start from £79 and include oil and filter change, brake fluid check, chain and sprocket inspection, tyre check, battery check, and a safety inspection report.',
        ],
        [
            'category' => 'Repairs & Servicing',
            'q' => 'Do you offer collection and delivery for repairs?',
            'a' => 'Yes, we offer motorcycle collection and delivery across London. Contact your nearest branch for availability and charges.',
        ],
        [
            'category' => 'Sales & Finance',
            'q' => 'Do you sell new motorcycles?',
            'a' => 'Yes. We sell new Honda and Yamaha motorcycles as well as quality used bikes sourced from our own rental fleet and external suppliers.',
        ],
        [
            'category' => 'Sales & Finance',
            'q' => 'Can I get finance on a motorcycle?',
            'a' => 'Yes, we offer flexible finance plans to help you purchase your motorcycle. Interest rates and terms vary. Visit our Finance page or speak to a member of staff for a personalised quote.',
        ],
        [
            'category' => 'Sales & Finance',
            'q' => 'Do you buy or part-exchange motorcycles?',
            'a' => 'We consider part-exchange on a case-by-case basis. Contact us with details of your bike and we will advise.',
        ],
        [
            'category' => 'General',
            'q' => 'Where are your branches?',
            'a' => 'We have three branches in London: Catford (SE6), Tooting (SW17), and Sutton (SM1). Full address details are on our Locations page.',
        ],
        [
            'category' => 'General',
            'q' => 'What are your opening hours?',
            'a' => 'Monday to Saturday: 9:00am to 6:00pm. We are closed on Sundays and Bank Holidays.',
        ],
        [
            'category' => 'General',
            'q' => 'Can I visit any branch for any service?',
            'a' => 'Yes. All three branches offer the full range of services. Some services may require appointment – we recommend calling ahead.',
        ],
        [
            'category' => 'NGN Club',
            'q' => 'What is the NGN Club?',
            'a' => 'The NGN Club is our loyalty programme for riders. Members receive exclusive discounts on services, priority booking, and special rewards. Sign up on our NGN Club page.',
        ],
        [
            'category' => 'NGN Club',
            'q' => 'Is it free to join the NGN Club?',
            'a' => 'There is a membership fee which gives you access to exclusive benefits. Visit the NGN Club page for current membership details and pricing.',
        ],
    ];

    public function getFilteredFaqsProperty(): array
    {
        if ($this->search === '') {
            return $this->faqs;
        }

        $query = strtolower($this->search);

        return array_values(array_filter($this->faqs, fn ($faq) =>
            str_contains(strtolower($faq['q']), $query) ||
            str_contains(strtolower($faq['a']), $query) ||
            str_contains(strtolower($faq['category']), $query)
        ));
    }

    public function getGroupedFaqsProperty(): array
    {
        $grouped = [];
        foreach ($this->filteredFaqs as $faq) {
            $grouped[$faq['category']][] = $faq;
        }
        return $grouped;
    }

    public function render()
    {
        return view('livewire.site.faq')
            ->layout('components.layouts.public', [
                'title' => 'FAQs – Motorcycle Rental, MOT, Repairs & Sales | NGN Motors',
                'description' => 'Frequently asked questions about motorcycle rentals, MOT testing, repairs, servicing, finance, and the NGN Club at NGN Motors London.',
            ]);
    }
}
