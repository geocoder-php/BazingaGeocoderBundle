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

use Bazinga\GeocoderBundle\DependencyInjection\Compiler\AddProvidersPass;
use Geocoder\Provider\BingMaps\BingMaps;
use Geocoder\ProviderAggregator;
use Http\Client\Curl\Client;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class AddProvidersPassTest extends TestCase
{
    private AddProvidersPass $compilerPass;

    protected function setUp(): void
    {
        $this->compilerPass = new AddProvidersPass();
    }

    public function testRegistersProviders(): void
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->setDefinition(ProviderAggregator::class, new Definition(ProviderAggregator::class));

        $bing = $containerBuilder->setDefinition('bing_maps', new Definition(BingMaps::class, [new Client(), 'apikey']));
        $bing->addTag('bazinga_geocoder.provider');

        $this->compilerPass->process($containerBuilder);

        /** @var ProviderAggregator $providerAggregator */
        $providerAggregator = $containerBuilder->get(ProviderAggregator::class);
        $providers = $providerAggregator->getProviders();

        $this->assertArrayHasKey('bing_maps', $providers);
        $this->assertInstanceOf(BingMaps::class, $providers['bing_maps']);
    }
}
