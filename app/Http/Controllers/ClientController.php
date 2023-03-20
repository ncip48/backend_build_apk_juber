<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function getRecentApk(Request $request)
    {
        $client = $request->client;

        $client = Client::where('username', $client)->first();

        if (!$client) {
            return response()->json([
                'success' => false,
                'message' => 'Client not found',
                'data' => []
            ]);
        }

        $terbaru = Application::where('client', $client->folder)->orderBy('id', 'desc')->first();

        $datas = Application::where('client', $client->folder)->get();

        return response()->json([
            'success' => true,
            'message' => 'Status',
            'data' => [
                'terbaru' => $terbaru,
                'semua' => $datas,
            ],
        ]);
    }

    public function getProfile(Request $request)
    {
        $client = $request->client;

        $client = Client::where('username', $client)->first();

        if (!$client) {
            return response()->json([
                'success' => false,
                'message' => 'Client not found',
                'data' => []
            ]);
        }

        $terbaru = Application::where('client', $client->folder)->orderBy('id', 'desc')->first();

        $datas = Application::where('client', $client->folder)->get();

        //get link download

        //get the real url
        $url = $request->getSchemeAndHttpHost();
        $terbaru->link = $url . '/uploads/' . $terbaru->file;

        $datas = $datas->map(function ($item) use ($url) {
            $item->link = $url . '/uploads/' . $item->file;
            return $item;
        });

        return response()->json([
            'success' => true,
            'message' => 'Sukses mengambil data',
            'data' => [
                'client' => $client,
                'apk' => [
                    'terbaru' => $terbaru,
                    'semua' => $datas,
                ]
            ],
        ]);
    }
}
