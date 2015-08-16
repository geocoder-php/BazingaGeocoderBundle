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
     * @param string                     $value         value to geocode
     * @param float                      $duration      geocoding duration
     * @param string                     $providerClass Geocoder provider class name
     * @param \SplObjectStorage|Geocoded $results
     */
    public function logRequest($value, $duration, $providerClass, $results)
    {
        if (null !== $this->logger) {
            $this->logger->info(sprintf('%s %0.2f ms (%s)', $value, $duration, $providerClass));
        }

        $data = array();

        if ($results instanceof \SplObjectStorage) {
            $data = array();
            foreach ($results as $result) {
                $data[] = $result->toArray();
            }
        } else {
            $data = $results->toArray();
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
