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
use Nyholm\BundleTest\BaseBundleTestCase;
use Nyholm\BundleTest\CompilerPass\PublicServicePass;
use Nyholm\NSA;
use Symfony\Bridge\PhpUnit\SetUpTearDownTrait;

class BundleInitializationTest extends BaseBundleTestCase
{
    use SetUpTearDownTrait;

    protected function doSetUp(): void
    {
        $this->addCompilerPass(new PublicServicePass('|[Bb]azinga:*|'));
        $this->addCompilerPass(new PublicServicePass('|[gG]eocoder:*|'));
    }

    protected function getBundleClass()
    {
        return BazingaGeocoderBundle::class;
    }

    public function testInitBundle()
    {
        // Boot the kernel.
        $this->bootKernel();

        // Get the container
        $container = $this->getContainer();

        // Test if services exists
        $this->assertTrue($container->has(ProviderAggregator::class));
        $service = $container->get(ProviderAggregator::class);
        $this->assertInstanceOf(ProviderAggregator::class, $service);
    }

    public function testBundleWithOneProviderConfiguration()
    {
        // Create a new Kernel
        $kernel = $this->createKernel();

        // Add some configuration
        $kernel->addConfigFile(__DIR__.'/config/simple.yml');

        // Boot the kernel as normal ...
        $this->bootKernel();
        $container = $this->getContainer();

        $this->assertTrue($container->has('bazinga_geocoder.provider.acme'));
        $service = $container->get('bazinga_geocoder.provider.acme');
        $this->assertInstanceOf(PluginProvider::class, $service);
        $this->assertInstanceOf(GoogleMaps::class, NSA::getProperty($service, 'provider'));
    }

    public function testBundleWithCachedProvider()
    {
        $kernel = $this->createKernel();
        $kernel->addConfigFile(__DIR__.'/config/cache.yml');
        $this->bootKernel();
        $container = $this->getContainer();

        $this->assertTrue($container->has('bazinga_geocoder.provider.acme'));
        $service = $container->get('bazinga_geocoder.provider.acme');
        $this->assertInstanceOf(PluginProvider::class, $service);
        $plugins = NSA::getProperty($service, 'plugins');
        $this->assertNotEmpty($plugins);
        $this->assertInstanceOf(CachePlugin::class, $plugins[0]);
    }

    public function testCacheLifetimeCanBeNull()
    {
        $kernel = $this->createKernel();
        $kernel->addConfigFile(__DIR__.'/config/cache_without_lifetime.yml');

        $this->bootKernel();

        $container = $this->getContainer();

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

    public function testBundleWithPluginsYml()
    {
        $kernel = $this->createKernel();
        $kernel->addConfigFile(__DIR__.'/config/service_plugin.yml');
        $this->bootKernel();
        $container = $this->getContainer();

        $this->assertTrue($container->has('bazinga_geocoder.provider.acme'));
        $service = $container->get('bazinga_geocoder.provider.acme');
        $this->assertInstanceOf(PluginProvider::class, $service);
        $plugins = NSA::getProperty($service, 'plugins');
        $this->assertCount(3, $plugins);
        $this->assertInstanceOf(LoggerPlugin::class, $plugins[0]);
    }

    public function testBundleWithPluginXml()
    {
        $kernel = $this->createKernel();
        $kernel->addConfigFile(__DIR__.'/config/service_plugin.xml');
        $this->bootKernel();
        $container = $this->getContainer();

        $this->assertTrue($container->has('bazinga_geocoder.provider.acme'));
        $service = $container->get('bazinga_geocoder.provider.acme');
        $this->assertInstanceOf(PluginProvider::class, $service);
        $plugins = NSA::getProperty($service, 'plugins');
        $this->assertNotEmpty($plugins);
        $this->assertInstanceOf(LoggerPlugin::class, $plugins[0]);
    }

    public function testBundleHasRegisteredDumpers()
    {
        $this->bootKernel();
        $container = $this->getContainer();

        $this->assertTrue($container->has(GeoArray::class));
        $this->assertTrue($container->has(GeoJson::class));
        $this->assertTrue($container->has(Gpx::class));
        $this->assertTrue($container->has(Kml::class));
        $this->assertTrue($container->has(Wkb::class));
        $this->assertTrue($container->has(Wkt::class));
    }
}
