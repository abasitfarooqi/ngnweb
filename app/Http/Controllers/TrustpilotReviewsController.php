<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class TrustpilotReviewsController extends Controller
{
    public function getReviews()
    {
        $response = Http::get('https://api.trustpilot.com/v1/business-units/{businessUnitId}/reviews', [
            'apikey' => '123',
        ]);

        $reviews = $response->json();

        return view('frontend.home', ['reviews' => $reviews]);
    }
}
