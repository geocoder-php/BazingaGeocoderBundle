<?php

declare(strict_types=1);

/*
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Bazinga\GeocoderBundle\Mapping;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
final class ClassMetadata
{
    /**
     * @param non-empty-string $provider
     */
    public function __construct(
        public readonly string $provider,
        public readonly ?\ReflectionProperty $addressProperty = null,
        public readonly ?\ReflectionProperty $latitudeProperty = null,
        public readonly ?\ReflectionProperty $longitudeProperty = null,
        public readonly ?\ReflectionMethod $addressGetter = null,
        public readonly ?\ReflectionProperty $northProperty = null,
        public readonly ?\ReflectionProperty $southProperty = null,
        public readonly ?\ReflectionProperty $eastProperty = null,
        public readonly ?\ReflectionProperty $westProperty = null,
        public readonly ?\ReflectionProperty $streetNumberProperty = null,
        public readonly ?\ReflectionProperty $streetNameProperty = null,
        public readonly ?\ReflectionProperty $localityProperty = null,
        public readonly ?\ReflectionProperty $postalCodeProperty = null,
        public readonly ?\ReflectionProperty $subLocalityProperty = null,
        public readonly ?\ReflectionProperty $countryProperty = null,
    ) {
    }
}
