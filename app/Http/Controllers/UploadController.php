<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;

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

    public function uploadLargeFiles(Request $request)
    {
        $receiver = new FileReceiver('file', $request, HandlerFactory::classFromRequest($request));

        if (!$receiver->isUploaded()) {
            // file not uploaded
            return response()->json([
                'success' => false,
                'message' => 'File failed to upload',
                'data' => []
            ]);
        }

        $fileReceived = $receiver->receive(); // receive file
        if ($fileReceived->isFinished()) { // file uploading is complete / all chunks are uploaded
            $file = $fileReceived->getFile(); // get file
            $extension = $file->getClientOriginalExtension();
            $fileName = str_replace('.' . $extension, '', $file->getClientOriginalName()); //file name without extenstion
            $fileName .= '_' . uniqid() . '.' . $extension; // a unique file name

            // $disk = Storage::disk(config('filesystems.default'));
            // $path = $disk->putFileAs('public/uploads', $file, $fileName);

            // $f = storage_path('upload/' . $fileName);

            //move storage/app/upload/$fileName to public/uploads
            $upload = $file->move(public_path('uploads'), $fileName);
            // File::move($f, public_path('uploads/' . $fileName));

            // delete chunked file
            unlink($file->getPathname());
            return response()->json([
                'success' => true,
                'message' => 'File uploaded',
                'data' => [
                    'filename' => $file,
                    'customName' => $fileName
                ]
            ]);
        }

        // otherwise return percentage information
        $handler = $fileReceived->handler();
        return [
            'done' => $handler->getPercentageDone(),
            'status' => true
        ];
    }
}
