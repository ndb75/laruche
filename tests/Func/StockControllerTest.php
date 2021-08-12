<?php

namespace App\Tests\Func;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class StockControllerTest extends AbstractTestController
{
    public function testPostStock()
    {
        $userRepository = static::getContainer()->get(UserRepository::class);

        // retrieve the test user
        $testUser = $userRepository->findOneByEmail('laruche@test.fr');

        // simulate $testUser being logged in
        $this->client->loginUser($testUser);

        copy(__DIR__.'/../../src/DataFixtures/data/test.csv', __DIR__.'/data/test.csv');

        $uploadedFile = new UploadedFile(
            __DIR__.'/data/test.csv',
            'test.csv'
        );
        $response = $this->client->request('POST', '/stock', [], [
            'file' => $uploadedFile
        ]);

        $response = json_decode($this->client->getResponse()->getContent());

        self::assertEquals(34, $response->averagePrice);
        self::assertEquals(90.45, $response->maxPrice);
        self::assertEquals(3, $response->minPrice);
        self::assertEquals(3, $response->nbCountry);
    }
}