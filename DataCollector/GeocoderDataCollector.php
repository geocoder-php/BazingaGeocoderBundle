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
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

/**
 * @author Michal Dabrowski <dabrowski@brillante.pl>
 */
class GeocoderDataCollector extends DataCollector
{
    use DataCollectorSymfonyCompatibilityTrait;

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
     * Returns an array of collected requests.
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

    public function getProviders(): array
    {
        return $this->data['providers'];
    }

    public function getProviderQueries(string $provider): array
    {
        return array_filter($this->data['queries'], function ($data) use ($provider) {
            return $data['providerName'] === $provider;
        });
    }

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
