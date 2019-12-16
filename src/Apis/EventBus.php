<?php

namespace App\Apis;

use App\Actions\PostToSlack;
use App\Entity\ApiCall;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class EventBus
{
    private $client;
    private $apiToken;
    private $url;
    private $em;
    private $slack;
    public function __construct(string $apiToken, string $eventBusUrl, EntityManagerInterface $em, PostToSlack $slack)
    {
        $this->url = $eventBusUrl;
        $this->apiToken = $apiToken;
        $this->em = $em;
        $this->client = new Client();
        $this->slack = $slack;
    }

    public function updateSalesforce(string $object, string $id, string $values) :ApiCall
    {
        $call = (new ApiCall())->setHttpType('POST')
                    ->setDestination('/master/update-salesforce')
                    ->setContent(json_encode([
                        'id'    =>  $id,
                        'object'    =>  $object,
                        'values'    =>  json_decode($values)
                    ]))
                    ->setDirection('out')
                    ->setHeaders([
                        'X-Authorization'   =>  $this->apiToken,
                        'Content-Type'      =>  'application/json'
                    ]);
        $this->em->persist($call);
        $this->em->flush();
        try{
            $response = $this->client->post($this->url . $call->getDestination(), [
                'headers'   =>  [
                    'X-Authorization'   =>  $this->apiToken,
                    'Content-Type'      =>  'application/json'
                ],
                RequestOptions::JSON    => json_decode($call->getContent())
            ]);
            $call->setResponse($response->getBody())->setResponseCode($response->getStatusCode());

        } catch(\Exception $e)
        {
            $call->setResponseCode($e->getCode())->setResponse($e->getMessage());
            $this->slack->text('[' .  $call->getId() . '] ' . $call->getResponse() . ': ' . $call->getResponseCode());

        }
        $this->em->persist($call);
        $this->em->flush();
        return $call;

    }
}