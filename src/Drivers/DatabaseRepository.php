<?php

namespace Crumbls\WordPress\Drivers;

use Crumbls\WordPress\Contracts\WordPressDriver;
use GuzzleHttp\Client;

class DatabaseRepository implements WordPressDriver
{
protected $baseUri;
protected $client;

public function __construct($baseUri)
{
$this->baseUri = $baseUri;
$this->client = new Client();
}

public function getPosts($perPage = 10)
{
$url = $this->baseUri . 'posts';
$response = $this->client->get($url, ['query' => ['per_page' => $perPage]]);

return json_decode($response->getBody(), true);
}

// Implement other methods from the WordPressDriver interface for additional endpoints or custom requests.
}