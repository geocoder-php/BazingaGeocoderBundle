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
    private $instances = [];

    public function __construct()
    {
        $this->data['queries'] = [];
        $this->data['providers'] = [];
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        $this->instances = [];
        $this->data['queries'] = [];
        $this->data['providers'] = [];
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        if (!empty($this->data['queries'])) {
            // To avoid collection more that once.
            return;
        }
        foreach ($this->instances as $instance) {
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
     * @return array
     */
    public function getQueries(): array
    {
        return $this->data['queries'];
    }

    /**
     * Returns the execution time of all collected requests in seconds.
     *
     * @return float
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
     * @return array
     */
    public function getProviders(): array
    {
        return $this->data['providers'];
    }

    /**
     * @param string $provider
     *
     * @return array
     */
    public function getProviderQueries(string $provider): array
    {
        return array_filter($this->data['queries'], function ($data) use ($provider) {
            return $data['providerName'] === $provider;
        });
    }

    /**
     * @param ProfilingPlugin $instance
     */
    public function addInstance(ProfilingPlugin $instance)
    {
        $this->instances[] = $instance;
        $this->data['providers'][] = $instance->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'geocoder';
    }
}
