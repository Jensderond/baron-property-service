<?php

namespace App\Client;

use App\Contract\PropertyClientInterface;
use Error;
use Symfony\Contracts\HttpClient\Exception;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PropertyClient implements PropertyClientInterface
{
    private HttpClientInterface $http;

    public function __construct(HttpClientInterface $pyberClient)
    {
        $this->http = $pyberClient;
    }

    /**
     * @throws Exception\ServerExceptionInterface
     * @throws Exception\RedirectionExceptionInterface
     * @throws Exception\ClientExceptionInterface
     * @throws Exception\TransportExceptionInterface
     */
    public function getProperties(): string
    {
        try {
            $req = $this->http->request('GET', '');
        } catch (Exception\TransportExceptionInterface $e) {
            throw new Error('Something went wrong with the request'.$e);
        }

        return $req->getContent();
    }
}
