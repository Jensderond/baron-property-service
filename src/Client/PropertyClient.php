<?php

namespace App\Client;

use App\Contract\PropertyClientInterface;
use Error;
use Symfony\Contracts\HttpClient\Exception;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PropertyClient implements PropertyClientInterface
{
    public function __construct(private readonly HttpClientInterface $pyberClient) {}

    /**
     * @throws Exception\ServerExceptionInterface
     * @throws Exception\RedirectionExceptionInterface
     * @throws Exception\ClientExceptionInterface
     * @throws Exception\TransportExceptionInterface
     */
    public function getProperties(): string
    {
        try {
            $req = $this->pyberClient->request('GET', '');
        } catch (Exception\TransportExceptionInterface $e) {
            throw new Error('Something went wrong with the request'.$e);
        }

        return $req->getContent();
    }
}
