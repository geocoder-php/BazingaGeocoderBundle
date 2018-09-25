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
use Geocoder\Plugin\Plugin\CachePlugin;
use Geocoder\Plugin\Plugin\LoggerPlugin;
use Geocoder\Plugin\PluginProvider;
use Geocoder\Provider\GoogleMaps\GoogleMaps;
use Geocoder\ProviderAggregator;
use Nyholm\BundleTest\BaseBundleTestCase;
use Nyholm\BundleTest\CompilerPass\PublicServicePass;
use Nyholm\NSA;

class BundleInitializationTest extends BaseBundleTestCase
{
    protected function setUp()
    {
        parent::setUp();
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

        // Test if you services exists
        $this->assertTrue($container->has('Geocoder\ProviderAggregator'));
        $service = $container->get('Geocoder\ProviderAggregator');
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
}
