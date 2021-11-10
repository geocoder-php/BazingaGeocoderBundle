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
use Geocoder\Provider\AlgoliaPlaces\AlgoliaPlaces;
use Geocoder\Provider\ArcGISOnline\ArcGISOnline;
use Geocoder\Provider\BingMaps\BingMaps;
use Geocoder\Provider\Chain\Chain;
use Geocoder\Provider\FreeGeoIp\FreeGeoIp;
use Geocoder\Provider\Geoip\Geoip;
use Geocoder\Provider\GeoIP2\GeoIP2;
use Geocoder\Provider\GeoIPs\GeoIPs;
use Geocoder\Provider\Geonames\Geonames;
use Geocoder\Provider\GeoPlugin\GeoPlugin;
use Geocoder\Provider\GoogleMaps\GoogleMaps;
use Geocoder\Provider\GoogleMapsPlaces\GoogleMapsPlaces;
use Geocoder\Provider\Here\Here;
use Geocoder\Provider\HostIp\HostIp;
use Geocoder\Provider\IpInfo\IpInfo;
use Geocoder\Provider\IpInfoDb\IpInfoDb;
use Geocoder\Provider\Ipstack\Ipstack;
use Geocoder\Provider\LocationIQ\LocationIQ;
use Geocoder\Provider\Mapbox\Mapbox;
use Geocoder\Provider\MapQuest\MapQuest;
use Geocoder\Provider\Mapzen\Mapzen;
use Geocoder\Provider\MaxMind\MaxMind;
use Geocoder\Provider\MaxMindBinary\MaxMindBinary;
use Geocoder\Provider\Nominatim\Nominatim;
use Geocoder\Provider\OpenCage\OpenCage;
use Geocoder\Provider\PickPoint\PickPoint;
use Geocoder\Provider\TomTom\TomTom;
use Geocoder\Provider\Yandex\Yandex;
use Nyholm\BundleTest\BaseBundleTestCase;
use Nyholm\BundleTest\CompilerPass\PublicServicePass;
use Nyholm\NSA;
use Symfony\Bridge\PhpUnit\SetUpTearDownTrait;

class ProviderFactoryTest extends BaseBundleTestCase
{
    use SetUpTearDownTrait;

    protected function doSetUp(): void
    {
        $this->addCompilerPass(new PublicServicePass('|bazinga.*|'));
    }

    protected function getBundleClass()
    {
        return BazingaGeocoderBundle::class;
    }

    public function getProviders()
    {
        yield [AlgoliaPlaces::class, ['empty', 'acme']];
        yield [ArcGISOnline::class, ['empty', 'acme']];
        yield [BingMaps::class, ['acme']];
        yield [Chain::class, ['acme']];
        yield [FreeGeoIp::class, ['empty', 'acme']];
        //yield [Geoip::class, ['empty']];
        yield [GeoIP2::class, ['acme']];
        if (class_exists(GeoIPs::class)) {
            yield [GeoIPs::class, ['acme']];
        }
        yield [Geonames::class, ['acme']];
        yield [GeoPlugin::class, ['empty']];
        yield [GoogleMaps::class, ['empty']];
        yield [GoogleMapsPlaces::class, ['acme']];
        yield [Here::class, ['acme']];
        yield [HostIp::class, ['empty']];
        yield [IpInfo::class, ['acme']];
        yield [IpInfoDb::class, ['empty', 'acme']];
        yield [Ipstack::class, ['acme']];
        yield [LocationIQ::class, ['acme']];
        yield [Mapbox::class, ['acme']];
        yield [MapQuest::class, ['acme']];
        if (class_exists(Mapzen::class)) {
            yield [Mapzen::class, ['acme']];
        }
        yield [MaxMind::class, ['acme']];
        yield [MaxMindBinary::class, ['acme']];
        yield [Nominatim::class, ['empty', 'acme']];
        yield [OpenCage::class, ['acme']];
        yield [PickPoint::class, ['acme']];
        yield [TomTom::class, ['acme']];
        yield [Yandex::class, ['empty', 'acme']];
    }

    /**
     * @dataProvider getProviders
     */
    public function testProviderConfiguration($class, $serviceNames)
    {
        // Create a new Kernel
        $kernel = $this->createKernel();

        // Add some configuration
        $kernel->addConfigFile(__DIR__.'/config/provider/'.strtolower(substr($class, strrpos($class, '\\') + 1)).'.yml');

        // Boot the kernel as normal ...
        $this->bootKernel();
        $container = $this->getContainer();

        foreach ($serviceNames as $name) {
            $this->assertTrue($container->has('bazinga_geocoder.provider.'.$name));
            $service = $container->get('bazinga_geocoder.provider.'.$name);
            $this->assertInstanceOf($class, NSA::getProperty($service, 'provider'));
        }
    }
}
