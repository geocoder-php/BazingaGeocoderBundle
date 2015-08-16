<?php

namespace Bazinga\Bundle\GeocoderBundle\Tests\Mapping\Driver;

use Bazinga\Bundle\GeocoderBundle\Mapping\Driver\AnnotationDriver;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Annotations\SimpleAnnotationReader;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class AnnotationDriverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AnnotationDriver
     */
    private $driver;

    /**
     * @var Reader
     */
    private $reader;

    protected function setUp()
    {
        AnnotationRegistry::registerLoader('class_exists');

        $this->reader = new SimpleAnnotationReader();
        $this->reader->addNamespace('Bazinga\Bundle\GeocoderBundle\Mapping\Annotations');

        $this->driver = new AnnotationDriver($this->reader);
    }

    public function testLoadMetadata()
    {
        $obj = new Dummy();
        $metadata = $this->driver->loadMetadataFromObject($obj);

        $this->assertInstanceOf('ReflectionProperty', $metadata->addressProperty);
        $this->assertInstanceOf('ReflectionProperty', $metadata->latitudeProperty);
        $this->assertInstanceOf('ReflectionProperty', $metadata->longitudeProperty);
    }

    /**
     * @expectedException Bazinga\Bundle\GeocoderBundle\Mapping\Exception\MappingException
     */
    public function testLoadMetadataFromWrongObject()
    {
        $this->driver->loadMetadataFromObject(new Dummy2());
    }

    public function testIsGeocodable()
    {
        $this->assertTrue($this->driver->isGeocodeable(new Dummy()));
    }
}

/**
 * @Geocodeable
 */
class Dummy
{
    /**
     * @Latitude
     */
    public $latitude;

    /**
     * @Longitude
     */
    public $longitude;

    /**
     * @Address
     */
    public $address;
}

class Dummy2
{
}
