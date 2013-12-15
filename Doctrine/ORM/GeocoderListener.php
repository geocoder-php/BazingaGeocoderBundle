<?php

namespace Bazinga\Bundle\GeocoderBundle\Doctrine\ORM;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\OnFlushEventArgs;

use Bazinga\Bundle\GeocoderBundle\Mapping\Driver\DriverInterface;
use Geocoder\Geocoder;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class GeocoderListener implements EventSubscriber
{
    private $driver;

    private $geocoder;

    public function __construct(Geocoder $geocoder, DriverInterface $driver)
    {
        $this->driver = $driver;
        $this->geocoder = $geocoder;
    }

    /**
     * {@inheritDoc}
     */
    public function getSubscribedEvents()
    {
        return array(
            Events::onFlush
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
        $result = $this->geocoder->geocode($address);

        $metadata->latitudeProperty->setValue($entity, $result['latitude']);
        $metadata->longitudeProperty->setValue($entity, $result['longitude']);
    }
}
