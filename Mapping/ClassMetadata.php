<?php

namespace Bazinga\Bundle\GeocoderBundle\Mapping;

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
}
