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
    ) {
    }
}
