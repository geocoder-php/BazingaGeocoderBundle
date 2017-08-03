<?php

declare(strict_types=1);

/*
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Bazinga\GeocoderBundle\Plugin;

use Geocoder\Collection;
use Geocoder\Exception\LogicException;
use Geocoder\Plugin\Plugin;
use Geocoder\Query\GeocodeQuery;
use Geocoder\Query\Query;
use Geocoder\Query\ReverseQuery;
use Geocoder\Exception\Exception;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class ProfilingPlugin implements Plugin
{
    /**
     * @var array
     */
    private $queries = [];

    /**
     * @var string service id of the provider;
     */
    private $name;

    /**
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function handleQuery(Query $query, callable $next, callable $first)
    {
        $startTime = microtime(true);

        return $next($query)->then(function (Collection $result) use ($query, $startTime) {
            $duration = (microtime(true) - $startTime) * 1000;
            $this->logQuery($query, $duration, $result);

            return $result;
        }, function (Exception $exception) use ($query, $startTime) {
            $duration = (microtime(true) - $startTime) * 1000;
            $this->logQuery($query, $duration, $exception);

            throw $exception;
        });
    }

    /**
     * @param Query                $query
     * @param float                $duration geocoding duration
     * @param Collection|Exception $result
     */
    private function logQuery(Query $query, float $duration, $result = null)
    {
        if ($query instanceof GeocodeQuery) {
            $queryString = $query->getText();
        } elseif ($query instanceof ReverseQuery) {
            $queryString = sprintf('(%s, %s)', $query->getCoordinates()->getLongitude(), $query->getCoordinates()->getLatitude());
        } else {
            throw new LogicException('First parameter to ProfilingProvider::logQuery must be a Query');
        }

        $this->queries[] = [
            'query' => $query,
            'queryString' => $queryString,
            'duration' => $duration,
            'providerName' => $this->getName(),
            'result' => $result,
            'resultCount' => $result instanceof Collection ? $result->count() : 0,
        ];
    }

    /**
     * @return array
     */
    public function getQueries(): array
    {
        return $this->queries;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
