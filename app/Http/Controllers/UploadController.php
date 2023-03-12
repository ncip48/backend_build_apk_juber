<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function upload(Request $request)
    {
        $file = $request->file('file');
        //get filename and custom name
        $filename = $file->getClientOriginalName();
        $customName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        //move file to storage
        $file->move(public_path('uploads'), $customName);
        //return response
        return response()->json([
            'success' => true,
            'message' => 'File uploaded successfully',
            'data' => [
                'filename' => $filename,
                'customName' => $customName
            ]
        ]);
    }
}
