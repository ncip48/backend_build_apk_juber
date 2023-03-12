<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GithubController extends Controller
{
    public function deploy(Request $request)
    {
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
        //read input as json
        $input = json_decode($request->getContent(), true);

        //make an post request to github to verify the payload
        $payload = [
            'ref' => 'ci/cd',
            'inputs' => [
                'name' => $input['name'],
                'client' => $input['client'],
                'type' => $input['type'],
                'version' => $input['version'],
                'package' => 'default',
                'home' => 'Dari laravel',
            ]
        ];
        $ch = curl_init('https://api.github.com/repos/jubercoding/juber.superatps/actions/workflows/50803826/dispatches');

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        //set your github token here
        $token = "Bearer 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";

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/vnd.github.v3+json',
            'Authorization: ' . $token,
            'User-Agent: jubercoding'
        ]);

        $response = curl_exec($ch);

        curl_close($ch);

        return response()->json([
            'success' => true,
            'message' => 'Deployed',
            'data' => [
                'response' => $response,
            ]
        ]);
    }

    public function checkJobs()
    {

        $ch = curl_init('https://api.github.com/repos/jubercoding/juber.superatps/actions/runs');

        //get
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPGET, true);

        //set your github token here
        $token = "Bearer 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";

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
}
