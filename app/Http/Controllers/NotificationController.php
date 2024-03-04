<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{

    public function all(Request $request)
    {
        $client = $request->client;
        $client = Client::where('username', $client)->first();
        $notif = Notification::where('client_id', $client->id)->get();

        return response()->json([
            'success' => true,
            'message' => 'Data found',
            'data' => $notif
        ]);
    }

    public function index(Request $request)
    {
        $client = $request->client;
        $client = Client::where('username', $client)->first();

        if ($client) {
            //latest notification
            $notif = Notification::where('client_id', $client->id)->latest()->first();

            return response()->json([
                'success' => true,
                'message' => 'Data found',
                'data' => $notif
            ]);
        } else {
            return response()->json(['message' => 'Client not found'], 404);
        }
    }

    public function create(Request $request)
    {
        $client = $request->client;
        $version = $request->version;
        $status = $request->status;

        $client = Client::where('username', $client)->first();

        if ($client) {
            Notification::create([
                'client_id' => $client->id,
                'version' => $version,
                'status' => $status,
            ]);
            return response()->json(['message' => 'Notification sent'], 200);
        } else {
            return response()->json(['message' => 'Client not found'], 404);
        }
    }
}
