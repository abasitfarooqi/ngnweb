<?php

return [

    'logo' => 'img/ngn-motor-logo-fit-small.png',

    'hours' => env('SITE_HOURS', 'Monday to Saturday, 9:00 AM – 6:00 PM · Sunday Closed'),

    /** Used bike detail page: classic | premium. Override per visit with ?layout=premium */
    'used_bike_detail_layout' => env('USED_BIKE_DETAIL_LAYOUT', 'classic'),

    'opening_hours' => [
        'monday'    => '9:00 AM – 6:00 PM',
        'tuesday'   => '9:00 AM – 6:00 PM',
        'wednesday' => '9:00 AM – 6:00 PM',
        'thursday'  => '9:00 AM – 6:00 PM',
        'friday'    => '9:00 AM – 6:00 PM',
        'saturday'  => '9:00 AM – 6:00 PM',
        'sunday'    => 'Closed',
    ],

    'branches' => [
        'catford' => [
            'phone' => '0208 314 1498',
            'name' => 'Catford',
            'whatsapp' => '447951790568',
            'address' => 'NGN 9-13 Catford Hill, London SE6 4NU',
            'email' => 'enquiries@neguinhomotors.co.uk',
            'map' => 'https://www.google.com/maps?q=9-13+Unit+1179+Catford+Hill+London+SE6+4NU',
            'whatsapp_link' => 'https://wa.me/447951790568?text=Hello%20NGN%2C%20I%20would%20like%20to%20inquire%20about%20your%20services.',
        ],
        'tooting' => [
            'phone' => '0203 409 5478',
            'name' => 'Tooting',
            'whatsapp' => '447951790565',
            'address' => 'NGN 4A Penwortham Road, London SW16 6RE',
            'email' => 'enquiries@neguinhomotors.co.uk',
            'map' => 'https://www.google.com/maps?q=4A+Penwortham+Road,+London+SW16+6RE',
            'whatsapp_link' => 'https://wa.me/447951790565?text=Hello%20NGN%2C%20I%20would%20like%20to%20inquire%20about%20your%20services.',
        ],
        'sutton' => [
            'phone' => '0208 412 9275',
            'name' => 'Sutton',
            'whatsapp' => '447946295530',
            'address' => 'NGN 329 High St, Sutton SM1 1LW',
            'email' => 'enquiries@neguinhomotors.co.uk',
            'map' => 'https://www.google.com/maps?q=329+High+St,+Sutton+SM1+1LW',
            'whatsapp_link' => 'https://wa.me/447946295530?text=Hello%20NGN%2C%20I%20would%20like%20to%20inquire%20about%20your%20services.',
        ],
    ],

];
