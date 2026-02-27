<?php

namespace App\Http\Controllers;

use App\Models\NgnCareer;

class CareerController extends Controller
{
    /**
     * Display a listing of careers.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Retrieve all careers from the database.
        $careers = NgnCareer::all(); // You might want to paginate or add filtering.

        return view('olders.frontend.ngnstore.careers', compact('careers'));
    }

    /**
     * Display the specified career.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        // Find a career by ID or throw a 404 error if not found.
        $career = NgnCareer::findOrFail($id);

        return view('olders.frontend.ngnstore.career-details', compact('career'));
    }
}
