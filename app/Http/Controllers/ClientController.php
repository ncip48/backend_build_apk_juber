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

        if ($client->icon) {
            $client->icon = $url . '/icons/' . $client->icon;
        } else {
            $client->icon = null;
        }

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

        $client = Client::create($input);

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

        $client = Client::where('username', $username)->first();

        return response()->json([
            'success' => true,
            'message' => 'Sukses mengubah client',
            'data' => $client,
        ]);
    }

    public function deleteClient($username)
    {
        $client = Client::where('username', $username)->first();

        if (!$client) {
            return response()->json([
                'success' => false,
                'message' => 'Client tidak ditemukan',
                'data' => []
            ]);
        }

        Client::where('username', $username)->delete();

        $client = Client::all();

        return response()->json([
            'success' => true,
            'message' => 'Sukses menghapus client',
            'data' => $client,
        ]);
    }

    public function changeIcon(Request $request)
    {

        //validasi icon harus berupa png dan nullable
        $request->validate([
            'icon' => 'nullable|mimes:png',
        ]);

        //check validation
        if ($request->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Icon harus berupa png',
                'data' => []
            ]);
        }

        $client = Client::where('username', $request->client)->first();

        if (!$client) {
            return response()->json([
                'success' => false,
                'message' => 'Client tidak ditemukan',
                'data' => []
            ]);
        }


        //if client already has icon, delete it
        if ($request->has('icon')) {
            if ($client->icon) {
                unlink(public_path() . '/icons/' . $client->icon);
            }
            $icon_name = time() . '_' . rand(1000, 9999) . '.png';
            $icon = $request->file('icon');
            $icon->move(public_path() . '/icons/', $icon_name);
        }

        $client = Client::where('username', $request->client)->first();
        //update client icon
        Client::where('username', $request->client)->update([
            'icon' => $request->has('icon') ? $icon_name : $client->icon,
            'name' => $request->name,
        ]);

        $client = Client::where('username', $request->client)->first();
        $client->icon = 'https://apps-build.berkah-ts.my.id/icons/' . $client->icon;

        return response()->json([
            'success' => true,
            'message' => 'Sukses mengubah icon',
            'data' => $client,
        ]);
    }
}
