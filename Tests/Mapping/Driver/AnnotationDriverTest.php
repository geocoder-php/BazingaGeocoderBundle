<?php

declare(strict_types=1);

/*
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Bazinga\GeocoderBundle\Tests\Mapping\Driver;

use Bazinga\GeocoderBundle\Mapping\Driver\AnnotationDriver;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Annotations\SimpleAnnotationReader;
use PHPUnit\Framework\TestCase;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class AnnotationDriverTest extends TestCase
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
        $this->reader->addNamespace('Bazinga\GeocoderBundle\Mapping\Annotations');

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
     * @expectedException \Bazinga\GeocoderBundle\Mapping\Exception\MappingException
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
