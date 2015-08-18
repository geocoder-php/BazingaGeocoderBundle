<?php

/**
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */
namespace Bazinga\Bundle\GeocoderBundle\Geocoder;

use Bazinga\Bundle\GeocoderBundle\Logger\GeocoderLogger;
use Geocoder\ProviderAggregator;

class LoggableGeocoder extends ProviderAggregator
{
    /**
     * @var GeocoderLogger
     */
    protected $logger;

    /**
     * @param GeocoderLogger $logger
     */
    public function setLogger(GeocoderLogger $logger = null)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function geocode($value)
    {
        if (null === $this->logger) {
            return parent::geocode($value);
        }

        $startTime = microtime(true);
        $results = parent::geocode($value);
        $duration = (microtime(true) - $startTime) * 1000;

        $this->logger->logRequest(
            sprintf('[Geocoding] %s', $value),
            $duration,
            $this->getProviderClass(),
            $results
        );

        return $results;
    }

    /**
     * {@inheritdoc}
     */
    public function reverse($latitude, $longitude)
    {
        if (null === $this->logger) {
            return parent::reverse($latitude, $longitude);
        }

        $startTime = microtime(true);
        $results = parent::reverse($latitude, $longitude);
        $duration = (microtime(true) - $startTime) * 1000;

        $value = sprintf('[Reverse geocoding] latitude: %s, longitude: %s', $latitude, $longitude);

        $this->logger->logRequest($value, $duration, $this->getProviderClass(), $results);

        return $results;
    }

    protected function getProviderClass()
    {
        $provider = explode('\\', get_class($this->getProvider()));

        return end($provider);
    }
}
