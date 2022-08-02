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
use Bazinga\GeocoderBundle\Mapping\Exception\MappingException;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Annotations\SimpleAnnotationReader;
use PHPUnit\Framework\TestCase;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
final class AnnotationDriverTest extends TestCase
{
    private AnnotationDriver $driver;
    private Reader $reader;

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

        self::assertInstanceOf(\ReflectionProperty::class, $metadata->addressProperty);
        self::assertInstanceOf(\ReflectionProperty::class, $metadata->latitudeProperty);
        self::assertInstanceOf(\ReflectionProperty::class, $metadata->longitudeProperty);
    }

    public function testLoadMetadataFromWrongObject(): void
    {
        $this->expectException(MappingException::class);
        $this->expectExceptionMessage('The class '.Dummy2::class.' is not geocodeable');

        $this->driver->loadMetadataFromObject(new Dummy2());
    }

    public function testIsGeocodable(): void
    {
        self::assertTrue($this->driver->isGeocodeable(new Dummy()));
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
