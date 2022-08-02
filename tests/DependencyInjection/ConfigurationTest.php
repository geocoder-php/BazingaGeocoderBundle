<?php

declare(strict_types=1);

/*
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Bazinga\GeocoderBundle\Tests\DependencyInjection;

use Bazinga\GeocoderBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Yaml\Yaml;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
final class ConfigurationTest extends TestCase
{
    public function testGetConfigTreeBuilder(): void
    {
        $config = Yaml::parseFile(__DIR__.'/Fixtures/config.yml');

        $configuration = new Configuration(true);
        $treeBuilder = $configuration->getConfigTreeBuilder();
        $processor = new Processor();

        $config = $processor->process($treeBuilder->buildTree(), $config);

        self::assertTrue($config['profiling']['enabled']);
        self::assertTrue($config['fake_ip']['enabled']);
        self::assertSame('192.168.99.1', $config['fake_ip']['local_ip']);
        self::assertSame('33.33.33.11', $config['fake_ip']['ip']);
    }

    public function testGetConfigTreeBuilderNoDebug(): void
    {
        $config = Yaml::parseFile(__DIR__.'/Fixtures/config.yml');

        $configuration = new Configuration(false);
        $treeBuilder = $configuration->getConfigTreeBuilder();
        $processor = new Processor();

        $config = $processor->process($treeBuilder->buildTree(), $config);

        self::assertFalse($config['profiling']['enabled']);
    }
}
