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

use Bazinga\GeocoderBundle\Mapping\Annotations\Address;
use Bazinga\GeocoderBundle\Mapping\Annotations\Geocodeable;
use Bazinga\GeocoderBundle\Mapping\Annotations\Latitude;
use Bazinga\GeocoderBundle\Mapping\Annotations\Longitude;
use Bazinga\GeocoderBundle\Mapping\Driver\AnnotationDriver;
use Bazinga\GeocoderBundle\Mapping\Exception\MappingException;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Annotations\SimpleAnnotationReader;
use PHPUnit\Framework\TestCase;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class AnnotationDriverTest extends TestCase
{
    private AnnotationDriver $driver;

    private SimpleAnnotationReader|Reader $reader;

    protected function setUp(): void
    {
        AnnotationRegistry::registerLoader('class_exists');

        $this->reader = new SimpleAnnotationReader();
        $this->reader->addNamespace('Bazinga\GeocoderBundle\Mapping\Annotations');

        $this->driver = new AnnotationDriver($this->reader);
    }

    public function testLoadMetadata(): void
    {
        $obj = new Dummy();
        $metadata = $this->driver->loadMetadataFromObject($obj);

        $this->assertInstanceOf('ReflectionProperty', $metadata->addressProperty);
        $this->assertInstanceOf('ReflectionProperty', $metadata->latitudeProperty);
        $this->assertInstanceOf('ReflectionProperty', $metadata->longitudeProperty);
    }

    public function testLoadMetadataFromWrongObject(): void
    {
        $this->expectException(MappingException::class);
        $this->expectExceptionMessage('The class '.Dummy2::class.' is not geocodeable');

        $this->driver->loadMetadataFromObject(new Dummy2());
    }

    public function testIsGeocodable(): void
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
