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
class ConfigurationTest extends TestCase
{
    public function testGetConfigTreeBuilder()
    {
        $config = Yaml::parse(file_get_contents(__DIR__.'/Fixtures/config.yml'));

        $configuration = new Configuration(true);
        $treeBuilder = $configuration->getConfigTreeBuilder();
        $processor = new Processor();

        $config = $processor->process($treeBuilder->buildTree(), $config);

        $this->assertTrue($config['profiling']['enabled']);
        $this->assertTrue($config['fake_ip']['enabled']);
        $this->assertSame('192.168.99.1', $config['fake_ip']['local_ip']);
        $this->assertSame('33.33.33.11', $config['fake_ip']['ip']);
    }

    public function testGetConfigTreeBuilderNoDebug()
    {
        $config = Yaml::parse(file_get_contents(__DIR__.'/Fixtures/config.yml'));

        $configuration = new Configuration(false);
        $treeBuilder = $configuration->getConfigTreeBuilder();
        $processor = new Processor();

        $config = $processor->process($treeBuilder->buildTree(), $config);

        $this->assertFalse($config['profiling']['enabled']);
    }
}
