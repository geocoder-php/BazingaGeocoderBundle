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

use Bazinga\GeocoderBundle\Mapping\Attributes;
use Bazinga\GeocoderBundle\Mapping\ClassMetadata;
use Bazinga\GeocoderBundle\Mapping\Exception\MappingException;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\Proxy\DefaultProxyClassNameResolver;

/**
 * @author Pierre du Plessis <pdples@gmail.com>
 */
final class AttributeDriver implements DriverInterface
{
    public function isGeocodeable(object $object): bool
    {
        $reflection = self::getReflection($object);

        return [] !== $reflection->getAttributes(Attributes\Geocodeable::class);
    }

    /**
     * @throws MappingException
     */
    public function loadMetadataFromObject(object $object): ClassMetadata
    {
        $reflection = self::getReflection($object);

        $attributes = $reflection->getAttributes(Attributes\Geocodeable::class);

        if ([] === $attributes) {
            throw new MappingException(sprintf('The class "%s" is not geocodeable', get_class($object)));
        }

        $args = [];

        foreach ($reflection->getProperties() as $property) {
            foreach ($property->getAttributes() as $attribute) {
                if (Attributes\Latitude::class === $attribute->getName()) {
                    $args['latitudeProperty'] = $property;
                } elseif (Attributes\Longitude::class === $attribute->getName()) {
                    $args['longitudeProperty'] = $property;
                } elseif (Attributes\Address::class === $attribute->getName()) {
                    $args['addressProperty'] = $property;
                }
            }
        }

        foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if ([] !== $method->getAttributes(Attributes\Address::class)) {
                if (0 !== $method->getNumberOfRequiredParameters()) {
                    throw new MappingException('You can not use a method requiring parameters with #[Address] attribute!');
                }

                $args['addressGetter'] = $method;
            }
        }

        return new ClassMetadata(...$args);
    }

    /**
     * @template T of object
     *
     * @param T $object
     *
     * @return \ReflectionClass<T>
     */
    private static function getReflection(object $object): \ReflectionClass
    {
        if (class_exists(ClassUtils::class)) {
            /** @var \ReflectionClass<T> */
            return ClassUtils::newReflectionObject($object);
        }

        /** @var \ReflectionClass<T> */
        return new \ReflectionClass(DefaultProxyClassNameResolver::getClass($object));
    }
}
