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
final class GeocoderListener implements EventSubscriber
{
    public function __construct(
        private readonly Provider $geocoder,
        private readonly DriverInterface $driver,
    ) {
    }

    /**
     * @return list<string>
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::onFlush,
        ];
    }

    public function onFlush(OnFlushEventArgs $args): void
    {
        $em = $args->getObjectManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            if (!$this->driver->isGeocodeable($entity)) {
                continue;
            }

            $metadata = $this->driver->loadMetadataFromObject($entity);

            $this->geocodeEntity($metadata, $entity);

            $uow->recomputeSingleEntityChangeSet(
                $em->getClassMetadata($entity::class),
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
                $em->getClassMetadata($entity::class),
                $entity
            );
        }
    }

    private function geocodeEntity(ClassMetadata $metadata, object $entity): void
    {
        if (null !== $metadata->addressGetter) {
            $address = $metadata->addressGetter->invoke($entity);
        } elseif (null !== $metadata->addressProperty) {
            $address = $metadata->addressProperty->getValue($entity);
        } else {
            $address = '';
        }

        if (!is_string($address) && !$address instanceof \Stringable) {
            return;
        }

        $addressString = (string) $address;
        if ('' === $addressString) {
            return;
        }

        $results = $this->geocoder->geocodeQuery(GeocodeQuery::create($addressString));

        if (!$results->isEmpty()) {
            $result = $results->first();
            $metadata->latitudeProperty?->setValue($entity, $result->getCoordinates()->getLatitude());
            $metadata->longitudeProperty?->setValue($entity, $result->getCoordinates()->getLongitude());
        }
    }

    private function shouldGeocode(ClassMetadata $metadata, UnitOfWork $unitOfWork, object $entity): bool
    {
        if (null !== $metadata->addressGetter) {
            return true;
        }

        $changeSet = $unitOfWork->getEntityChangeSet($entity);

        return isset($changeSet[$metadata->addressProperty?->getName() ?? '']);
    }
}
