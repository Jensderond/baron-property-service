<?php

namespace App\Client;

use App\Contract\PropertyClientInterface;
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
        $client = $this->realworksClient->withOptions([
            'base_uri' => 'https://api.realworks.nl',
            'headers' => ['Authorization' => $_ENV['REALWORKS_PROPERTY_TOKEN']],
        ]);

        return $this->fetchPaginated($client, '/wonen/v3/objecten?actief=all');
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
        $client = $this->realworksClient->withOptions([
            'base_uri' => 'https://api.realworks.nl',
            'headers' => ['Authorization' => $_ENV['REALWORKS_BOG_TOKEN']],
        ]);

        return $this->fetchPaginated($client, '/bog/v3/objecten?actief=all');
    }

    private function fetchPaginated(HttpClientInterface $client, string $baseUrl): string
    {
        $allResults = [];
        $offset = 0;
        $pageSize = 100;
        $maxIterations = 50;
        $separator = str_contains($baseUrl, '?') ? '&' : '?';

        for ($i = 0; $i < $maxIterations; $i++) {
            try {
                $url = $baseUrl . $separator . 'aantal=' . $pageSize . '&vanaf=' . $offset;
                $response = $client->request('GET', $url);
                $json = json_decode($response->getContent(), true);
            } catch (Exception\TransportExceptionInterface $e) {
                throw new Error('Something went wrong with the request' . $e);
            }

            $results = $json['resultaten'] ?? [];
            $allResults = array_merge($allResults, $results);

            if (count($results) < $pageSize) {
                break;
            }

            $offset += $pageSize;
        }

        return json_encode(['resultaten' => $allResults]);
    }
}
