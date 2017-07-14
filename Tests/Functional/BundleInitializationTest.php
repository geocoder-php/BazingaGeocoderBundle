<?php

declare(strict_types=1);
namespace Bazinga\Bundle\GeocoderBundle\Tests\Functional;

use Bazinga\Bundle\GeocoderBundle\BazingaGeocoderBundle;
use Geocoder\Provider\GoogleMaps\GoogleMaps;
use Geocoder\Provider\Provider;
use Geocoder\ProviderAggregator;
use Nyholm\BundleTest\BaseBundleTestCase;


class BundleInitializationTest extends BaseBundleTestCase
{
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
        $this->assertInstanceOf(Provider::class, $service);
    }
}