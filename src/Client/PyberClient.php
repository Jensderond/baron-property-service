<?php

namespace App\Client;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Contract\PyberClientInterface;

class PyberClient implements PyberClientInterface {
    private $http;

    public function __construct(HttpClientInterface $pyberClient)
    {
        $this->http = $pyberClient;
    }

    public function getProperties()
    {
        $req = $this->http->request('GET', '');

        return $req->toArray();
    }
}
