<?php

namespace App\Http\Controllers;

class FileController extends Controller
{
    public function show($path)
    {
        $filePath = storage_path('app/'.$path);

        if (file_exists($filePath)) {
            return response()->file($filePath);
        } else {
            abort(404);
        }
    }
}
