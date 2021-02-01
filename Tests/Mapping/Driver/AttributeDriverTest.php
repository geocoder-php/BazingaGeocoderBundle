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
use Doctrine\Common\Annotations\Reader;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\PhpUnit\SetUpTearDownTrait;

/**
 * @author Pierre du Plessis <pdples@gmail.com>
 */
final class AttributeDriverTest extends TestCase
{
    use SetUpTearDownTrait;

    /**
     * @var AttributeDriver
     */
    private $driver;

    /**
     * @var Reader
     */
    private $reader;

    public static function doSetUpBeforeClass(): void
    {
        if (PHP_VERSION_ID < 80000) {
            self::markTestSkipped(sprintf('"%s" is only supported on PHP 8', AttributeDriver::class));
        }
    }

    protected function doSetUp(): void
    {
        $this->driver = new AttributeDriver();
    }

    public function testLoadMetadata()
    {
        $obj = new Dummy3();
        $metadata = $this->driver->loadMetadataFromObject($obj);

        $this->assertInstanceOf('ReflectionProperty', $metadata->addressProperty);
        $this->assertInstanceOf('ReflectionProperty', $metadata->latitudeProperty);
        $this->assertInstanceOf('ReflectionProperty', $metadata->longitudeProperty);
    }

    public function testLoadMetadataFromWrongObject()
    {
        $this->expectException(MappingException::class);
        $this->expectExceptionMessage('The class '.Dummy4::class.' is not geocodeable');

        $this->driver->loadMetadataFromObject(new Dummy4());
    }

    public function testIsGeocodable()
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
