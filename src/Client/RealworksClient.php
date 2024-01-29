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
        $this->realworksClient = $realworksClient;
    }

    /**
     * @throws Exception\ServerExceptionInterface
     * @throws Exception\RedirectionExceptionInterface
     * @throws Exception\ClientExceptionInterface
     * @throws Exception\TransportExceptionInterface
     */
    public function getProperties(): string
    {
        $this->realworksClient = $this->realworksClient->withOptions([
            'base_uri' => 'https://api.realworks.nl',
            'headers' => ['Authorization' => $_ENV['REALWORKS_PROPERTY_TOKEN']],
        ]);
        try {
            $req = $this->realworksClient->request('GET', '/wonen/v2/objecten?actief=all');
        } catch (Exception\TransportExceptionInterface $e) {
            throw new Error('Something went wrong with the request'.$e);
        }

        return $req->getContent();
    }

    /**
     * @throws Exception\ServerExceptionInterface
     * @throws Exception\RedirectionExceptionInterface
     * @throws Exception\ClientExceptionInterface
     * @throws Exception\TransportExceptionInterface
     */
    public function getProjects(): string
    {
        $this->realworksClient = $this->realworksClient->withOptions([
            'base_uri' => 'https://api.realworks.nl',
            'headers' => ['Authorization' => $_ENV['REALWORKS_PROJECT_TOKEN']],
        ]);
        try {
            $req = $this->realworksClient->request('GET', '/nieuwbouw/v2/projecten');
        } catch (Exception\TransportExceptionInterface $e) {
            throw new Error('Something went wrong with the request'.$e);
        }

        return $req->getContent();
    }

    /**
     * @throws Exception\ServerExceptionInterface
     * @throws Exception\RedirectionExceptionInterface
     * @throws Exception\ClientExceptionInterface
     * @throws Exception\TransportExceptionInterface
     */
    public function getBogObjects(): string
    {
        $this->realworksClient = $this->realworksClient->withOptions([
            'base_uri' => 'https://api.realworks.nl',
            'headers' => ['Authorization' => $_ENV['REALWORKS_BOG_TOKEN']],
        ]);
        try {
            $req = $this->realworksClient->request('GET', '/bog/v2/objecten?actief=all');
        } catch (Exception\TransportExceptionInterface $e) {
            throw new Error('Something went wrong with the request'.$e);
        }

        return $req->getContent();
    }
}
