<?php

declare(strict_types=1);

/*
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Bazinga\Bundle\GeocoderBundle\Tests\Functional;

use Bazinga\Bundle\GeocoderBundle\BazingaGeocoderBundle;
use Geocoder\Provider\Cache\ProviderCache;
use Geocoder\Provider\GoogleMaps\GoogleMaps;
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
            [GoogleMaps::class, ['acme']]
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
