<?php

namespace App\Traits;

use GuzzleHttp\Client;

/**
 * 
 */
trait ConsumeExternalServices
{
    public function makeRequest($method, $requestUrl, $queryParams = [], $form_params = [], $headers = [], $isJsonRequest = false)
    {
        $client = new Client([
            'base_uri' => $this->baseUri,

        ]);

        if (method_exists($this, 'resolveAuthorization')) {
            $this->resolveAuthorization($queryParams, $form_params, $headers);
        }
        // dd($headers);
        $response = $client->request($method, $requestUrl, [
            $isJsonRequest ? 'json' : 'form_params' => $form_params,
            'headers' => $headers,
            'query' => $queryParams,
        ]);
        $response = $response->getBody()->getContents();

        if (method_exists($this, 'decodeResponse')) {
            $response = $this->decodeResponse($response);
        }
        return $response;
    }
}
