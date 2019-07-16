<?php


namespace App\Services;


use Symfony\Component\HttpClient\HttpClient;

class TokenGenerator
{

    static public function generate($type="local"): string
    {
        if($type==="ibm"){
            $apiToken=explode(":",config('serverless.ibm_token'));
            $httpClient=HttpClient::create([
                'auth_basic' => [
                    $apiToken[0],
                    $apiToken[1]
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                ]
            ]);
            $response = $httpClient->request(
                'POST',
                'https://eu-gb.functions.cloud.ibm.com/api/v1/namespaces/narushevich.maksim%40gmail.com_dev/actions/php_base_api_generate_hash?blocking=true');
            $resp=json_decode($response->getContent());
            return $resp->response->result->token;
        }else{
            return bin2hex(random_bytes(32));
        }

    }
}
