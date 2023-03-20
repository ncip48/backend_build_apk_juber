<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DownloadController extends Controller
{
    public function download(Request $request)
    {
        $url = "https://apps-build.berkah-ts.my.id";
        $file_path = public_path('uploads/' . $request->file);

        if (file_exists($file_path)) {
            return response()->json([
                'success' => true,
                'message' => 'File ditemukan!',
                'data' => [
                    'file' => $url . '/uploads/' . $request->file
                ]
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'File tidak ditemukan!',
                'data' => []
            ]);
        }
    }
}
