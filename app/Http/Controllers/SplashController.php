<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SplashController extends Controller
{
    public function getSplash(Request $request)
    {
        $username = $request->username;

        //checks in folder public/splashscreen/username.jpg
        $path = public_path('splashscreen/' . $username . '.jpg');
        if (file_exists($path)) {
            return response()->json([
                'success' => true,
                'message' => 'Splashscreen ditemukan!',
                'data' => [
                    'splashscreen' => 'https://app.build.berkah-ts.my.id/splashscreen/' . $username . '.jpg'
                ]
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Splashscreen tidak ditemukan!',
                'data' => []
            ]);
        }
    }

    public function createSplash(Request $request)
    {
        $username = $request->username;

        //checks in folder public/splashscreen/username.jpg
        $path = public_path('splashscreen/' . $username . '.jpg');
        //if exist then modify with new splashscreen
        if (file_exists($path)) {
            $file = $request->file('file');
            //get filename and custom name
            $filename = $file->getClientOriginalName();
            $customName = $username . '.jpg';
            //move file to storage
            $upload = $file->move(public_path('splashscreen'), $customName);

            if (!$upload) {
                return response()->json([
                    'success' => false,
                    'message' => 'File failed to upload',
                    'data' => [
                        'filename' => $filename,
                        'customName' => $customName
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => true,
                    'message' => 'File uploaded',
                    'data' => [
                        'filename' => $filename,
                        'customName' => $customName
                    ]
                ]);
            }
        } else {
            $file = $request->file('file');
            //get filename and custom name
            $filename = $file->getClientOriginalName();
            $customName = $username . '.jpg';
            //move file to storage
            $upload = $file->move(public_path('splashscreen'), $customName);

            if (!$upload) {
                return response()->json([
                    'success' => false,
                    'message' => 'File failed to upload',
                    'data' => [
                        'filename' => $filename,
                        'customName' => $customName
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => true,
                    'message' => 'File uploaded',
                    'data' => [
                        'filename' => $filename,
                        'customName' => $customName
                    ]
                ]);
            }
        }
    }
}
