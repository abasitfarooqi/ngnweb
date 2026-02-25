<?php

namespace App\Livewire;

use Livewire\Component;

class ProductCarouselTabBased extends Component
{
    public $products = [];

    public function mount()
    {
        $this->products = [
            [
                'title' => 'MT Braker Crash SV A8 Matt White Pink Purple',
                'price' => '£119.99',
                'image_1' => '/assets/img/accessories/helmets/1-2.jpg',
                'image_2' => '/assets/img/accessories/helmets/1-3.jpg',
                'description' => '<div>
                    <p>The new Braker SV is another 2024 innovation from MT Helmets. It represents an entry-level model in the sport-touring segment, featuring cutting-edge solutions such as the MT-QVSS visor attachment system, a housing for the UCS intercom, and a quick-actuating sun visor.</p>

                    <p>The Braker is a next-generation model designed to meet ECE 22.06 standards right from its inception, ensuring the highest level of safety at an affordable price point.</p>

                    <p>It boasts a triple front ventilation system and a rear spoiler with an air extraction system to enhance interior airflow.</p>

                    <h3>Key Features</h3>
                    <ul>
                        <li>Composition: HIRP - High Impact Resistance Polymer</li>
                        <li>Number of Shell Sizes: 2 (XS to M and L to XXL)</li>
                        <li>Number of EPS Liners: 3 (XS, S, M and L to XXL)</li>
                        <li>Available Sizes: XS to XXL</li>
                        <li>Weight: 1,600 grams</li>
                        <li>Visor: High-resistance MT-V-28B visor with MT-QVSS (Quick Visor Swap System)</li>
                        <li>Sun Visor: Single-hand actuation sun visor</li>
                        <li>Safety: Designed from the ground up to exceed ECE 22.06 regulations</li>
                        <li>Closure: Micrometric MT-MDTC type with a double-tooth system</li>
                        <li>Vents: Triple front ventilation system and a rear spoiler with an air extraction system to enhance interior airflow</li>
                        <li>Removable Interiors: Breathable, removable and washable interior components</li>
                        <li>MT-NSIC Intercom: Housing for standard UCS intercom</li>
                        <li>Visor Insert Compatibility: Fogoff FOG010 and Pinlock DKS435</li>
                    </ul>
                </div>',
            ],
            [
                'title' => 'MT Rapide Overtake Matt Black & Fluo Yellow',
                'price' => '£149.99',
                'image_1' => '/assets/img/accessories/helmets/2-1.jpg',
                'image_2' => '/assets/img/accessories/helmets/2-3.jpg',
                'description' => '<div>
                    <p>The new Braker SV is another 2024 innovation from MT Helmets. It represents an entry-level model in the sport-touring segment, featuring cutting-edge solutions such as the MT-QVSS visor attachment system, a housing for the UCS intercom, and a quick-actuating sun visor.</p>

                    <p>The Braker is a next-generation model designed to meet ECE 22.06 standards right from its inception, ensuring the highest level of safety at an affordable price point.</p>

                    <p>It boasts a triple front ventilation system and a rear spoiler with an air extraction system to enhance interior airflow.</p>

                    <h3>Key Features</h3>
                    <ul>
                        <li>Composition: HIRP - High Impact Resistance Polymer</li>
                        <li>Number of Shell Sizes: 2 (XS to M and L to XXL)</li>
                        <li>Number of EPS Liners: 3 (XS, S, M and L to XXL)</li>
                        <li>Available Sizes: XS to XXL</li>
                        <li>Weight: 1,600 grams</li>
                        <li>Visor: High-resistance MT-V-28B visor with MT-QVSS (Quick Visor Swap System)</li>
                        <li>Sun Visor: Single-hand actuation sun visor</li>
                        <li>Safety: Designed from the ground up to exceed ECE 22.06 regulations</li>
                        <li>Closure: Micrometric MT-MDTC type with a double-tooth system</li>
                        <li>Vents: Triple front ventilation system and a rear spoiler with an air extraction system to enhance interior airflow</li>
                        <li>Removable Interiors: Breathable, removable and washable interior components</li>
                        <li>MT-NSIC Intercom: Housing for standard UCS intercom</li>
                        <li>Visor Insert Compatibility: Fogoff FOG010 and Pinlock DKS435</li>
                    </ul>
                </div>',
            ],
            [
                'title' => 'MT Braker SV Zebra B5 Matt Black Red',
                'price' => '£119.99',
                'image_1' => '/assets/img/accessories/helmets/3-1.jpg',
                'image_2' => '/assets/img/accessories/helmets/3-2.jpg',
                'description' => '<div>
                    <ul>
                        <li><strong>Drop Down Inner Sun Visor</strong></li>
                        <li><strong>Hi-Impact Absorption Inner Shell</strong></li>
                        <li><strong>Multiple Density EPS Liner</strong></li>
                        <li><strong>Optimum Visor Closing</strong></li>
                        <li><strong>Homologated Front Protection P</strong></li>
                        <li><strong>Quick Release Micrometric Buckle</strong></li>
                        <li><strong>2 Outer Shell Sizes</strong></li>
                        <li><strong>Pinlock MaxVision Visor</strong></li>
                        <li><strong>Removable and Washable Liner</strong></li>
                        <li><strong>Optically Correct Anti Scratch Polycarbonate Visor</strong></li>
                        <li><strong>Quick Release Visor</strong></li>
                        <li><strong>Aerodynamic Design</strong></li>
                        <li><strong>Vent System</strong></li>
                        <li><strong>ECE/ONU.22.05.P</strong></li>
                        <li><strong>NBR7471</strong></li>
                        <li><strong>NTC 4533</strong></li>
                        <li><strong>ACU Gold Standard</strong></li>
                        <li><strong>4 Star Sharp Rating</strong></li>
                    </ul>
                </div>',

            ],
            [
                'title' => 'MT Thunder 3 Turbine C8 Matt Pink',
                'price' => '£119.99',
                'image_1' => '/assets/img/accessories/helmets/4-2.jpg',
                'image_2' => '/assets/img/accessories/helmets/4-1.jpg',
                'description' => '<div>
                    <p>The new Braker SV is another 2024 innovation from MT Helmets. It represents an entry-level model in the sport-touring segment, featuring cutting-edge solutions such as the MT-QVSS visor attachment system, a housing for the UCS intercom, and a quick-actuating sun visor.</p>

                    <p>The Braker is a next-generation model designed to meet ECE 22.06 standards right from its inception, ensuring the highest level of safety at an affordable price point.</p>

                    <p>It boasts a triple front ventilation system and a rear spoiler with an air extraction system to enhance interior airflow.</p>

                    <h3>Key Features</h3>
                    <ul>
                        <li>Composition: HIRP - High Impact Resistance Polymer</li>
                        <li>Number of Shell Sizes: 2 (XS to M and L to XXL)</li>
                        <li>Number of EPS Liners: 3 (XS, S, M and L to XXL)</li>
                        <li>Available Sizes: XS to XXL</li>
                        <li>Weight: 1,600 grams</li>
                        <li>Visor: High-resistance MT-V-28B visor with MT-QVSS (Quick Visor Swap System)</li>
                        <li>Sun Visor: Single-hand actuation sun visor</li>
                        <li>Safety: Designed from the ground up to exceed ECE 22.06 regulations</li>
                        <li>Closure: Micrometric MT-MDTC type with a double-tooth system</li>
                        <li>Vents: Triple front ventilation system and a rear spoiler with an air extraction system to enhance interior airflow</li>
                        <li>Removable Interiors: Breathable, removable and washable interior components</li>
                        <li>MT-NSIC Intercom: Housing for standard UCS intercom</li>
                        <li>Visor Insert Compatibility: Fogoff FOG010 and Pinlock DKS435</li>
                    </ul>
                </div>',
            ],
            [
                'title' => 'MT Targo Doppler Matt Black & Pink',
                'price' => '£89.99',
                'image_1' => '/assets/img/accessories/helmets/5-1.jpg',
                'image_2' => '/assets/img/accessories/helmets/5-2.jpg',
                'description' => '<ul>
                    <li>Injected Thermoplastic Shell</li>
                    <li>1 Outer Shell Sizes</li>
                    <li>Hi-Impact Absorption Inner Shell</li>
                    <li>Multiple Density EPS Liner</li>
                    <li>Optimum Visor Closing</li>
                    <li>Homologated Front Protection P</li>
                    <li>Mircometric Buckle</li>
                    <li>Pinlock Max Vision Visor</li>
                    <li>Pinlock Ready</li>
                    <li>Removable and Washable Liner</li>
                    <li>Optically Correct Anti Scratch Polycarbonate Visor</li>
                    <li>Quick Release Visor</li>
                    <li>Pivoting Visor Mechanism</li>
                    <li>Aerodynamic Design</li>
                    <li>Vent System</li>
                    <li>ECE/ONU.22.05.P</li>
                    <li>ACU Gold Standard</li>
                </ul>',
            ],
            // Add other products here...
        ];
    }

    public function render()
    {
        return view('livewire.product-carousel-tab-based');
    }
}
