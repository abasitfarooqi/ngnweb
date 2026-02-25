<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use Illuminate\Http\Request;

class SubscriberController extends Controller
{
    public function store(Request $request)
    {
        $subscriber = new Subscriber;
        $subscriber->name = $request->input('name');
        $subscriber->email = $request->input('email');
        $subscriber->save();

        return redirect()->back()->with('success', 'Thanks for subscribing');
    }
}
