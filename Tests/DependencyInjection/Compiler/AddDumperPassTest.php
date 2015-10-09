<?php

namespace Bazinga\Bundle\GeocoderBundle\Tests\DependencyInjection\Compiler;

use Bazinga\Bundle\GeocoderBundle\DependencyInjection\Compiler\AddDumperPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class AddDumperPassTest extends \PHPUnit_Framework_TestCase
{
    public function testProcess()
    {
        $builder = new ContainerBuilder();
        $builder->setDefinition('bazinga_geocoder.dumper_manager', new Definition('Bazinga\Bundle\GeocoderBundle\DumperManager'));

        $dumper = new Definition('Geocoder\Dumper\GeoJson');
        $dumper->addTag('bazinga_geocoder.dumper', array('alias' => 'geojson'));

        $builder->setDefinition('bazinga_geocoder.dumper.geojson', $dumper);

        $compiler = new AddDumperPass();
        $compiler->process($builder);

        $manager = $builder->get('bazinga_geocoder.dumper_manager');

        $this->assertTrue($manager->has('geojson'));
    }

    public function testProcessWithoutManager()
    {
        $builder = new ContainerBuilder();
        $compiler = new AddDumperPass();

        $this->assertNull($compiler->process($builder));
    }
}
