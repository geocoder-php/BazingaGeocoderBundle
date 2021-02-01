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

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class AnnotationDriver implements DriverInterface
{
    private $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    public function isGeocodeable($object): bool
    {
        $reflection = ClassUtils::newReflectionObject($object);

        return (bool) $this->reader->getClassAnnotation($reflection, Annotations\Geocodeable::class);
    }

    public function loadMetadataFromObject($object)
    {
        $reflection = ClassUtils::newReflectionObject($object);

        if (!$annotation = $this->reader->getClassAnnotation($reflection, Annotations\Geocodeable::class)) {
            throw new Exception\MappingException(sprintf('The class %s is not geocodeable', get_class($object)));
        }

        $metadata = new ClassMetadata();
        $metadata->provider = $annotation->provider;

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
}
