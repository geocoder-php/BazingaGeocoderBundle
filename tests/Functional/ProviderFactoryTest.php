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
use Geocoder\Provider\GeoIP2\GeoIP2;
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
use Geocoder\Provider\MaxMind\MaxMind;
use Geocoder\Provider\Nominatim\Nominatim;
use Geocoder\Provider\OpenCage\OpenCage;
use Geocoder\Provider\PickPoint\PickPoint;
use Geocoder\Provider\TomTom\TomTom;
use Geocoder\Provider\Yandex\Yandex;
use Nyholm\BundleTest\TestKernel;
use Nyholm\NSA;
use Symfony\Bridge\PhpUnit\ExpectDeprecationTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

final class ProviderFactoryTest extends KernelTestCase
{
    use ExpectDeprecationTrait;

    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    protected static function createKernel(array $options = []): KernelInterface
    {
        /**
         * @var TestKernel $kernel
         */
        $kernel = parent::createKernel($options);
        $kernel->addTestBundle(BazingaGeocoderBundle::class);
        $kernel->handleOptions($options);

        return $kernel;
    }

    /**
     * @param class-string           $class
     * @param list<non-empty-string> $serviceNames
     *
     * @dataProvider getProviders
     */
    public function testProviderConfiguration(string $class, array $serviceNames): void
    {
        self::bootKernel(['config' => static function (TestKernel $kernel) use ($class) {
            $kernel->addTestConfig(__DIR__.'/config/framework.yml');

            if ($kernel::VERSION_ID >= 60000) {
                $kernel->addTestConfig(__DIR__.'/config/framework_sf6.yml');
            }

            $kernel->addTestConfig(__DIR__.'/config/provider/'.strtolower(substr($class, strrpos($class, '\\') + 1)).'.yml');
        }]);

        $container = self::getContainer();

        foreach ($serviceNames as $name) {
            self::assertTrue($container->has('bazinga_geocoder.provider.'.$name));
            $service = $container->get('bazinga_geocoder.provider.'.$name);
            self::assertInstanceOf($class, NSA::getProperty($service, 'provider'));
        }
    }

    /**
     * @return iterable<array{0: class-string, 1: list<non-empty-string>}>
     */
    public function getProviders(): iterable
    {
        yield [AlgoliaPlaces::class, ['empty', 'acme']];
        yield [ArcGISOnline::class, ['empty', 'acme']];
        yield [BingMaps::class, ['acme']];
        yield [Chain::class, ['acme']];
        yield [FreeGeoIp::class, ['empty', 'acme']];
        yield [GeoIP2::class, ['acme']];
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
        yield [MaxMind::class, ['acme']];
        yield [Nominatim::class, ['empty', 'acme']];
        yield [OpenCage::class, ['acme']];
        yield [PickPoint::class, ['acme']];
        yield [TomTom::class, ['acme']];
        yield [Yandex::class, ['empty', 'acme']];
    }
}
