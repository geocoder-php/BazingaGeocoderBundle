<?php

/**
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */
namespace Bazinga\Bundle\GeocoderBundle\Logger;

use Psr\Log\LoggerInterface;
use Geocoder\Model\AddressCollection;
use Geocoder\Model\Address;

/**
 * @author Michal Dabrowski <dabrowski@brillante.pl>
 */
class GeocoderLogger
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var array
     */
    protected $requests = array();

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    /**
     * @param string            $value         value to geocode
     * @param float             $duration      geocoding duration
     * @param string            $providerClass Geocoder provider class name
     * @param AddressCollection $results
     */
    public function logRequest($value, $duration, $providerClass, AddressCollection $results)
    {
        if (null !== $this->logger) {
            $this->logger->info(sprintf('%s %0.2f ms (%s)', $value, $duration, $providerClass));
        }

        $data = array();

        /** @var Address $result */
        foreach ($results as $result) {
            $data[] = $result->toArray();
        }

        $this->requests[] = array(
            'value' => $value,
            'duration' => $duration,
            'providerClass' => $providerClass,
            'result' => json_encode($data),
        );
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
