<?php

declare(strict_types=1);

/*
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Bazinga\GeocoderBundle\Mapping\Annotations;

#[\Attribute(\Attribute::TARGET_CLASS)]
/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 *
 * @Annotation
 */
class Geocodeable
{
    /**
     * @var string
     */
    public $provider = null;

    public function __construct(array $options = [], string $provider = null)
    {
        $this->provider = $options['provider'] ?? $provider;
    }
}
