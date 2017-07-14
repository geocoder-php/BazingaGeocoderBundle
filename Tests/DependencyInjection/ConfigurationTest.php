<?php

namespace Bazinga\Bundle\GeocoderBundle\Tests\DependencyInjection;

use Bazinga\Bundle\GeocoderBundle\DependencyInjection\Configuration;
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
    }
}
