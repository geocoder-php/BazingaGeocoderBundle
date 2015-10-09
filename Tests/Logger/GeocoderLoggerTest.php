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
use Geocoder\Model\Coordinates;
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
        $logger = $this->getMock('Psr\\Log\\LoggerInterface');
        $logger
            ->expects($this->any())
            ->method('info')
            ->will($this->returnValue(null))
        ;

        $this->geocoderLogger = new GeocoderLogger($logger);

        $coordinates = new Coordinates(1, 2);
        $bounds = new Bounds(1, 2, 3, 4);
        $country = new Country('France', 'FR');
        $this->result = new Address($coordinates, $bounds, '10', 'rue Gambetta', '75020', 'Paris', null, null, $country);

        $coordinates = new Coordinates(3, 4);
        $bounds = new Bounds(5, 6, 7, 8);
        $country = new Country('France', 'FR');
        $otherResult = new Address($coordinates, $bounds, '3', 'avenue Secrétan', '75019', 'Paris', null, null, $country);

        $this->results = new AddressCollection(array($this->result, $otherResult));
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
        $this->assertSame($request['result'], '[{"latitude":1,"longitude":2,"bounds":{"south":1,"west":2,"north":3,"east":4},"streetNumber":"10","streetName":"rue Gambetta","postalCode":"75020","locality":"Paris","subLocality":null,"adminLevels":[],"country":"France","countryCode":"FR","timezone":null},{"latitude":3,"longitude":4,"bounds":{"south":5,"west":6,"north":7,"east":8},"streetNumber":"3","streetName":"avenue Secr\u00e9tan","postalCode":"75019","locality":"Paris","subLocality":null,"adminLevels":[],"country":"France","countryCode":"FR","timezone":null}]');
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
        $this->assertSame($request['result'], '[{"latitude":1,"longitude":2,"bounds":{"south":1,"west":2,"north":3,"east":4},"streetNumber":"10","streetName":"rue Gambetta","postalCode":"75020","locality":"Paris","subLocality":null,"adminLevels":[],"country":"France","countryCode":"FR","timezone":null},{"latitude":3,"longitude":4,"bounds":{"south":5,"west":6,"north":7,"east":8},"streetNumber":"3","streetName":"avenue Secr\u00e9tan","postalCode":"75019","locality":"Paris","subLocality":null,"adminLevels":[],"country":"France","countryCode":"FR","timezone":null}]');
    }
}
