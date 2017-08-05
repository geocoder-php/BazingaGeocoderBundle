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
        $reflection = new \ReflectionObject($object);

        return (bool) $this->reader->getClassAnnotation($reflection, Annotations\Geocodeable::class);
    }

    public function loadMetadataFromObject($object)
    {
        $reflection = new \ReflectionObject($object);
        if (!$annotation = $this->reader->getClassAnnotation($reflection, Annotations\Geocodeable::class)) {
            throw new Exception\MappingException(sprintf(
                'The class %s is not geocodeable', get_class($object)
            ));
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

        return $metadata;
    }
}
