<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Client;
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
        $upload = $file->move(public_path('uploads'), $customName);

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
            $client = Client::where('folder', $request->client)->first();
            Application::create([
                'name' => $client->name,
                'client' => $request->client,
                'type' => $request->type,
                'version' => $request->version,
                'package' => $request->package,
                'file' => $customName,
            ]);
            //update the version of the client
            if ($request->type == 'apk') {
                $client->version = $request->version;
            } else {
                $client->version_aab = $request->version;
            }
            $client->save();

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
