<?php

namespace Bazinga\Bundle\GeocoderBundle\Doctrine\ORM;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Bazinga\Bundle\GeocoderBundle\Mapping\Driver\DriverInterface;
use Geocoder\Geocoder;
use Geocoder\Provider\Provider;
use Geocoder\Query\GeocodeQuery;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class GeocoderListener implements EventSubscriber
{
    /**
     * @var DriverInterface
     */
    private $driver;

    /**
     * @var Provider
     */
    private $geocoder;

    public function __construct(Provider $geocoder, DriverInterface $driver)
    {
        $this->driver = $driver;
        $this->geocoder = $geocoder;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return array(
            Events::onFlush,
        );
    }

    public function onFlush(OnFlushEventArgs $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            if (!$this->driver->isGeocodeable($entity)) {
                continue;
            }

            $this->geocodeEntity($entity);

            $uow->recomputeSingleEntityChangeSet(
                $em->getClassMetadata(get_class($entity)),
                $entity
            );
        }

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if (!$this->driver->isGeocodeable($entity)) {
                continue;
            }

            $this->geocodeEntity($entity);

            $uow->recomputeSingleEntityChangeSet(
                $em->getClassMetadata(get_class($entity)),
                $entity
            );
        }
    }

    private function geocodeEntity($entity)
    {
        $metadata = $this->driver->loadMetadataFromObject($entity);
        $address = $metadata->addressProperty->getValue($entity);
        $results = $this->geocoder->geocodeQuery(GeocodeQuery::create($address));

        if (!empty($results)) {
            $result = $results->first();
            $metadata->latitudeProperty->setValue($entity, $result->getCoordinates()->getLatitude());
            $metadata->longitudeProperty->setValue($entity, $result->getCoordinates()->getLongitude());
        }
    }
}
