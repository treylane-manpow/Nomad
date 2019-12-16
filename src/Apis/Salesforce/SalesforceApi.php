<?php

namespace App\Apis\Salesforce;

use GuzzleHttp\Client;

class SalesforceApi{

    public const USER_INFO_ENDPOINT = 'RESOURCE_OWNER';
    private const DEFAULT_API_VERSION = 'v38.0';
    private $clientId;
    private $clientSecret;
    private $username;
    private $password;
    private $accessToken;
    private $apiVersion;
    private $environment;
    private $oAuthAccessToken;
    private $salesforceAuthorization;
    private $client;
    public function __construct(  string $clientId,
                                  string $clientSecret,
                                  ?string $username,
                                  ?string $password,
                                  ?string $environment = null,
                                  ?string $apiVersion = null

    ) {
        if ($apiVersion === null) {
            $apiVersion = self::DEFAULT_API_VERSION;
        }
        $this->environment = $environment;
        $this->apiVersion = $apiVersion;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->username = $username;
        $this->password = $password;

        $this->salesforceAuthorization = new Authorization(
            $this->clientId,
            $this->clientSecret,
            $this->username,
            $this->password,
            $this->environment,
            $this->apiVersion
        );
        $config = [
            'base_uri' => $this->salesforceAuthorization->getInstanceUrl() . '/'
        ];
        $this->client = new Client($config);
    }

    public function query(string $string){
        $results = $this->http('/query/?q=' . $string);
        $response = json_decode($results->getBody());
        return $response;
    }
    public function nextUrl(string $string){
        $results = $this->rawHttp( $string);
        $response = json_decode($results->getBody());

        return $response;
    }
    private function http($uri) {
        return $this->rawHttp('services/data/'.$this->apiVersion. $uri);
    }
    private function rawHttp($uri){
        $headers = [];
        $headers['Content-Type'] = 'application/json';
        return $this->client->get($uri, [
            'headers' => [
                'Content-Type'  => 'application/x-www-form-urlencoded',
                'Authorization' =>  'Bearer ' . $this->salesforceAuthorization->getToken()
            ]

        ]);

    }
    public function delete(string $objectName, string $id){
        $headers = [];
        $headers['Content-Type'] = 'application/json';
        return $this->client->post('services/data/'.$this->apiVersion. "/sobjects/$objectName/$id?_HttpMethod=DELETE", [
            //    return $this->client->patch('http://webhook.site/c1f194fa-141d-4f11-bbac-39bdc083c71e', [
            'headers' => [
                'Content-Type'  => 'application/json',
                'Authorization' =>  'Bearer ' . $this->salesforceAuthorization->getToken()
            ]
        ]);
    }
    public function getObject(string $objectName, string $id, array $fields)
    {
        $results = $this->http("/sobjects/$objectName/$id" );
        $response = json_decode($results->getBody());
        return $response;

    }
    public function updateObject(string $objectName, string $id, array $payload)
    {

        $headers = [];
        $headers['Content-Type'] = 'application/json';
        return $this->client->post('services/data/'.$this->apiVersion. "/sobjects/$objectName/$id?_HttpMethod=PATCH", [
            //    return $this->client->patch('http://webhook.site/c1f194fa-141d-4f11-bbac-39bdc083c71e', [
            'headers' => [
                'Content-Type'  => 'application/json',
                'Authorization' =>  'Bearer ' . $this->salesforceAuthorization->getToken()
            ],
            \GuzzleHttp\RequestOptions::JSON   =>  $payload
        ]);
    }
    public function insertObject(string $objectName, array $payload)
    {
        $headers = [];
        $headers['Content-Type'] = 'application/json';
        return $this->client->post('services/data/'.$this->apiVersion. "/sobjects/$objectName?_HttpMethod=POST", [
            //    return $this->client->patch('http://webhook.site/c1f194fa-141d-4f11-bbac-39bdc083c71e', [
            'headers' => [
                'Content-Type'  => 'application/json',
                'Authorization' =>  'Bearer ' . $this->salesforceAuthorization->getToken()
            ],
            \GuzzleHttp\RequestOptions::JSON   =>  $payload
        ]);

    }
    public function describe(string $objectName)
    {
        $headers = [];
        $headers['Content-Type'] = 'application/json';
        return $this->client->get('services/data/'.$this->apiVersion. "/sobjects/$objectName/describe", [
            //    return $this->client->patch('http://webhook.site/c1f194fa-141d-4f11-bbac-39bdc083c71e', [
            'headers' => [
                'Content-Type'  => 'application/json',
                'Authorization' =>  'Bearer ' . $this->salesforceAuthorization->getToken()
            ]
        ]);

    }
}