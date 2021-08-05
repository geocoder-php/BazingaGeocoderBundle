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
use Geocoder\Query\GeocodeQuery;
use Symfony\Component\DependencyInjection\ServiceLocator;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 * @author Pierre du Plessis <pdples@gmail.com>
 */
class GeocodeEntityListener implements EventSubscriber
{
    /**
     * @var DriverInterface
     */
    private $driver;

    /**
     * @var ServiceLocator
     */
    private $providerLocator;

    public function __construct(ServiceLocator $providerLocator, DriverInterface $driver)
    {
        $this->driver = $driver;
        $this->providerLocator = $providerLocator;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            Events::onFlush,
        ];
    }

    public function onFlush(OnFlushEventArgs $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            if (!$this->driver->isGeocodeable($entity)) {
                continue;
            }

            /** @var ClassMetadata $metadata */
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

            /** @var ClassMetadata $metadata */
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

    /**
     * @param object $entity
     */
    private function geocodeEntity(ClassMetadata $metadata, $entity)
    {
        if (null !== $metadata->addressGetter) {
            $address = $metadata->addressGetter->invoke($entity);
        } else {
            $address = $metadata->addressProperty->getValue($entity);
        }

        if (empty($address)) {
            return;
        }

        $serviceId = sprintf('bazinga_geocoder.provider.%s', $metadata->provider);

        if (!$this->providerLocator->has($serviceId)) {
            throw new \RuntimeException(sprintf('The provider "%s" is invalid for object "%s".', $metadata->provider, get_class($entity)));
        }

        $results = $this->providerLocator->get($serviceId)->geocodeQuery(GeocodeQuery::create($address));

        if (!$results->isEmpty()) {
            $result = $results->first();
            $metadata->latitudeProperty->setValue($entity, $result->getCoordinates()->getLatitude());
            $metadata->longitudeProperty->setValue($entity, $result->getCoordinates()->getLongitude());
        }
    }

    /**
     * @param object $entity
     */
    private function shouldGeocode(ClassMetadata $metadata, UnitOfWork $unitOfWork, $entity): bool
    {
        if (null !== $metadata->addressGetter) {
            return true;
        }

        $changeSet = $unitOfWork->getEntityChangeSet($entity);

        return isset($changeSet[$metadata->addressProperty->getName()]);
    }
}
