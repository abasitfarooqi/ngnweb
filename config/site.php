<?php

return [

    'hours' => env('SITE_HOURS', 'Mon–Fri 9am–6pm · Sat 9am–3:45pm · Sun Closed'),

    /** Used bike detail page: classic | premium. Override per visit with ?layout=premium */
    'used_bike_detail_layout' => env('USED_BIKE_DETAIL_LAYOUT', 'classic'),

    'opening_hours' => [
        'monday'    => '9 am – 6 pm',
        'tuesday'   => '9 am – 6 pm',
        'wednesday' => '9 am – 6 pm',
        'thursday'  => '9 am – 6 pm',
        'friday'    => '9 am – 6 pm',
        'saturday'  => '9 am – 3:45 pm',
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
