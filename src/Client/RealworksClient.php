<?php

namespace App\Client;

use App\Contract\PropertyClientInterface;
use EasyRdf\Http;
use Error;
use Symfony\Contracts\HttpClient\Exception;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class RealworksClient implements PropertyClientInterface
{
    public function __construct(private HttpClientInterface $realworksClient)
    {
        $this->realworksClient = $realworksClient->withOptions([
            'base_uri' => 'https://api.realworks.nl',
            'headers' => ['Authorization' => $_ENV['REALWORKS_TOKEN']],
        ]);
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
            $req = $this->realworksClient->request('GET', '/wonen/v2/objecten');
        } catch (Exception\TransportExceptionInterface $e) {
            throw new Error('Something went wrong with the request'.$e);
        }

        return $req->getContent();
    }
}
