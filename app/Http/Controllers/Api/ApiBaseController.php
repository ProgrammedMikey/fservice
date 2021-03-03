<?php

namespace App\Http\Controllers\Api;

use Laravel\Lumen\Routing\Controller as BaseController;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ApiBaseController extends BaseController
{
    public $client;

    public function guzzleClient(Request $request)
    {
         $this->validate($request, [
            'FRESHSERVICE_API_KEY'   => 'required|string',
        ]);

        // $internalAPI =  env('FRESHSERVICE_API_KEY');
        // $freshServiceApiKey = request()->header('FRESHSERVICE_API_KEY');
        $freshServiceApiKey =  $request->FRESHSERVICE_API_KEY;
                
        $username = $freshServiceApiKey;
        $password = 'x';
        $credentials = base64_encode("$username:$password");

        $fsURL = env('FRESHSERVICE_DOMAIN');
        $client = new Client([
            'base_uri' => $request->url,
            // 'base_url' => $fsURL,
            'headers' => [
                'Authorization' => 'Basic '.$credentials,
                'Content-Type'  => 'application/json'
            ],
        ]);
        return $client;
    }
}
