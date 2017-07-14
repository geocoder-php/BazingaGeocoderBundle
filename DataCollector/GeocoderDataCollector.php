<?php

/**
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Bazinga\Bundle\GeocoderBundle\DataCollector;

use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Michal Dabrowski <dabrowski@brillante.pl>
 */
class GeocoderDataCollector extends DataCollector
{
    /**
     * @var ProfilingProvider[]
     */
    private $instances = [];

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
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
    public function getQueries()
    {
        return $this->data['queries'];
    }

    /**
     * Returns the number of collected requests.
     *
     * @return int
     */
    public function getQueryCount()
    {
        return count($this->data['queries']);
    }

    /**
     * Returns the execution time of all collected requests in seconds.
     *
     * @return float
     */
    public function getTime()
    {
        $time = 0;
        foreach ($this->data['queries'] as $command) {
            $time += $command['duration'];
        }

        return $time;
    }

    /**
     * @param ProfilingProvider $instance
     */
    public function addInstance(ProfilingProvider $instance)
    {
        $this->instances[] = $instance;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'geocoder';
    }
}
