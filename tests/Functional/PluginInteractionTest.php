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
        $kernel->handleOptions($options);

        return $kernel;
    }

    public function testCachePluginUsesIpFromFakeIpPlugin(): void
    {
        $kernel = self::bootKernel(['config' => static function (TestKernel $kernel): void {
            $kernel->setClearCacheAfterShutdown(false);
            $kernel->addTestConfig(__DIR__.'/config/framework.yml');

            if ($kernel::VERSION_ID >= 60000) {
                $kernel->addTestConfig(__DIR__.'/config/framework_sf6.yml');
            }

            $kernel->addTestConfig(__DIR__.'/config/cache_symfony.yml');
            $kernel->addTestConfig(__DIR__.'/config/geo_plugin_fakeip_with_cache_cn.yml');
        }]);
        $kernel->setClearCacheAfterShutdown(false);
        $container = self::getContainer();

        $geoPluginGeocoder = $container->get('bazinga_geocoder.provider.geoPlugin');
        $result = $geoPluginGeocoder->geocodeQuery(GeocodeQuery::create('::1'));
        $country = $result->first()->getCountry()->getCode();
        self::assertEquals('CN', $country);

        $kernel = self::bootKernel(['config' => static function (TestKernel $kernel): void {
            $kernel->setClearCacheAfterShutdown(false);
            $kernel->addTestConfig(__DIR__.'/config/framework.yml');

            if ($kernel::VERSION_ID >= 60000) {
                $kernel->addTestConfig(__DIR__.'/config/framework_sf6.yml');
            }

            $kernel->addTestConfig(__DIR__.'/config/cache_symfony.yml');
            $kernel->addTestConfig(__DIR__.'/config/geo_plugin_fakeip_with_cache_fr.yml');
        }]);
        $kernel->setClearCacheAfterShutdown(false);
        $container = self::getContainer();

        $geoPluginGeocoder = $container->get('bazinga_geocoder.provider.geoPlugin');
        $result = $geoPluginGeocoder->geocodeQuery(GeocodeQuery::create('::1'));
        $country = $result->first()->getCountry()->getCode();
        self::assertEquals('FR', $country);
    }
}
