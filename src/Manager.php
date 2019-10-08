<?php

namespace App\Classes;

use MongoDB\Client;

class Manager
{
    const BASE_URL_API = 'https://www.flickr.com/services/rest/?method=flickr.photos.search&api_key=8ace1d78f867284ec1756b03727c8b7d';
    const OPTIONS_API = '&format=json&nojsoncallback=1';

    private $client;
    private $guzzle;

    public function __construct()
    {
        $this->client = new Client("mongodb://localhost:27017");
        $this->guzzle = new \GuzzleHttp\Client();
    }

    public function handle($search)
    {
        $response = $this->guzzle->request('GET', self::BASE_URL_API . '&tags=' . $search . self::OPTIONS_API);
        $images = \GuzzleHttp\json_decode($response->getBody(), true);

        $db = $this->client->selectDatabase("flickr");
        $imageRepository = $db->images;

        foreach ($images['photos']['photo'] as $image) {
            try {
                $imageRepository->insertOne( ['_id' => $image['id'], 'secret' => $image['secret'] , 'tags' => $search, 'url' => 'https://farm' . $image['farm'] . '.staticflickr.com/' . $image['server'] . '/' . $image['id'] . '_'. $image['secret'] .'.jpg'] );
            } catch (\Exception $exception) {
                continue;
            }
        }

        $cursor = $imageRepository->find(
            [
                'tags' => $search,
            ],
            [
                'limit' => 20,
                'projection' => [
                    'url' => 1,
                ],
            ]
        );

        return [
            'cursor' => $cursor->toArray(),
            'countDocuments' => $imageRepository->countDocuments()
        ];
    }

    public function getPhotoById($id)
    {
        $db = $this->client->selectDatabase("flickr");
        $imageRepository = $db->images;
        $image = $imageRepository->find(['_id' => $id], ['_id' => 1, 'url' => 1]);

        return $image->toArray();
    }

    public function getInfosDb()
    {
        $db = $this->client->selectDatabase("flickr");
        $imageRepository = $db->images;

        return [
            'dbname' => $db->getDatabaseName(),
            'countDocuments' => $imageRepository->countDocuments()
        ];
    }

    public function clearDb()
    {
        $db = $this->client->selectDatabase("flickr");
        $imageRepository = $db->images;
        $imageRepository->deleteMany([]);

        return $imageRepository->countDocuments();
    }
}
