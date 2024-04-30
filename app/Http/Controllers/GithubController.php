<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Client;
use App\Models\Notification;
use Illuminate\Http\Request;

class GithubController extends Controller
{
    public function deploy(Request $request)
    {
        //read input as json
        $input = json_decode($request->getContent(), true);

        $client = Client::where('username', $input['client'])->first();

        if (!$client) {
            return response()->json([
                'success' => false,
                'message' => 'Client not found',
                'data' => []
            ]);
        }

        $aplikasi_apk = Application::where('client', $client->folder)->where('type', 'apk')->orderBy('id', 'desc')->first();
        $aplikasi_aab = Application::where('client', $client->folder)->where('type', 'aab')->orderBy('id', 'desc')->first();
        //cek versi aplikasi dengan format x.x.xx dan cek jika kurang dari versi itu maka tidak bisa diupload
        if ($input['type'] == 'apk') {
            $versi = explode('.', $input['version']);
            if (count($versi) != 3) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format versi salah',
                    'data' => []
                ]);
            }
            if ($aplikasi_apk) {
                $versi_terbaru = explode('.', $aplikasi_apk->version);
                //jika sama
                if ($versi[0] == $versi_terbaru[0] && $versi[1] == $versi_terbaru[1] && $versi[2] == $versi_terbaru[2]) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Versi tidak boleh sama dengan versi sebelumnya',
                        'data' => []
                    ]);
                }
                if ($versi[0] < $versi_terbaru[0]) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Versi tidak boleh lebih kecil dari versi sebelumnya',
                        'data' => []
                    ]);
                }
                if ($versi[1] < $versi_terbaru[1]) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Versi tidak boleh lebih kecil dari versi sebelumnya',
                        'data' => []
                    ]);
                }
                if ($versi[2] < $versi_terbaru[2]) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Versi tidak boleh lebih kecil dari versi sebelumnya',
                        'data' => []
                    ]);
                }
            }
        } else {
            $versi = explode('.', $input['version']);
            if (count($versi) != 3) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format versi salah',
                    'data' => []
                ]);
            }
            if ($aplikasi_aab) {
                $versi_terbaru = explode('.', $aplikasi_aab->version);
                //jika sama
                if ($versi[0] == $versi_terbaru[0] && $versi[1] == $versi_terbaru[1] && $versi[2] == $versi_terbaru[2]) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Versi tidak boleh sama dengan versi sebelumnya',
                        'data' => []
                    ]);
                }
                if ($versi[0] < $versi_terbaru[0]) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Versi tidak boleh lebih kecil dari versi sebelumnya',
                        'data' => []
                    ]);
                }
                if ($versi[1] < $versi_terbaru[1]) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Versi tidak boleh lebih kecil dari versi sebelumnya',
                        'data' => []
                    ]);
                }
                if ($versi[2] < $versi_terbaru[2]) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Versi tidak boleh lebih kecil dari versi sebelumnya',
                        'data' => []
                    ]);
                }
            }
        }

        $check = self::checkJobs();

        $result = json_decode($check->getContent(), true);

        $status = $result['data']['response'];
        if ($status != 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Mohon tunggu proses sebelumnya selesai',
                'data' => [
                    'response' => $status
                ]
            ]);
        }

        //make an post request to github to verify the payload
        $payload = [
            'ref' => 'FE-oto',
            'inputs' => [
                'name' => $input['name'],
                'icon' => $input['icon'] ?? null,
                'client' => $client->folder,
                'type' => $input['type'],
                'version' => $input['version'],
                'package' => $input['package'],
                'home' => 'Dari laravel',
            ]
        ];
        // $ch = curl_init('https://api.github.com/repos/jubercoding/juber.superatps/actions/workflows/50803826/dispatches');
        $ch = curl_init('https://api.github.com/repos/jubercoding/juber.superatps/actions/workflows/manual.yml/dispatches');

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        //set your github token here
        $token = "TOKEN";

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/vnd.github.v3+json',
            'Authorization: ' . $token,
            'User-Agent: jubercoding'
        ]);

        $response = curl_exec($ch);

        curl_close($ch);

        Notification::create([
            'client_id' => $client->id,
            'version' => $input['version'],
            'status' => '0',
            'type' => $input['type']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Deployed. Silahkan tunggu kurang lebih 25 - 40 menit untuk proses selesai. Link terbaru akan muncul di bawah ini',
            'data' => [
                'response' => $response,
            ]
        ]);
    }

    public function checkJobs()
    {

        // $ch = curl_init('https://api.github.com/repos/jubercoding/juber.superatps/actions/runs');
        $ch = curl_init('https://api.github.com/repos/jubercoding/juber.superatps/actions/runs');

        //get
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPGET, true);

        //set your github token here
        $token = "TOKEN";

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/vnd.github.v3+json',
            'Authorization: ' . $token,
            'User-Agent: jubercoding'
        ]);

        $response = curl_exec($ch);

        curl_close($ch);

        $response = json_decode($response, true);
        $status = $response['workflow_runs'][0]['status'];

        return response()->json([
            'success' => true,
            'message' => 'Status',
            'data' => [
                'response' => $status,
            ]
        ]);
    }

    public function createDownloadLink(Request $request)
    {
        $file_path = public_path('uploads/' . $request->file);

        if (file_exists($file_path)) {
            return response()->download($file_path);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'File not found',
                'data' => [
                    'file' => $request->file,
                ]
            ]);
        }
    }
}
