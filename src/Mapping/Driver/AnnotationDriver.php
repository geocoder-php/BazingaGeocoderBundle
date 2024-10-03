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
use Bazinga\GeocoderBundle\Mapping\Exception;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\Proxy\DefaultProxyClassNameResolver;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class AnnotationDriver implements DriverInterface
{
    private Reader $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    public function isGeocodeable($object): bool
    {
        $reflection = self::getReflection($object);

        return (bool) $this->reader->getClassAnnotation($reflection, Annotations\Geocodeable::class);
    }

    public function loadMetadataFromObject($object)
    {
        $reflection = self::getReflection($object);

        if (!$annotation = $this->reader->getClassAnnotation($reflection, Annotations\Geocodeable::class)) {
            throw new Exception\MappingException(sprintf('The class %s is not geocodeable', get_class($object)));
        }

        $metadata = new ClassMetadata();

        foreach ($reflection->getProperties() as $property) {
            foreach ($this->reader->getPropertyAnnotations($property) as $annotation) {
                if ($annotation instanceof Annotations\Latitude) {
                    $property->setAccessible(true);
                    $metadata->latitudeProperty = $property;
                } elseif ($annotation instanceof Annotations\Longitude) {
                    $property->setAccessible(true);
                    $metadata->longitudeProperty = $property;
                } elseif ($annotation instanceof Annotations\Address) {
                    $property->setAccessible(true);
                    $metadata->addressProperty = $property;
                }
            }
        }

        foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if ($this->reader->getMethodAnnotation($method, Annotations\Address::class)) {
                if (0 !== $method->getNumberOfRequiredParameters()) {
                    throw new \Exception('You can not use a method requiring parameters with @Address annotation!');
                }

                $metadata->addressGetter = $method;
            }
        }

        return $metadata;
    }

    private static function getReflection(object $object): \ReflectionClass
    {
        if (class_exists(ClassUtils::class)) {
            return ClassUtils::newReflectionObject($object);
        }

        return new \ReflectionClass(DefaultProxyClassNameResolver::getClass($object));
    }
}
