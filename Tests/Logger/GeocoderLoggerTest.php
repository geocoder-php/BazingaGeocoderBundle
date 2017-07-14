<?php

/**
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Bazinga\Bundle\GeocoderBundle\Tests\Logger;

use Bazinga\Bundle\GeocoderBundle\Logger\GeocoderLogger;
use Geocoder\Model\Address;
use Geocoder\Model\AddressCollection;
use Geocoder\Model\Bounds;
use Geocoder\Model\Country;

/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
class GeocoderLoggerTest extends \PHPUnit_Framework_TestCase
{
    protected $geocoderLogger;
    protected $result;
    protected $results;

    public function setUp()
    {
        $logger = $this->getMockBuilder('Psr\\Log\\LoggerInterface')->getMock();
        $logger
            ->expects($this->any())
            ->method('info')
            ->will($this->returnValue(null));

        $this->geocoderLogger = new GeocoderLogger($logger);

        $this->result = Address::createFromArray([
            'latitude'  => 1,
            'longitude' => 2,
            'bounds'    => [
                'south' => 1,
                'west'  => 2,
                'north' => 3,
                'east'  => 4,
            ],
            'streetNumber' => '10',
            'streetName'   => 'rue Gambetta',
            'locality'     => 'Paris',
            'postalCode'   => '75020',
            'country'      => 'France',
            'countryCode'  => 'FR',
        ]);

        $otherResult = Address::createFromArray([
            'latitude'  => 3,
            'longitude' => 4,
            'bounds'    => [
                'south' => 5,
                'west'  => 6,
                'north' => 7,
                'east'  => 8,
            ],
            'streetNumber' => '3',
            'streetName'   => 'avenue Secrétan',
            'locality'     => 'Paris',
            'postalCode'   => '75019',
            'country'      => 'France',
            'countryCode'  => 'FR',
        ]);
        $this->results = new AddressCollection([$this->result, $otherResult]);
    }

    public function testLogNoResults()
    {
        $this->geocoderLogger->logRequest('copenhagen', 0.123, 'FooProvider', new AddressCollection());

        $this->assertTrue(is_array($requests = $this->geocoderLogger->getRequests()));
        $this->assertCount(1, $requests);
        $this->assertTrue(is_array($request = $requests[0]));
        $this->assertSame($request['value'], 'copenhagen');
        $this->assertSame($request['duration'], 0.123);
        $this->assertSame($request['providerClass'], 'FooProvider');
        $this->assertSame($request['result'], '[]');
    }

    public function testLogResults()
    {
        $this->geocoderLogger->logRequest('copenhagen', 0.123, 'FooProvider', $this->results);

        $this->assertTrue(is_array($requests = $this->geocoderLogger->getRequests()));
        $this->assertCount(1, $requests);
        $this->assertTrue(is_array($request = $requests[0]));
        $this->assertSame($request['value'], 'copenhagen');
        $this->assertSame($request['duration'], 0.123);
        $this->assertSame($request['providerClass'], 'FooProvider');

        $this->assertContains('"latitude":1', $request['result']);
        $this->assertContains('"longitude":2', $request['result']);
        $this->assertContains('"streetNumber":"10"', $request['result']);
        $this->assertContains('"streetName":"rue Gambetta"', $request['result']);
        $this->assertContains('"postalCode":"75020"', $request['result']);
        $this->assertContains('"locality":"Paris"', $request['result']);
        $this->assertContains('"locality":"Paris"', $request['result']);
        $this->assertContains('"country":"France"', $request['result']);
        $this->assertContains('"countryCode":"FR"', $request['result']);
        $this->assertCount(2, json_decode($request['result']));
    }

    public function testLog2RequestWithMultipleResults()
    {
        $this->geocoderLogger->logRequest('copenhagen', 0.123, 'FooProvider', $this->results);
        $this->geocoderLogger->logRequest('paris', 0.456, 'BarProvider', $this->results);

        $this->assertTrue(is_array($requests = $this->geocoderLogger->getRequests()));
        $this->assertCount(2, $requests);

        $this->assertTrue(is_array($request = $requests[0]));
        $this->assertSame($request['value'], 'copenhagen');
        $this->assertCount(2, json_decode($request['result']));

        $this->assertTrue(is_array($request = $requests[1]));
        $this->assertSame($request['value'], 'paris');
        $this->assertCount(2, json_decode($request['result']));
    }

    public function testLogRequestsWithoutLogger()
    {
        $geocoderLogger = new GeocoderLogger();
        $geocoderLogger->logRequest('copenhagen', 0.123, 'FooProvider', $this->results);

        $this->assertTrue(is_array($requests = $geocoderLogger->getRequests()));
        $this->assertCount(1, $requests);
        $this->assertTrue(is_array($request = $requests[0]));
        $this->assertSame($request['value'], 'copenhagen');
        $this->assertSame($request['duration'], 0.123);
        $this->assertSame($request['providerClass'], 'FooProvider');

        $this->assertContains('"latitude":1', $request['result']);
        $this->assertContains('"longitude":2', $request['result']);
        $this->assertContains('"streetNumber":"10"', $request['result']);
        $this->assertContains('"streetName":"rue Gambetta"', $request['result']);
        $this->assertContains('"postalCode":"75020"', $request['result']);
        $this->assertContains('"locality":"Paris"', $request['result']);
        $this->assertContains('"locality":"Paris"', $request['result']);
        $this->assertContains('"country":"France"', $request['result']);
        $this->assertContains('"countryCode":"FR"', $request['result']);
    }
}
