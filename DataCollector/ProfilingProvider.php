<?php

/*
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Bazinga\Bundle\GeocoderBundle\DataCollector;

use Geocoder\Collection;
use Geocoder\Exception\LogicException;
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
     * @var array
     */
    private $queries = [];

    /**
     * @param Provider $realProvider
     */
    public function __construct(Provider $realProvider)
    {
        $this->realProvider = $realProvider;
    }

    public function geocodeQuery(GeocodeQuery $query): Collection
    {
        $startTime = microtime(true);
        try {
            $result = $this->realProvider->geocodeQuery($query);
        } finally {
            $duration = (microtime(true) - $startTime) * 1000;

            $this->logQuery($query, $duration, $result);
        }

        return $result;
    }

    public function reverseQuery(ReverseQuery $query): Collection
    {
        $startTime = microtime(true);
        try {
            $result = $this->realProvider->reverseQuery($query);
        } finally {
            $duration = (microtime(true) - $startTime) * 1000;

            $this->logQuery($query, $duration, $result);
        }

        return $result;
    }

    /**
     * @param GeocodeQuery|ReverseQuery $query
     * @param float                     $duration geocoding duration
     * @param Collection                $result
     */
    private function logQuery($query, float $duration, Collection $result = null)
    {
        if ($query instanceof GeocodeQuery) {
            $queryString = $query->getText();
        } elseif ($query instanceof ReverseQuery) {
            $queryString = sprintf('(%s, %s)', $query->getCoordinates()->getLongitude(), $query->getCoordinates()->getLatitude());
        } else {
            throw new LogicException('First parameter to ProfilingProvider::logQuery must be a query');
        }

        $this->queries[] = [
            'query' => $query,
            'queryString' => $queryString,
            'duration' => $duration,
            'providerName' => $this->getName(),
            'result' => $result,
        ];
    }

    /**
     * @return array
     */
    public function getQueries(): array
    {
        return $this->queries;
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
