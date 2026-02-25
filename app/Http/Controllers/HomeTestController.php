<?php

namespace App\Http\Controllers;

class HomeTestController extends Controller
{
    public function index()
    {
        return view('hometest'); // Return the home view
    }
}
