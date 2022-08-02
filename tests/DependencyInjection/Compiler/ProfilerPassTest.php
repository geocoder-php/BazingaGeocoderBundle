<?php

declare(strict_types=1);

/*
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Bazinga\GeocoderBundle\Tests\DependencyInjection\Compiler;

use Bazinga\GeocoderBundle\DataCollector\GeocoderDataCollector;
use Bazinga\GeocoderBundle\DependencyInjection\Compiler\ProfilerPass;
use Bazinga\GeocoderBundle\Plugin\ProfilingPlugin;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ProfilerPassTest extends TestCase
{
    /**
     * @var ProfilerPass
     */
    private $compilerPass;

    protected function setUp(): void
    {
        $this->compilerPass = new ProfilerPass();
    }

    public function testRegistersProviders(): void
    {
        $geocoderDataCollectorDefinition = new Definition(GeocoderDataCollector::class);

        $containerBuilder = new ContainerBuilder();
        $containerBuilder->setDefinition(GeocoderDataCollector::class, $geocoderDataCollectorDefinition);

        $bing = $containerBuilder->setDefinition('geocoder_profiling', new Definition(ProfilingPlugin::class, ['provider_id']));
        $bing->addTag('bazinga_geocoder.profiling_plugin');

        $this->compilerPass->process($containerBuilder);

        $this->assertTrue($geocoderDataCollectorDefinition->hasMethodCall('addInstance'));
        $this->assertInstanceOf(Reference::class, $geocoderDataCollectorDefinition->getMethodCalls()[0][1][0]);
    }
}
