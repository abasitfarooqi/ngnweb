<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): View
    {
        return view('fileUpload');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|mimes:pdf,xlx,csv,jpg,png|max:2048',
        ]);

        $fileName = time().'.'.$request->file->extension();

        Storage::disk('local')->put($fileName, 'Contents');
        // $request->file->move(public_path('uploads'), $fileName);

        /*
            Write Code Here for
            Store $fileName name in DATABASE from HERE
        */

        return back()
            ->with('success', 'You have successfully upload file.')
            ->with('file', $fileName);
    }
}
