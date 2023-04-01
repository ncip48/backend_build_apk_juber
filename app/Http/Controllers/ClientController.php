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

        $terbaru_apk = Application::where('client', $client->folder)->where('type', 'apk')->orderBy('id', 'desc')->first();
        $terbaru_aab = Application::where('client', $client->folder)->where('type', 'aab')->orderBy('id', 'desc')->first();

        $datas = Application::where('client', $client->folder)->get();

        //get link download

        //get the real url
        $url = "https://apps-build.berkah-ts.my.id";

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
                    'terbaru' => [
                        'apk' => $terbaru_apk,
                        'aab' => $terbaru_aab,
                    ],
                    'semua' => $datas,
                ]
            ],
        ]);
    }

    public function getClients()
    {
        $clients = Client::all();

        return response()->json([
            'success' => true,
            'message' => 'Sukses mengambil data',
            'data' => $clients,
        ]);
    }

    public function createClient(Request $request)
    {
        //retrieve json data
        $input = json_decode($request->getContent(), true);

        $client = Client::where('username', $input['username'])->first();

        if ($client) {
            return response()->json([
                'success' => false,
                'message' => 'Client sudah ada',
                'data' => []
            ]);
        }

        Client::create($input);

        return response()->json([
            'success' => true,
            'message' => 'Sukses membuat client',
            'data' => $client,
        ]);
    }

    public function editClient(Request $request, $username)
    {
        //retrieve json data
        $input = json_decode($request->getContent(), true);

        $client = Client::where('username', $username)->first();

        if (!$client) {
            return response()->json([
                'success' => false,
                'message' => 'Client tidak ditemukan',
                'data' => []
            ]);
        }

        //all input json
        Client::where('username', $username)->update($input);

        return response()->json([
            'success' => true,
            'message' => 'Sukses mengubah client',
            'data' => $client,
        ]);
    }
}
