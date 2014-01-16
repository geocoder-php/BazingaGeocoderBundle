<?php

/**
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Bazinga\Bundle\GeocoderBundle\Logger;

use Symfony\Component\HttpKernel\Log\LoggerInterface;

/**
 * @author Michal Dabrowski <dabrowski@brillante.pl>
 */
class GeocoderLogger
{
    protected $logger;

    protected $requests = array();

    /**
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    /**
     *
     * @param string $value         value to geocode
     * @param float  $duration
     * @param string $providerClass Geocoder provider class
     * @param mixed  $result
     */
    public function logRequest($value, $duration, $providerClass, $result)
    {
        if (null !== $this->logger) {
            $this->requests[] = array(
                'value'         => $value,
                'duration'      => $duration,
                'providerClass' => $providerClass,
                'result'        => $result
            );

            $this->logger->info(sprintf("%s %0.2f ms (%s)",
                $value,
                $duration,
                $providerClass
            ));
        }
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
