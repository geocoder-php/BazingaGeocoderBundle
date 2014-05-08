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
use Geocoder\Result\Geocoded;

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
        $logger = $this->getMock('Symfony\\Component\\HttpKernel\\Log\\LoggerInterface');
        $logger
            ->expects($this->any())
            ->method('info')
            ->will($this->returnValue(null))
        ;

        $this->geocoderLogger = new GeocoderLogger($logger);

        $this->result = new Geocoded;
        $this->result->fromArray(array(
            'latitude'  => 1,
            'longitude' => 2,
        ));

        $this->results = new \SplObjectStorage;
        $this->results->attach($this->result);

        $otherResult = new Geocoded;
        $otherResult->fromArray(array(
            'latitude'  => 3,
            'longitude' => 4,
        ));

        $this->results->attach($otherResult);
    }

    public function testLogNoResult()
    {
        $this->geocoderLogger->logRequest('copenhagen', 0.123, 'FooProvider', new Geocoded);

        $requests = $this->geocoderLogger->getRequests();

        $this->assertTrue(is_array($requests = $this->geocoderLogger->getRequests()));
        $this->assertCount(1, $requests);
        $this->assertTrue(is_array($request = $requests[0]));
        $this->assertSame($request['value'], 'copenhagen');
        $this->assertSame($request['duration'], 0.123);
        $this->assertSame($request['providerClass'], 'FooProvider');
        $this->assertSame($request['result'], '{"latitude":0,"longitude":0,"bounds":null,"streetNumber":null,"streetName":null,"zipcode":null,"city":null,"cityDistrict":null,"county":null,"countyCode":null,"region":null,"regionCode":null,"country":null,"countryCode":null,"timezone":null}');
    }

    public function testLogNoResults()
    {
        $this->geocoderLogger->logRequest('copenhagen', 0.123, 'FooProvider', new \SplObjectStorage);

        $this->assertTrue(is_array($requests = $this->geocoderLogger->getRequests()));
        $this->assertCount(1, $requests);
        $this->assertTrue(is_array($request = $requests[0]));
        $this->assertSame($request['value'], 'copenhagen');
        $this->assertSame($request['duration'], 0.123);
        $this->assertSame($request['providerClass'], 'FooProvider');
        $this->assertSame($request['result'], '[]');
    }

    public function testLogSingleResult()
    {
        $this->geocoderLogger->logRequest('copenhagen', 0.123, 'FooProvider', $this->result);

        $this->assertTrue(is_array($requests = $this->geocoderLogger->getRequests()));
        $this->assertCount(1, $requests);
        $this->assertTrue(is_array($request = $requests[0]));
        $this->assertSame($request['value'], 'copenhagen');
        $this->assertSame($request['duration'], 0.123);
        $this->assertSame($request['providerClass'], 'FooProvider');
        $this->assertSame($request['result'], '{"latitude":1,"longitude":2,"bounds":null,"streetNumber":null,"streetName":null,"zipcode":null,"city":null,"cityDistrict":null,"county":null,"countyCode":null,"region":null,"regionCode":null,"country":null,"countryCode":null,"timezone":null}');
    }

    public function testLog2RequestsWithSingleResult()
    {
        $this->geocoderLogger->logRequest('copenhagen', 0.123, 'FooProvider', $this->result);
        $this->geocoderLogger->logRequest('paris', 0.456, 'BarProvider', $this->result);

        $this->assertTrue(is_array($requests = $this->geocoderLogger->getRequests()));
        $this->assertCount(2, $requests);
        $this->assertTrue(is_array($request = $requests[0]));
        $this->assertSame($request['value'], 'copenhagen');
        $this->assertTrue(is_array($request = $requests[1]));
        $this->assertSame($request['value'], 'paris');
    }

    public function testLogMultipleResults()
    {
        $this->geocoderLogger->logRequest('copenhagen', 0.123, 'FooProvider', $this->results);

        $this->assertTrue(is_array($requests = $this->geocoderLogger->getRequests()));
        $this->assertCount(1, $requests);
        $this->assertTrue(is_array($request = $requests[0]));
        $this->assertSame($request['value'], 'copenhagen');
        $this->assertSame($request['duration'], 0.123);
        $this->assertSame($request['providerClass'], 'FooProvider');
        $this->assertSame($request['result'], '[{"latitude":1,"longitude":2,"bounds":null,"streetNumber":null,"streetName":null,"zipcode":null,"city":null,"cityDistrict":null,"county":null,"countyCode":null,"region":null,"regionCode":null,"country":null,"countryCode":null,"timezone":null},{"latitude":3,"longitude":4,"bounds":null,"streetNumber":null,"streetName":null,"zipcode":null,"city":null,"cityDistrict":null,"county":null,"countyCode":null,"region":null,"regionCode":null,"country":null,"countryCode":null,"timezone":null}]');
        $this->assertCount(2, json_decode($request['result']));
    }

    public function testLog2RequetsWithMultipleResults()
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
        $geocoderLogger = new GeocoderLogger;
        $geocoderLogger->logRequest('copenhagen', 0.123, 'FooProvider', $this->result);

        $this->assertTrue(is_array($requests = $geocoderLogger->getRequests()));
        $this->assertCount(1, $requests);
        $this->assertTrue(is_array($request = $requests[0]));
        $this->assertSame($request['value'], 'copenhagen');
        $this->assertSame($request['duration'], 0.123);
        $this->assertSame($request['providerClass'], 'FooProvider');
        $this->assertSame($request['result'], '{"latitude":1,"longitude":2,"bounds":null,"streetNumber":null,"streetName":null,"zipcode":null,"city":null,"cityDistrict":null,"county":null,"countyCode":null,"region":null,"regionCode":null,"country":null,"countryCode":null,"timezone":null}');
    }
}
