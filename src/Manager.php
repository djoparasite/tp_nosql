<?php

namespace App\Classes;

use MongoDB\Client;

/**
 * Class Manager
 * @package App\Classes
 */
class Manager
{

    // You have to change it every day
    const API_KEY = "21918178cf10baf619b096c35021291e";

    const BASE_URL_API = 'https://www.flickr.com/services/rest/?method=flickr.photos.search&api_key=' . self::API_KEY;
    const OPTIONS_API = '&format=json&nojsoncallback=1';

    /**
     * @var Client
     */
    private $client;

    /**
     * @var \GuzzleHttp\Client
     */
    private $guzzle;

    /**
     * Manager constructor.
     */
    public function __construct()
    {
        $this->client = new Client("mongodb://localhost:27017");
        $this->guzzle = new \GuzzleHttp\Client();
    }

    /**
     * @param $search
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle($search)
    {
        $response = $this->guzzle->request('GET', self::BASE_URL_API . '&tags=' . $search . self::OPTIONS_API);
        $images = \GuzzleHttp\json_decode($response->getBody(), true);

        $db = $this->client->selectDatabase("flickr");
        $imageRepository = $db->images;

        foreach ($images['photos']['photo'] as $image) {
            try {
                // try to stock img in mongo
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

    /**
     * @param $id
     * @return array
     * Get an image thanks to the ID's image from mongo
     */
    public function getPhotoById($id)
    {
        $db = $this->client->selectDatabase("flickr");

        $imageRepository = $db->images;
        $image = $imageRepository->find(['_id' => $id], ['_id' => 1, 'url' => 1]);

        return $image->toArray();
    }

    /**
     * @return array
     */
    public function getInfosDb()
    {
        $db = $this->client->selectDatabase("flickr");
        $imageRepository = $db->images;

        return [
            'dbname' => $db->getDatabaseName(),
            'countDocuments' => $imageRepository->countDocuments()
        ];
    }

    /**
     * @return int
     * Clear db collection images
     */
    public function clearDb()
    {
        $db = $this->client->selectDatabase("flickr");
        $imageRepository = $db->images;
        $imageRepository->deleteMany([]);

        return $imageRepository->countDocuments();
    }

    /**
     * @param $img
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getOtherInformations($img)
    {
        $response = $this->guzzle->request('GET', "https://www.flickr.com/services/rest/?method=flickr.photos.getInfo&api_key=" . self::API_KEY . "&photo_id=" . $img['_id'] . "&secret=" . $img['secret'] . self::OPTIONS_API);
        $informations = \GuzzleHttp\json_decode($response->getBody(), true);

        $db = $this->client->selectDatabase("flickr");
        $imageRepository = $db->images;

        // update document to add the owner's information
        $imageRepository->updateOne(['_id' => $img['_id']], ['$set' => ['owner' =>  $informations['photo']['owner']]]);

        // update document to add their dates
        foreach ($informations['photo']['dates'] as $key => $date) {
            try {
                // try to update document img in mongo
                $imageRepository->updateOne(['_id' => $img['_id']], ['$set' => [$key => $date]]);
            } catch (\Exception $exception) {
                continue;
            }
        }

        $image = $imageRepository->find(['_id' => $img['_id']], ['_id' => 1, 'url' => 1]);

        return $image->toArray();
    }
}
