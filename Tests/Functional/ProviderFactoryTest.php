<?php

declare(strict_types=1);

/*
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Bazinga\GeocoderBundle\Tests\Functional;

use Bazinga\GeocoderBundle\BazingaGeocoderBundle;
use Geocoder\Provider\ArcGISOnline\ArcGISOnline;
use Geocoder\Provider\BingMaps\BingMaps;
use Geocoder\Provider\Cache\ProviderCache;
use Geocoder\Provider\Chain\Chain;
use Geocoder\Provider\FreeGeoIp\FreeGeoIp;
use Geocoder\Provider\Geoip\Geoip;
use Geocoder\Provider\GeoIP2\GeoIP2;
use Geocoder\Provider\GeoIPs\GeoIPs;
use Geocoder\Provider\Geonames\Geonames;
use Geocoder\Provider\GeoPlugin\GeoPlugin;
use Geocoder\Provider\GoogleMaps\GoogleMaps;
use Geocoder\Provider\HostIp\HostIp;
use Geocoder\Provider\IpInfoDb\IpInfoDb;
use Geocoder\Provider\MapQuest\MapQuest;
use Geocoder\Provider\Mapzen\Mapzen;
use Geocoder\Provider\MaxMind\MaxMind;
use Geocoder\Provider\MaxMindBinary\MaxMindBinary;
use Geocoder\Provider\Provider;
use Geocoder\ProviderAggregator;
use Nyholm\BundleTest\BaseBundleTestCase;

class ProviderFactoryTest extends BaseBundleTestCase
{
    protected function getBundleClass()
    {
        return BazingaGeocoderBundle::class;
    }

    public function getProviders()
    {
        return [
            [ArcGISOnline::class, ['empty', 'acme']],
            [BingMaps::class, ['acme']],
            [Chain::class, ['acme']],
            [FreeGeoIp::class, ['empty', 'acme']],
            //[Geoip::class, ['empty']],
            [GeoIP2::class, ['acme']],
            [GeoIPs::class, ['acme']],
            [Geonames::class, ['acme']],
            [GeoPlugin::class, ['empty']],
            [GoogleMaps::class, ['empty']],
            [HostIp::class, ['empty']],
            [IpInfoDb::class, ['empty', 'acme']],
            [MapQuest::class, ['acme']],
            [Mapzen::class, ['acme']],
            [MaxMind::class, ['acme']],
            [MaxMindBinary::class, ['acme']],
        ];
    }

    /**
     * @dataProvider getProviders
     */
    public function testBundleWithOneProviderConfiguration($class, $serviceNames)
    {
        // Create a new Kernel
        $kernel = $this->createKernel();

        // Add some configuration
        $kernel->addConfigFile(__DIR__.'/config/provider/'.strtolower(substr($class, strrpos($class, '\\')+1)).'.yml');

        // Boot the kernel as normal ...
        $this->bootKernel();
        $container = $this->getContainer();

        foreach ($serviceNames as $name) {
            $this->assertTrue($container->has('bazinga_geocoder.provider.'.$name));
            $service = $container->get('bazinga_geocoder.provider.'.$name);
            $this->assertInstanceOf($class, $service);
        }
    }
}
