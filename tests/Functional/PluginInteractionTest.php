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
use Geocoder\Query\GeocodeQuery;
use Nyholm\BundleTest\TestKernel;
use Symfony\Bridge\PhpUnit\ExpectDeprecationTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpKernel\KernelInterface;

final class PluginInteractionTest extends KernelTestCase
{
    use ExpectDeprecationTrait;

    protected static function getKernelClass(): string
    {
        return CustomTestKernel::class;
    }

    protected static function createKernel(array $options = []): KernelInterface
    {
        /**
         * @var TestKernel $kernel
         */
        $kernel = parent::createKernel($options);
        $kernel->addTestBundle(BazingaGeocoderBundle::class);
        $kernel->addTestCompilerPass(new class implements CompilerPassInterface {
            public function process(ContainerBuilder $container): void
            {
                $container
                    ->getDefinition('http_client')
                    ->setPublic(true);
            }
        });
        $kernel->handleOptions($options);

        return $kernel;
    }

    public function testCachePluginUsesIpFromFakeIpPlugin(): void
    {
        $kernel = self::bootKernel(['config' => static function (TestKernel $kernel) {
            $kernel->setClearCacheAfterShutdown(false);
            $kernel->addTestConfig(__DIR__.'/config/framework.yml');

            if ($kernel::VERSION_ID >= 60000) {
                $kernel->addTestConfig(__DIR__.'/config/framework_sf6.yml');
            }

            $kernel->addTestConfig(__DIR__.'/config/cache_symfony.yml');
            $kernel->addTestConfig(__DIR__.'/config/fakeip_with_cache_cn.yml');
        }]);
        $kernel->setClearCacheAfterShutdown(false);
        $container = self::getContainer();

        $container->set('http_client', new MockHttpClient(static function (string $method, string $url): MockResponse {
            self::assertSame('GET', $method);
            self::assertSame('https://freegeoip.app/json/123.123.123.128', $url);

            return new MockResponse('{"ip":"123.123.123.128","country_code":"CN","country_name":"China","region_code":"CN-BJ","region_name":"Beijing","city":"Beijing","zip_code":"100006","time_zone":"Asia\/Shanghai","latitude":39.907501220703125,"longitude":116.39710235595703,"metro_code":0}', ['response_headers' => ['content-type' => 'application-json']]);
        }));

        $geoPluginGeocoder = $container->get('bazinga_geocoder.provider.geoPlugin');
        $result = $geoPluginGeocoder->geocodeQuery(GeocodeQuery::create('::1'));
        $country = $result->first()->getCountry()->getCode();
        self::assertEquals('CN', $country);

        $kernel = self::bootKernel(['config' => static function (TestKernel $kernel) {
            $kernel->setClearCacheAfterShutdown(false);
            $kernel->addTestConfig(__DIR__.'/config/framework.yml');

            if ($kernel::VERSION_ID >= 60000) {
                $kernel->addTestConfig(__DIR__.'/config/framework_sf6.yml');
            }

            $kernel->addTestConfig(__DIR__.'/config/cache_symfony.yml');
            $kernel->addTestConfig(__DIR__.'/config/fakeip_with_cache_fr.yml');
        }]);
        $kernel->setClearCacheAfterShutdown(false);
        $container = self::getContainer();

        $container->set('http_client', new MockHttpClient(static function (string $method, string $url): MockResponse {
            self::assertSame('GET', $method);
            self::assertSame('https://freegeoip.app/json/87.98.128.10', $url);

            return new MockResponse('{"ip":"87.98.128.10","country_code":"FR","country_name":"France","region_code":null,"region_name":"Nord","city":"Roubaix","zip_code":"59100","time_zone":"Europe\/Paris","latitude":50.69371032714844,"longitude":3.174438953399658,"metro_code":0}', ['response_headers' => ['content-type' => 'application-json']]);
        }));

        $geoPluginGeocoder = $container->get('bazinga_geocoder.provider.geoPlugin');
        $result = $geoPluginGeocoder->geocodeQuery(GeocodeQuery::create('::1'));
        $country = $result->first()->getCountry()->getCode();
        self::assertEquals('FR', $country);
    }
}
