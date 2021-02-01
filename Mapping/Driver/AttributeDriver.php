<?php

declare(strict_types=1);

/*
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Bazinga\GeocoderBundle\Mapping\Driver;

use Bazinga\GeocoderBundle\Mapping\Annotations;
use Bazinga\GeocoderBundle\Mapping\ClassMetadata;
use Bazinga\GeocoderBundle\Mapping\Exception\MappingException;
use Doctrine\Common\Util\ClassUtils;

/**
 * @author Pierre du Plessis <pdples@gmail.com>
 */
final class AttributeDriver implements DriverInterface
{
    public function isGeocodeable($object): bool
    {
        if (PHP_VERSION_ID < 80000) {
            return false;
        }

        $reflection = ClassUtils::newReflectionObject($object);

        return count($reflection->getAttributes(Annotations\Geocodeable::class)) > 0;
    }

    /**
     * @throws MappingException
     */
    public function loadMetadataFromObject($object): ClassMetadata
    {
        if (PHP_VERSION_ID < 80000) {
            throw new MappingException(sprintf('The class %s is not geocodeable', get_class($object)));
        }

        $reflection = ClassUtils::newReflectionObject($object);

        $attributes = $reflection->getAttributes(Annotations\Geocodeable::class);

        if (0 === count($attributes)) {
            throw new MappingException(sprintf('The class %s is not geocodeable', get_class($object)));
        }

        $metadata = new ClassMetadata();
        $metadata->provider = $attributes[0]->newInstance()->provider;

        foreach ($reflection->getProperties() as $property) {
            foreach ($property->getAttributes() as $attribute) {
                if (Annotations\Latitude::class === $attribute->getName()) {
                    $property->setAccessible(true);
                    $metadata->latitudeProperty = $property;
                } elseif (Annotations\Longitude::class === $attribute->getName()) {
                    $property->setAccessible(true);
                    $metadata->longitudeProperty = $property;
                } elseif (Annotations\Address::class === $attribute->getName()) {
                    $property->setAccessible(true);
                    $metadata->addressProperty = $property;
                }
            }
        }

        foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if (count($method->getAttributes(Annotations\Address::class)) > 0) {
                if (0 !== $method->getNumberOfRequiredParameters()) {
                    throw new MappingException('You can not use a method requiring parameters with #[Address] attribute!');
                }

                $metadata->addressGetter = $method;
            }
        }

        return $metadata;
    }
}
