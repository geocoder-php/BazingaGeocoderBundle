<?php

/**
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */
namespace Bazinga\Bundle\GeocoderBundle\Provider;

use Doctrine\Common\Cache\Cache as DoctrineCache;
use Geocoder\Provider\AbstractProvider;
use Geocoder\Provider\Provider;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class Cache extends AbstractProvider implements Provider
{
    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var Provider
     */
    private $provider;

    /**
     * @var int
     */
    private $lifetime;

    /**
     * @var string|null
     */
    private $locale;

    /**
     * @var int
     */
    private $maxResults = Provider::MAX_RESULTS;

    /**
     * Constructor.
     *
     * @param DoctrineCache $cache    The cache interface
     * @param Provider      $provider The fallback provider
     * @param int           $lifetime The cache lifetime
     * @param string        $locale
     */
    public function __construct(DoctrineCache $cache, Provider $provider, $lifetime = 0, $locale = null)
    {
        parent::__construct();

        $this->cache = $cache;
        $this->provider = $provider;
        $this->lifetime = $lifetime;
        $this->locale = $locale;
    }

    /**
     * {@inheritdoc}
     */
    public function geocode($address)
    {
        $key = 'geocoder_'.sha1($this->locale.$address);

        if (false !== $data = $this->cache->fetch($key)) {
            return unserialize($data);
        }

        $data = $this->provider->geocode($address);
        $this->cache->save($key, serialize($data), $this->lifetime);

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function reverse($latitude, $longitude)
    {
        $key = 'geocoder_'.sha1($this->locale.$latitude.$longitude);

        if (false !== $data = $this->cache->fetch($key)) {
            return unserialize($data);
        }

        $data = $this->provider->reverse($latitude, $longitude);
        $this->cache->save($key, serialize($data), $this->lifetime);

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'cache';
    }

    /**
     * {@inheritdoc}
     */
    public function setMaxResults($maxResults)
    {
        $this->maxResults = $maxResults;

        return $this;
    }
}
