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
use Bazinga\GeocoderBundle\Tests\Mapping\Driver\Fixtures\DummyNonGeocodable;
use Bazinga\GeocoderBundle\Tests\Mapping\Driver\Fixtures\DummyWithAddressGetter;
use PHPUnit\Framework\TestCase;

/**
 * @author Pierre du Plessis <pdples@gmail.com>
 */
final class AttributeDriverTest extends TestCase
{
    private AttributeDriver $driver;

    protected function setUp(): void
    {
        $this->driver = new AttributeDriver();
    }

    public function testLoadMetadata(): void
    {
        $metadata = $this->driver->loadMetadataFromObject(new Dummy());

        self::assertSame('acme', $metadata->provider);
        self::assertNotNull($metadata->addressProperty);
        self::assertSame('address', $metadata->addressProperty->getName());
        self::assertNotNull($metadata->latitudeProperty);
        self::assertSame('latitude', $metadata->latitudeProperty->getName());
        self::assertNotNull($metadata->longitudeProperty);
        self::assertSame('longitude', $metadata->longitudeProperty->getName());
        self::assertNotNull($metadata->northProperty);
        self::assertSame('north', $metadata->northProperty->getName());
        self::assertNotNull($metadata->southProperty);
        self::assertSame('south', $metadata->southProperty->getName());
        self::assertNotNull($metadata->eastProperty);
        self::assertSame('east', $metadata->eastProperty->getName());
        self::assertNotNull($metadata->westProperty);
        self::assertSame('west', $metadata->westProperty->getName());
        self::assertNotNull($metadata->streetNumberProperty);
        self::assertSame('streetNumber', $metadata->streetNumberProperty->getName());
        self::assertNotNull($metadata->streetNameProperty);
        self::assertSame('streetName', $metadata->streetNameProperty->getName());
        self::assertNotNull($metadata->localityProperty);
        self::assertSame('locality', $metadata->localityProperty->getName());
        self::assertNotNull($metadata->postalCodeProperty);
        self::assertSame('postalCode', $metadata->postalCodeProperty->getName());
        self::assertNotNull($metadata->subLocalityProperty);
        self::assertSame('subLocality', $metadata->subLocalityProperty->getName());
        self::assertNotNull($metadata->countryProperty);
        self::assertSame('country', $metadata->countryProperty->getName());
    }

    public function testLoadMetadataWithAddressGetter(): void
    {
        $metadata = $this->driver->loadMetadataFromObject(new DummyWithAddressGetter());

        self::assertSame('acme', $metadata->provider);
        self::assertNotNull($metadata->addressGetter);
        self::assertSame('getAddress', $metadata->addressGetter->getName());
    }

    public function testLoadMetadataFromWrongObject(): void
    {
        $this->expectException(MappingException::class);
        $this->expectExceptionMessage('The class "'.DummyNonGeocodable::class.'" is not geocodeable');

        $this->driver->loadMetadataFromObject(new DummyNonGeocodable());
    }

    public function testIsGeocodable(): void
    {
        self::assertTrue($this->driver->isGeocodeable(new Dummy()));
    }
}
