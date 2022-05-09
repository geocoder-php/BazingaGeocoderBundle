<?php

declare(strict_types=1);

/*
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Bazinga\GeocoderBundle\Doctrine\ORM;

use Bazinga\GeocoderBundle\Mapping\ClassMetadata;
use Bazinga\GeocoderBundle\Mapping\Driver\DriverInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\UnitOfWork;
use Geocoder\Provider\Provider;
use Geocoder\Query\GeocodeQuery;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class GeocoderListener implements EventSubscriber
{
    public function __construct(private Provider $geocoder, private DriverInterface $driver)
    {
    }

    /**
     * {@inheritdoc}
     *
     * @phpstan-return list<string>
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::onFlush,
        ];
    }

    public function onFlush(OnFlushEventArgs $args): void
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            if (!$this->driver->isGeocodeable($entity)) {
                continue;
            }

            $metadata = $this->driver->loadMetadataFromObject($entity);

            $this->geocodeEntity($metadata, $entity);

            $uow->recomputeSingleEntityChangeSet(
                $em->getClassMetadata(get_class($entity)),
                $entity
            );
        }

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if (!$this->driver->isGeocodeable($entity)) {
                continue;
            }

            $metadata = $this->driver->loadMetadataFromObject($entity);

            if (!$this->shouldGeocode($metadata, $uow, $entity)) {
                continue;
            }

            $this->geocodeEntity($metadata, $entity);

            $uow->recomputeSingleEntityChangeSet(
                $em->getClassMetadata(get_class($entity)),
                $entity
            );
        }
    }

    private function geocodeEntity(ClassMetadata $metadata, object $entity): void
    {
        if (null !== $metadata->addressGetter) {
            $address = $metadata->addressGetter->invoke($entity);
        } else {
            $address = $metadata->addressProperty->getValue($entity);
        }

        if (empty($address) || !is_string($address)) {
            return;
        }

        $results = $this->geocoder->geocodeQuery(GeocodeQuery::create($address));

        if (!$results->isEmpty()) {
            $result = $results->first();
            $metadata->latitudeProperty->setValue($entity, $result->getCoordinates()->getLatitude());
            $metadata->longitudeProperty->setValue($entity, $result->getCoordinates()->getLongitude());
        }
    }

    private function shouldGeocode(ClassMetadata $metadata, UnitOfWork $unitOfWork, object $entity): bool
    {
        if (null !== $metadata->addressGetter) {
            return true;
        }

        $changeSet = $unitOfWork->getEntityChangeSet($entity);

        return isset($changeSet[$metadata->addressProperty->getName()]);
    }
}
