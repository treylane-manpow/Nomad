<?php

namespace App\Apis\Salesforce;

use GuzzleHttp\Client;

class Authorization{
    private $token;
    private $instanceUrl;
    private $clientId;
    private $clientSecret;
    private $username;
    private $password;
    private $accessToken;
    private $apiVersion;
    private $environment;
    public function __construct(string $clientId,
                                string $clientSecret,
                                ?string $username,
                                ?string $password,
                                ?string $environment,
                                ?string $apiVersion
    )
    {
        $this->environment = $environment;
        $this->apiVersion = $apiVersion;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->username = $username;
        $this->password = $password;
    }
    public function getToken(){
        if($this->token == null){
            return $this->token = $this->getNewToken();
        }
        return $this->token;
    }
    public function getInstanceUrl(){
        if($this->instanceUrl == null) {
            $this->getNewToken();
            return $this->instanceUrl;
        }
        return $this->instanceUrl;
    }
    private function getNewToken(){
        $config = [
            'base_uri' => ""
        ];
        $this->client = new Client($config);
        $uri = "https://$this->environment.salesforce.com" . '/services/oauth2/token';
        $headers = [];
        $headers['Content-Type'] = 'application/json';
        $results = $this->client->post( $uri, [
            'headers' => [
                'Content-Type'  => 'application/x-www-form-urlencoded'
            ],
            'form_params' => [
                'grant_type'    => 'password',
                'client_id'     => $this->clientId,
                'client_secret' => $this->clientSecret,
                'username'      => $this->username,
                'password'      => $this->password
            ]

        ]);
        $auth = json_decode($results->getBody());
        $this->instanceUrl = $auth->instance_url;
        return $auth->access_token;
    }
}