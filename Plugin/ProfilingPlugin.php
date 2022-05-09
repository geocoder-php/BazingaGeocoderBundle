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
use Geocoder\Exception\Exception;
use Geocoder\Exception\LogicException;
use Geocoder\Plugin\Plugin;
use Geocoder\Query\GeocodeQuery;
use Geocoder\Query\Query;
use Geocoder\Query\ReverseQuery;
use Http\Promise\Promise;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class ProfilingPlugin implements Plugin
{
    /**
     * @phpstan-var array<int, array{query: Query, queryString: string, duration: float, providerName: string, result: mixed, resultCount: int}>
     */
    private array $queries = [];

    /**
     * @param string $name service id of the provider
     */
    public function __construct(private string $name)
    {
    }

    public function handleQuery(Query $query, callable $next, callable $first): Promise
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

    private function logQuery(Query $query, float $duration, mixed $result = null): void
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
     * @phpstan-return array<int, array{query: Query, queryString: string, duration: float, providerName: string, result: mixed, resultCount: int}>
     */
    public function getQueries(): array
    {
        return $this->queries;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
