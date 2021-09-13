<?php
namespace Helper;

use Codeception\Test\Unit as TestUnit;

class Unit extends \Codeception\Module
{
    /*
     * @param array @array
     * @return \Generator|[]
     */
    public function arrayAsGenerator(array $array)
    {
        yield $array;
    }

    public function getApiProvider($emptyApiResult = false)
    {
        $unit = new TestUnit();

        $apiProvider = $unit->getMockBuilder(\App\Provider\CustomersRandomUserApiProvider::class)
            ->disableOriginalConstructor()
            ->getMock();

        $apiProvider
            ->method('loadByNationality')
            ->with(\App\Provider\CustomersProviderInterface::NATIONALITY_AU, 100)
            ->willReturn($this->arrayAsGenerator(!$emptyApiResult ? $this->getCustomersArray() : []));

        return $apiProvider;

    }

    public function getCustomersArray()
    {
        return json_decode('[
            {
                "gender": "female",
                "name": {
                    "title": "Ms",
                    "first": "Melanie",
                    "last": "Harvey"
                },
                "location": {
                    "street": {
                        "number": 6414,
                        "name": "Thornridge Cir"
                    },
                    "city": "Cairns",
                    "state": "Northern Territory",
                    "country": "Australia",
                    "postcode": 3050,
                    "coordinates": {
                        "latitude": "-75.8103",
                        "longitude": "-149.5327"
                    },
                    "timezone": {
                        "offset": "-8:00",
                        "description": "Pacific Time (US & Canada)"
                    }
                },
                "email": "melanie.harvey@example.com",
                "login": {
                    "uuid": "40a355e8-d8e0-4f2b-b799-3b4486f5cc27",
                    "username": "happymeercat280",
                    "password": "marty",
                    "salt": "61KpXT4w",
                    "md5": "82b5f3b00b7aaf70b0c49859e06b311b",
                    "sha1": "cc60027042afc76af8a8b7af582938cf5f756e39",
                    "sha256": "607d4deac6a48fbb8eddcdbed3d5966cd9f9baaed0d036f4b5297808995b8a55"
                },
                "phone": "07-8489-0224"
            }
        ]', true);
    }
}
