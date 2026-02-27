<?php

namespace App\Http\Controllers\Welcome;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

/**
 * Legacy WelcomeController — all frontend routes now served by Livewire Site\* components.
 * These methods issue permanent 301 redirects to the correct new URLs so old bookmarks / links don't break.
 */
class WelcomeController extends Controller
{
    public function HomeMain()
    {
        return redirect('/', 301);
    }

    public function BikesForSaleHome()
    {
        return redirect('/', 301);
    }

    public function BikesForSale()
    {
        return redirect('/bikes', 301);
    }

    public function accessories()
    {
        return redirect('/accessories', 301);
    }

    public function RentInformation()
    {
        return redirect('/rentals', 301);
    }

    public function GetServices()
    {
        return redirect('/repairs', 301);
    }

    public function AllGetServices()
    {
        return redirect('/repairs', 301);
    }

    public function Repairs()
    {
        return redirect('/repairs', 301);
    }

    public function RepairServices()
    {
        return redirect('/motorbike-repair-services', 301);
    }

    public function ServiceBike()
    {
        return redirect('/repairs', 301);
    }

    public function ServiceMot()
    {
        return redirect('/mot', 301);
    }

    public function MotorcycleShop()
    {
        return redirect('/shop', 301);
    }

    public function MotorcycleAccessories()
    {
        return redirect('/accessories', 301);
    }

    public function AccidentClaim()
    {
        return redirect('/recovery', 301);
    }

    public function GetProducts()
    {
        return redirect('/shop', 301);
    }

    public function GpsTracker()
    {
        return redirect('/gps-tracker', 301);
    }

    public function SpareParts()
    {
        return redirect('/spare-parts', 301);
    }

    public function AboutMethod()
    {
        return redirect('/about', 301);
    }

    public function ContactMethod()
    {
        return redirect('/contact', 301);
    }

    public function CookiePrivacyPolicy()
    {
        return redirect('/cookie-policy', 301);
    }

    public function TermsOfUse()
    {
        return redirect('/terms-and-conditions', 301);
    }

    public function ShippingPolicy()
    {
        return redirect('/shipping-policy', 301);
    }

    public function RefundPolicy()
    {
        return redirect('/refund-policy', 301);
    }

    public function returnPolicy()
    {
        return redirect('/return-policy', 301);
    }

    public function RepairsIndex()
    {
        return redirect('/repairs', 301);
    }

    public function BasicServices()
    {
        return redirect('/motorbike-basic-service-london', 301);
    }

    public function MajorServices()
    {
        return redirect('/motorbike-full-service-london', 301);
    }

    public function ServiceComparison()
    {
        return redirect('/motorbike-service-comparison', 301);
    }

    public function SoonCome()
    {
        return redirect('/coming-soon', 301);
    }
}
