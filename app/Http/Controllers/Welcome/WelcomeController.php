<?php

namespace App\Http\Controllers\Welcome;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\Motorbike;
use App\Models\Motorcycle;

class WelcomeController extends Controller
{
    public function HomeMain()
    {
        return view('frontend.index');
    }

    public function BikesForSaleHome()
    {
        // Fetch all columns for the latest new bikes using the Motorcycle model
        $latestNewBikes = Motorcycle::select('*')
            ->where('availability', '=', 'for sale')
            ->orderBy('created_at', 'desc')
            ->limit(12)
            ->get();

        // Fetch the latest used bikes using the Motorbike model
        $latestUsedBikes = Motorbike::join('motorbikes_sale', 'motorbikes.id', '=', 'motorbikes_sale.motorbike_id')
            ->select('motorbikes.*', 'motorbikes_sale.price', 'motorbikes_sale.image_one')
            ->where('motorbikes_sale.is_sold', 0) // Only available bikes
            ->orderBy('motorbikes.created_at', 'desc')
            ->limit(12)
            ->get();

        $blogPosts = BlogPost::with(['category', 'images'])
            ->where('slug', '!=', '')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        // Pass the motorcycle data to the view
        return view('frontend.index', compact('latestNewBikes', 'latestUsedBikes', 'blogPosts'));
    }

    public function BikesForSale()
    {
        // Fetch all columns for the latest new bikes using the Motorcycle model
        $latestNewBikes = Motorcycle::select('*')
            ->where('availability', '=', 'for sale')
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        // Fetch the latest used bikes using the Motorbike model
        $latestUsedBikes = Motorbike::join('motorbikes_sale', 'motorbikes.id', '=', 'motorbikes_sale.motorbike_id')
            ->select('motorbikes.*', 'motorbikes_sale.price', 'motorbikes_sale.image_one')
            ->where('motorbikes_sale.is_sold', 0) // Only available bikes
            ->orderBy('motorbikes.created_at', 'desc')
            ->limit(8)
            ->get();

        return view('frontend.motorcycle-sales', compact('latestNewBikes', 'latestUsedBikes'));
    }

    // public function BikesForSale()
    // {
    //     return view('frontend.motorcycle-sales');
    // }

    // newly added
    public function accessories()
    {
        return view('frontend.ngnstore.accessories');
    }

    public function RentInformation()
    {
        return view('frontend.rentals-information');
    }

    public function GetServices()
    {
        return view('frontend.motorbike-motorcycle-servicing-repairs-london');
    }

    public function AllGetServices()
    {
        return view('frontend.all-services-main-page');
    }

    public function Repairs()
    {
        return view('frontend.service-repairs');
    }

    public function RepairServices()
    {
        return view('frontend.repairs.RepairServices');
    }

    public function ServiceBike()
    {
        return view('frontend.service-motorcycle');
    }

    public function ServiceMot()
    {
        return view('frontend.service-mot');
    }

    public function MotorcycleShop()
    {
        return view('frontend.shop-motorcycle');
    }

    public function MotorcycleAccessories()
    {
        return view('frontend.shop-accessories');
    }

    public function AccidentClaim()
    {
        return view('frontend.accidents');
    }

    public function GetProducts()
    {
        return view('frontend.shop');
    }

    public function GpsTracker()
    {
        return view('frontend.gps-tracker');
    }

    public function SpareParts()
    {
        return view('frontend.spare-parts');
    }

    public function AboutMethod()
    {
        return view('frontend.about_page');
    }

    public function ContactMethod()
    {
        return view('contact');
    }

    // LEGALS
    public function CookiePrivacyPolicy()
    {
        return view('frontend.legals.cookies-and-privacy-policy');
    }

    public function TermsOfUse()
    {
        return view('frontend.legals.terms-of-service');
    }

    public function ShippingPolicy()
    {
        return view('frontend.legals.shipping-policy');
    }

    public function RefundPolicy()
    {
        return view('frontend.legals.refund-policy');
    }

    public function returnPolicy()
    {
        return view('frontend.legals.return-policy'); // Return the view for the return policy
    }

    public function RepairsIndex()
    {
        return view('frontend.repairs.index'); // Main repairs page
    }

    public function BasicServices()
    {
        return view('frontend.repairs.BasicServices'); // Basic services page
    }

    public function MajorServices()
    {
        return view('frontend.repairs.MajorServices'); // Major services page
    }

    public function ServiceComparison()
    {
        return view('frontend.repairs.ServiceComparison'); // Service comparison page
    }

    public function SoonCome()
    {
        return view('coming-soon.soon-come');
    }
}
