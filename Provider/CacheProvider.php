<?php

/**
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Bazinga\Bundle\GeocoderBundle\Provider;

use Geocoder\Provider\ProviderInterface;
use Doctrine\Common\Cache\Cache;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class CacheProvider implements ProviderInterface
{
    /**
     * @var \Doctrine\Common\Cache\Cache
     */
    private $cache;

    /**
     * @var \Geocoder\Provider\ProviderInterface
     */
    private $fallback;

    /**
     * @var integer
     */
    private $lifetime;

    /**
     * Constructor
     *
     * @param Cache             $cache     The cache interface
     * @param ProviderInterface $fallback  The fallback provider
     * @param integer           $lifetime  The cache lifetime
     */
    public function __construct(Cache $cache, ProviderInterface $fallback, $lifetime = 0)
    {
        $this->cache = $cache;
        $this->fallback = $fallback;
        $this->lifetime = $lifetime;
    }

    /**
     * {@inheritDoc}
     */
    public function getGeocodedData($address)
    {
        $key = crc32($address);

        if (false !== $data = $this->cache->fetch($key)) {
            return unserialize($data);
        }

        $data = $this->fallback->getGeocodedData($address);
        $this->cache->save($key, serialize($data), $this->lifetime);

        return $data;
    }

    /**
     * {@inheritDoc}
     */
    public function getReversedData(array $coordinates)
    {
        $key = crc32(serialize($coordinates));

        if (false !== $data = $this->cache->fetch($key)) {
            return unserialize($data);
        }

        $data = $this->fallback->getReversedData($coordinates);
        $this->cache->save($key, serialize($data), $this->lifetime);

        return $data;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'cache';
    }
}