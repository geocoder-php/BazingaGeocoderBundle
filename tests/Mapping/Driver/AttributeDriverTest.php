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

use Bazinga\GeocoderBundle\Mapping\Driver\AttributeDriver;
use Bazinga\GeocoderBundle\Mapping\Exception\MappingException;
use Bazinga\GeocoderBundle\Tests\Mapping\Driver\Fixtures\Dummy;
use Bazinga\GeocoderBundle\Tests\Mapping\Driver\Fixtures\Dummy2;
use PHPUnit\Framework\TestCase;

/**
 * @author Pierre du Plessis <pdples@gmail.com>
 */
final class AttributeDriverTest extends TestCase
{
    private AttributeDriver $driver;

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
        $obj = new Dummy();
        $metadata = $this->driver->loadMetadataFromObject($obj);

        self::assertInstanceOf('ReflectionProperty', $metadata->addressProperty);
        self::assertInstanceOf('ReflectionProperty', $metadata->latitudeProperty);
        self::assertInstanceOf('ReflectionProperty', $metadata->longitudeProperty);
    }

    /**
     * @requires PHP 8.0
     */
    public function testLoadMetadataFromWrongObject(): void
    {
        $this->expectException(MappingException::class);
        $this->expectExceptionMessage('The class '.Dummy2::class.' is not geocodeable');

        $this->driver->loadMetadataFromObject(new Dummy2());
    }

    /**
     * @requires PHP 8.0
     */
    public function testIsGeocodable(): void
    {
        self::assertTrue($this->driver->isGeocodeable(new Dummy()));
    }
}
