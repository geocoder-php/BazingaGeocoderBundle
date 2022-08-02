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
use Bazinga\GeocoderBundle\Mapping\Driver\AttributeDriver;
use Bazinga\GeocoderBundle\Mapping\Exception\MappingException;
use PHPUnit\Framework\TestCase;

/**
 * @author Pierre du Plessis <pdples@gmail.com>
 */
final class AttributeDriverTest extends TestCase
{
    /**
     * @var AttributeDriver
     */
    private $driver;

    public static function setUpBeforeClass(): void
    {
        if (PHP_VERSION_ID < 80000) {
            self::markTestSkipped(sprintf('"%s" is only supported on PHP 8', AttributeDriver::class));
        }
    }

    protected function setUp(): void
    {
        $this->driver = new AttributeDriver();
    }

    /**
     * @requires PHP 8.0
     */
    public function testLoadMetadata(): void
    {
        $obj = new Dummy3();
        $metadata = $this->driver->loadMetadataFromObject($obj);

        $this->assertInstanceOf('ReflectionProperty', $metadata->addressProperty);
        $this->assertInstanceOf('ReflectionProperty', $metadata->latitudeProperty);
        $this->assertInstanceOf('ReflectionProperty', $metadata->longitudeProperty);
    }

    /**
     * @requires PHP 8.0
     */
    public function testLoadMetadataFromWrongObject(): void
    {
        $this->expectException(MappingException::class);
        $this->expectExceptionMessage('The class '.Dummy4::class.' is not geocodeable');

        $this->driver->loadMetadataFromObject(new Dummy4());
    }

    /**
     * @requires PHP 8.0
     */
    public function testIsGeocodable(): void
    {
        $this->assertTrue($this->driver->isGeocodeable(new Dummy3()));
    }
}

#[Geocodeable()]
class Dummy3
{
    #[Latitude()]
    public $latitude;

    #[Longitude()]
    public $longitude;

    #[Address()]
    public $address;
}

class Dummy4
{
}
