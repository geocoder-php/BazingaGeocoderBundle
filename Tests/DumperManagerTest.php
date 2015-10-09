<?php

namespace Bazinga\Bundle\GeocoderBundle\Tests;

use Bazinga\Bundle\GeocoderBundle\DumperManager;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class DumperManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DumperManager
     */
    private $manager;

    protected function setup()
    {
        $this->manager = new DumperManager();
    }

    public function testSet()
    {
        $this->assertNull($this->manager->set('test', $this->getMock('Geocoder\\Dumper\\Dumper')));
    }

    public function testGet()
    {
        $dumper = $this->getMock('Geocoder\\Dumper\\Dumper');
        $this->manager->set('test', $dumper);

        $this->assertEquals($dumper, $this->manager->get('test'));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetNotExistDumper()
    {
        $this->manager->get('not-exist');
    }

    public function testHas()
    {
        $this->assertFalse($this->manager->has('test'));
        $this->manager->set('test', $this->getMock('Geocoder\\Dumper\\Dumper'));
        $this->assertTrue($this->manager->has('test'));
    }

    public function testRemove()
    {
        $this->manager->set('test', $this->getMock('Geocoder\\Dumper\\Dumper'));
        $this->manager->remove('test');
        $this->assertFalse($this->manager->has('test'));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testRemoveNotExistDumper()
    {
        $this->manager->remove('not-exist');
    }
}
