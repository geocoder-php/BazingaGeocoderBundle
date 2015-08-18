<?php

namespace Bazinga\Bundle\GeocoderBundle\Tests\Provider;

use Bazinga\Bundle\GeocoderBundle\Provider\Cache;
use Doctrine\Common\Cache\ArrayCache;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class CacheProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testGeocode()
    {
        $address = 'Paris, France';
        $coordinates = array('lat' => 48.857049,'lng' => 2.35223);
        $cacheKey = 'geocoder_'.sha1($address);

        $delegate = $this->getMock('Geocoder\\Provider\\Provider');
        $delegate->expects($this->once())
            ->method('geocode')
            ->with($address)
            ->will($this->returnValue($coordinates));

        $cache = $this->getMock('Doctrine\\Common\\Cache\\Cache');
        $cache->expects($this->once())
            ->method('fetch')
            ->with($cacheKey)
            ->will($this->returnValue(false));

        $cache->expects($this->once())
            ->method('save')
            ->with($cacheKey, serialize($coordinates), 0);

        $provider = new Cache($cache, $delegate);
        $this->assertEquals($coordinates, $provider->geocode($address));
    }

    public function testCachedGeocode()
    {
        $address = 'Paris, France';
        $coordinates = array('lat' => 48.857049,'lng' => 2.35223);
        $cacheKey = 'geocoder_'.sha1($address);

        $delegate = $this->getMock('Geocoder\\Provider\\Provider');
        $delegate->expects($this->once())
            ->method('geocode')
            ->with($address)
            ->will($this->returnValue($coordinates));

        $provider = new Cache($cache = new ArrayCache(), $delegate);

        $provider->geocode($address);
        $provider->geocode($address);

        $this->assertTrue($cache->contains($cacheKey));
    }

    public function testReverse()
    {
        $coordinates = array('lat' => 48.857049, 'lon' => 2.35223);

        $delegate = $this->getMock('Geocoder\\Provider\\Provider');
        $delegate->expects($this->once())
            ->method('reverse')
            ->with($coordinates['lat'], $coordinates['lon'])
            ->will($this->returnValue('Paris, France'));

        $cache = new ArrayCache();

        $provider = new Cache($cache, $delegate);

        $this->assertEquals('Paris, France', $provider->reverse($coordinates['lat'], $coordinates['lon']));
        $this->assertEquals('Paris, France', $provider->reverse($coordinates['lat'], $coordinates['lon']));
    }

    public function testGetName()
    {
        $delegate = $this->getMock('Geocoder\\Provider\\Provider');
        $cache = $this->getMock('Doctrine\\Common\\Cache\\Cache');

        $provider = new Cache($cache, $delegate);

        $this->assertEquals('cache', $provider->getName());
    }
}
