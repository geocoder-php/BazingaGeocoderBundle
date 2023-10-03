<?php

declare(strict_types=1);

/*
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Bazinga\GeocoderBundle\DataCollector;

use Bazinga\GeocoderBundle\Plugin\ProfilingPlugin;
use Geocoder\Query\Query;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

/**
 * @author Michal Dabrowski <dabrowski@brillante.pl>
 */
class GeocoderDataCollector extends DataCollector
{
    /**
     * @var ProfilingPlugin[]
     */
    private array $instances = [];

    public function __construct()
    {
        $this->data['queries'] = [];
        $this->data['providers'] = [];
    }

    /**
     * @return void
     */
    public function reset()
    {
        $this->instances = [];
        $this->data['queries'] = [];
        $this->data['providers'] = [];
    }

    /**
     * @return void
     */
    public function collect(Request $request, Response $response, \Throwable $exception = null)
    {
        if (!empty($this->data['queries'])) {
            // To avoid collection more that once.
            return;
        }

        $instances = $this->instances;

        foreach ($instances as $instance) {
            foreach ($instance->getQueries() as $query) {
                $query['query'] = $this->cloneVar($query['query']);
                $query['result'] = $this->cloneVar($query['result']);
                $this->data['queries'][] = $query;
            }
        }
    }

    /**
     * Returns an array of collected requests.
     *
     * @return list<array{query: Query, queryString: string, duration: float, providerName: string, result: mixed, resultCount: int}>
     */
    public function getQueries(): array
    {
        return $this->data['queries'];
    }

    /**
     * Returns the execution time of all collected requests in seconds.
     */
    public function getTotalDuration(): float
    {
        $time = 0;
        foreach ($this->data['queries'] as $command) {
            $time += $command['duration'];
        }

        return $time;
    }

    /**
     * @return string[]
     */
    public function getProviders(): array
    {
        return $this->data['providers'];
    }

    /**
     * @return list<array{query: Query, queryString: string, duration: float, providerName: string, result: mixed, resultCount: int}>
     */
    public function getProviderQueries(string $provider): array
    {
        return array_filter($this->data['queries'], static function ($data) use ($provider) {
            return $data['providerName'] === $provider;
        });
    }

    /**
     * @return void
     */
    public function addInstance(ProfilingPlugin $instance)
    {
        $this->instances[] = $instance;
        $this->data['providers'][] = $instance->getName();
    }

    public function getName(): string
    {
        return 'geocoder';
    }
}
