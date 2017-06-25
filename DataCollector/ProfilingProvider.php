<?php

namespace Bazinga\Bundle\GeocoderBundle\DataCollector;

use Bazinga\Bundle\GeocoderBundle\Logger\GeocoderLogger;
use Geocoder\Collection;
use Geocoder\Provider\Provider;
use Geocoder\Query\GeocodeQuery;
use Geocoder\Query\ReverseQuery;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class ProfilingProvider implements Provider
{
    /**
     * @var Provider
     */
    private $realProvider;

    /**
     * @var GeocoderLogger
     */
    private $logger;

    /**
     * @param Provider       $realProvider
     * @param GeocoderLogger $logger
     */
    public function __construct(Provider $realProvider, GeocoderLogger $logger)
    {
        $this->realProvider = $realProvider;
        $this->logger = $logger;
    }

    public function geocodeQuery(GeocodeQuery $query): Collection
    {
        $startTime = microtime(true);
        try {
            $results = $this->realProvider->geocodeQuery($query);
        } finally {
            $duration = (microtime(true) - $startTime) * 1000;

            $this->logger->logRequest(
                sprintf('[Geocoding] %s', $query),
                $duration,
                $this->getName(),
                $results
            );
        }

        return $results;
    }

    public function reverseQuery(ReverseQuery $query): Collection
    {
        $startTime = microtime(true);
        try {
            $results = $this->realProvider->reverseQuery($query);
        } finally {
            $duration = (microtime(true) - $startTime) * 1000;

            $this->logger->logRequest(
                sprintf('[Geocoding] %s', $query),
                $duration,
                $this->getName(),
                $results
            );
        }

        return $results;
    }

    public function __call($method, $args)
    {
        return call_user_func_array([$this->realProvider, $method], $args);
    }

    public function getName(): string
    {
        return $this->realProvider->getName();
    }
}
