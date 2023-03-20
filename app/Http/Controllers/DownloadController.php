<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DownloadController extends Controller
{
    public function download(Request $request)
    {
        $file_path = public_path('uploads/' . $request->file);

        if ($file_path) {
            return response()->download($file_path);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'File tidak ditemukan!',
                'data' => []
            ]);
        }
    }
}
