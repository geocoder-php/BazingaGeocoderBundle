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
use Bazinga\GeocoderBundle\Tests\PublicServicePass;
use Geocoder\Dumper\GeoArray;
use Geocoder\Dumper\GeoJson;
use Geocoder\Dumper\Gpx;
use Geocoder\Dumper\Kml;
use Geocoder\Dumper\Wkb;
use Geocoder\Dumper\Wkt;
use Geocoder\Plugin\Plugin\CachePlugin;
use Geocoder\Plugin\Plugin\LoggerPlugin;
use Geocoder\Plugin\PluginProvider;
use Geocoder\Provider\GoogleMaps\GoogleMaps;
use Geocoder\ProviderAggregator;
use Nyholm\BundleTest\TestKernel;
use Nyholm\NSA;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

class BundleInitializationTest extends KernelTestCase
{
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
        $kernel->addTestCompilerPass(new PublicServicePass('|[Bb]azinga:*|'));
        $kernel->addTestCompilerPass(new PublicServicePass('|[gG]eocoder:*|'));
        $kernel->handleOptions($options);

        return $kernel;
    }

    public function testInitBundle(): void
    {
        $kernel = self::bootKernel(['config' => static function (TestKernel $kernel) {
            $kernel->addTestConfig(__DIR__.'/config/framework.yml');
        }]);

        $container = method_exists(__CLASS__, 'getContainer') ? self::getContainer() : $kernel->getContainer();

        // Test if services exists
        $this->assertTrue($container->has(ProviderAggregator::class));
        $service = $container->get(ProviderAggregator::class);
        $this->assertInstanceOf(ProviderAggregator::class, $service);
    }

    public function testBundleWithOneProviderConfiguration(): void
    {
        $kernel = self::bootKernel(['config' => static function (TestKernel $kernel) {
            $kernel->addTestConfig(__DIR__.'/config/framework.yml');
            $kernel->addTestConfig(__DIR__.'/config/simple.yml');
        }]);

        $container = method_exists(__CLASS__, 'getContainer') ? self::getContainer() : $kernel->getContainer();

        $this->assertTrue($container->has('bazinga_geocoder.provider.acme'));
        $service = $container->get('bazinga_geocoder.provider.acme');
        $this->assertInstanceOf(PluginProvider::class, $service);
        $this->assertInstanceOf(GoogleMaps::class, NSA::getProperty($service, 'provider'));
    }

    public function testBundleWithCachedProvider(): void
    {
        $kernel = self::bootKernel(['config' => static function (TestKernel $kernel) {
            $kernel->addTestConfig(__DIR__.'/config/framework.yml');
            $kernel->addTestConfig(__DIR__.'/config/cache.yml');
        }]);

        $container = method_exists(__CLASS__, 'getContainer') ? self::getContainer() : $kernel->getContainer();

        $this->assertTrue($container->has('bazinga_geocoder.provider.acme'));
        $service = $container->get('bazinga_geocoder.provider.acme');
        $this->assertInstanceOf(PluginProvider::class, $service);
        $plugins = NSA::getProperty($service, 'plugins');
        $this->assertNotEmpty($plugins);
        $this->assertInstanceOf(CachePlugin::class, $plugins[0]);
    }

    public function testCacheLifetimeCanBeNull(): void
    {
        $kernel = self::bootKernel(['config' => static function (TestKernel $kernel) {
            $kernel->addTestConfig(__DIR__.'/config/framework.yml');
            $kernel->addTestConfig(__DIR__.'/config/cache_without_lifetime.yml');
        }]);

        $container = method_exists(__CLASS__, 'getContainer') ? self::getContainer() : $kernel->getContainer();

        self::assertTrue($container->has('bazinga_geocoder.provider.acme'));

        /** @var PluginProvider $service */
        $service = $container->get('bazinga_geocoder.provider.acme');
        self::assertInstanceOf(PluginProvider::class, $service);

        $plugins = NSA::getProperty($service, 'plugins');
        self::assertCount(1, $plugins);

        $cachePlugin = array_shift($plugins);
        self::assertInstanceOf(CachePlugin::class, $cachePlugin);

        $cacheLifeTime = NSA::getProperty($cachePlugin, 'lifetime');
        self::assertNull($cacheLifeTime);
    }

    public function testBundleWithPluginsYml(): void
    {
        $kernel = self::bootKernel(['config' => static function (TestKernel $kernel) {
            $kernel->addTestConfig(__DIR__.'/config/framework.yml');
            $kernel->addTestConfig(__DIR__.'/config/service_plugin.yml');
        }]);

        $container = method_exists(__CLASS__, 'getContainer') ? self::getContainer() : $kernel->getContainer();

        $this->assertTrue($container->has('bazinga_geocoder.provider.acme'));
        $service = $container->get('bazinga_geocoder.provider.acme');
        $this->assertInstanceOf(PluginProvider::class, $service);
        $plugins = NSA::getProperty($service, 'plugins');
        $this->assertCount(3, $plugins);
        $this->assertInstanceOf(LoggerPlugin::class, $plugins[0]);
    }

    public function testBundleWithPluginXml(): void
    {
        $kernel = self::bootKernel(['config' => static function (TestKernel $kernel) {
            $kernel->addTestConfig(__DIR__.'/config/framework.yml');
            $kernel->addTestConfig(__DIR__.'/config/service_plugin.xml');
        }]);

        $container = method_exists(__CLASS__, 'getContainer') ? self::getContainer() : $kernel->getContainer();

        $this->assertTrue($container->has('bazinga_geocoder.provider.acme'));
        $service = $container->get('bazinga_geocoder.provider.acme');
        $this->assertInstanceOf(PluginProvider::class, $service);
        $plugins = NSA::getProperty($service, 'plugins');
        $this->assertNotEmpty($plugins);
        $this->assertInstanceOf(LoggerPlugin::class, $plugins[0]);
    }

    public function testBundleHasRegisteredDumpers(): void
    {
        $kernel = self::bootKernel(['config' => static function (TestKernel $kernel) {
            $kernel->addTestConfig(__DIR__.'/config/framework.yml');
        }]);

        $container = method_exists(__CLASS__, 'getContainer') ? self::getContainer() : $kernel->getContainer();

        $this->assertTrue($container->has(GeoArray::class));
        $this->assertTrue($container->has(GeoJson::class));
        $this->assertTrue($container->has(Gpx::class));
        $this->assertTrue($container->has(Kml::class));
        $this->assertTrue($container->has(Wkb::class));
        $this->assertTrue($container->has(Wkt::class));
    }
}
