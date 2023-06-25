<?php

namespace App\Http\Controllers;

use App\Models\Application;
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

    public function downloadClient(Request $request)
    {
        $client = $request->client;
        $url = "https://apps-build.berkah-ts.my.id";

        $application = Application::where('client', $client)->where('type', 'apk')->first();

        if ($application) {
            return response()->json([
                'success' => true,
                'message' => 'File ditemukan!',
                'data' => [
                    'file' => $url . '/uploads/' . $application->file
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
