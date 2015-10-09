<?php

/**
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */
namespace Bazinga\Bundle\GeocoderBundle\DataCollector;

use Bazinga\Bundle\GeocoderBundle\Logger\GeocoderLogger;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Michal Dabrowski <dabrowski@brillante.pl>
 */
class GeocoderDataCollector extends DataCollector
{
    /**
     * @var GeocoderLogger
     */
    protected $logger;

    /**
     * @param GeocoderLogger $logger
     */
    public function __construct(GeocoderLogger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data = array(
            'requests' => null !== $this->logger ? $this->logger->getRequests() : array(),
        );
    }

    /**
     * Returns an array of collected requests.
     *
     * @return array
     */
    public function getRequests()
    {
        return $this->data['requests'];
    }

    /**
     * Returns the number of collected requests.
     *
     * @return int
     */
    public function getRequestsCount()
    {
        return count($this->data['requests']);
    }

    /**
     * Returns the execution time of all collected requests in seconds.
     *
     * @return float
     */
    public function getTime()
    {
        $time = 0;
        foreach ($this->data['requests'] as $command) {
            $time += $command['duration'];
        }

        return $time;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'geocoder';
    }
}
