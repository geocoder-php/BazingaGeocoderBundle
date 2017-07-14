<?php

/**
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Bazinga\Bundle\GeocoderBundle\Logger;

use Geocoder\Collection;
use Geocoder\Location;
use Psr\Log\LoggerInterface;

/**
 * @author Michal Dabrowski <dabrowski@brillante.pl>
 */
class GeocoderLogger
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var array
     */
    private $requests = [];

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    /**
     * @param string     $value         value to geocode
     * @param float      $duration      geocoding duration
     * @param string     $providerClass Geocoder provider class name
     * @param Collection $results
     */
    public function logRequest(string $value, float $duration, string $providerClass, Collection $results)
    {
        if (null !== $this->logger) {
            $this->logger->info(sprintf('%s %0.2f ms (%s)', $value, $duration, $providerClass));
        }

        $data = [];
        /** @var Location $result */
        foreach ($results as $result) {
            $data[] = $result->toArray();
        }

        $this->requests[] = [
            'value'         => $value,
            'duration'      => $duration,
            'providerClass' => $providerClass,
            'result'        => json_encode($data),
        ];
    }

    /**
     * Returns an array of the logged requests.
     *
     * @return array
     */
    public function getRequests()
    {
        return $this->requests;
    }
}
