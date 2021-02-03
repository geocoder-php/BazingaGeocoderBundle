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
class ClassMetadata
{
    /**
     * @var \ReflectionProperty
     */
    public $addressProperty;

    /**
     * @var \ReflectionProperty
     */
    public $latitudeProperty;

    /**
     * @var \ReflectionProperty
     */
    public $longitudeProperty;

    /**
     * @var \ReflectionMethod
     */
    public $addressGetter;

    /**
     * @var string|null
     */
    public $provider = null;
}
