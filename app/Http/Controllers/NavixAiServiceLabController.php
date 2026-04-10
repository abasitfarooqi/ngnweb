<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class NavixAiServiceLabController extends Controller
{
    public function __invoke(): View
    {
        return view('pages.navixai-service-lab', [
            'title' => 'NavixAI Service Lab | NGN Motors',
            'description' => 'One-page NGN services test hub with structured NavixAI-readable context and enquiry submission.',
        ]);
    }
}
