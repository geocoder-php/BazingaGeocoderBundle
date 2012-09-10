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
    private $provider;

    /**
     * @var integer
     */
    private $lifetime;

    /**
     * @var string|null
     */
    private $locale;

    /**
     * Constructor
     *
     * @param Cache             $cache     The cache interface
     * @param ProviderInterface $provider  The fallback provider
     * @param integer           $lifetime  The cache lifetime
     */
    public function __construct(Cache $cache, ProviderInterface $provider, $lifetime = 0, $locale = null)
    {
        $this->cache = $cache;
        $this->provider = $provider;
        $this->lifetime = $lifetime;
        $this->locale = $locale;
    }

    /**
     * {@inheritDoc}
     */
    public function getGeocodedData($address)
    {
        $key = crc32($this->locale.$address);

        if (false !== $data = $this->cache->fetch($key)) {
            return unserialize($data);
        }

        $data = $this->provider->getGeocodedData($address);
        $this->cache->save($key, serialize($data), $this->lifetime);

        return $data;
    }

    /**
     * {@inheritDoc}
     */
    public function getReversedData(array $coordinates)
    {
        $key = crc32(serialize($this->locale.$coordinates));

        if (false !== $data = $this->cache->fetch($key)) {
            return unserialize($data);
        }

        $data = $this->provider->getReversedData($coordinates);
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