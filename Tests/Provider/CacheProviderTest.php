<?php

namespace Bazinga\Bundle\GeocoderBundle\Tests\Provider;

use Bazinga\Bundle\GeocoderBundle\Provider\CacheProvider;
use Doctrine\Common\Cache\ArrayCache;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class CacheProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testGetGeocodedData()
    {
        $address = 'Paris, France';
        $coordinates = array('lat' => 48.857049,'lng' => 2.35223);

        $delegate = $this->getMock('Geocoder\\Provider\\ProviderInterface');
        $delegate->expects($this->once())
            ->method('getGeocodedData')
            ->with($address)
            ->will($this->returnValue($coordinates));

        $cache = $this->getMock('Doctrine\\Common\\Cache\\Cache');
        $cache->expects($this->once())
            ->method('fetch')
            ->with(crc32($address))
            ->will($this->returnValue(false));

        $cache->expects($this->once())
            ->method('save')
            ->with(crc32($address), serialize($coordinates), 0);

        $provider = new CacheProvider($cache, $delegate);
        $this->assertEquals($coordinates, $provider->getGeocodedData($address));
    }

    public function testGetCachedGeocodedData()
    {
        $address = 'Paris, France';
        $coordinates = array('lat' => 48.857049,'lng' => 2.35223);

        $delegate = $this->getMock('Geocoder\\Provider\\ProviderInterface');
        $delegate->expects($this->once())
            ->method('getGeocodedData')
            ->with($address)
            ->will($this->returnValue($coordinates));

        $provider = new CacheProvider($cache = new ArrayCache(), $delegate);

        $provider->getGeocodedData($address);
        $provider->getGeocodedData($address);

        $this->assertTrue($cache->contains(crc32($address)));
    }

    public function testGetReversedData()
    {
        $coordinates = array(48.857049, 2.35223);

        $delegate = $this->getMock('Geocoder\\Provider\\ProviderInterface');
        $delegate->expects($this->once())
            ->method('getReversedData')
            ->with($coordinates)
            ->will($this->returnValue('Paris, France'));

        $cache = new ArrayCache();

        $provider = new CacheProvider($cache, $delegate);

        $this->assertEquals('Paris, France', $provider->getReversedData($coordinates));
        $this->assertEquals('Paris, France', $provider->getReversedData($coordinates));
    }

    public function testGetName()
    {
        $delegate = $this->getMock('Geocoder\\Provider\\ProviderInterface');
        $cache = $this->getMock('Doctrine\\Common\\Cache\\Cache');

        $provider = new CacheProvider($cache, $delegate);

        $this->assertEquals('cache', $provider->getName());
    }
}
