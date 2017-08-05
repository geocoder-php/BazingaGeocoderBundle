<?php

declare(strict_types=1);

/*
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Bazinga\GeocoderBundle\Tests\Doctrine\ORM;

use Bazinga\GeocoderBundle\Doctrine\ORM\GeocoderListener;
use Bazinga\GeocoderBundle\Mapping\Driver\AnnotationDriver;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\SimpleAnnotationReader;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\Tests\DoctrineTestCase;
use Doctrine\Tests\OrmTestCase;
use Geocoder\Provider\GoogleMaps\GoogleMaps;
use Http\Client\Curl\Client;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class GeocoderListenerTest extends OrmTestCase
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var GeocoderListener
     */
    private $listener;

    public static function setUpBeforeClass()
    {
        if (!class_exists(DoctrineTestCase::class)) {
            /*
             * We check for DoctrineTestCase because it is in the same package as OrmTestCase and we want to be able to
             * fake OrmTestCase
             */
            static::fail('Doctrine\Tests\OrmTestCase was not found.');
        }
        parent::setUpBeforeClass();
    }

    protected function setUp()
    {
        AnnotationRegistry::registerLoader('class_exists');

        $conn = DriverManager::getConnection([
            'driver' => 'pdo_sqlite',
            'memory' => true,
        ]);

        $this->em = $this->_getTestEntityManager($conn);

        $reader = new SimpleAnnotationReader();
        $reader->addNamespace('Bazinga\GeocoderBundle\Mapping\Annotations');
        $reader->addNamespace('Doctrine\ORM\Mapping');

        $driver = new AnnotationDriver($reader);

        $geocoder = new GoogleMaps(new Client());

        $this->listener = new GeocoderListener($geocoder, $driver);

        $this->em->getEventManager()->addEventSubscriber($this->listener);

        $sm = new SchemaTool($this->em);
        $sm->createSchema([
            $this->em->getClassMetadata('Bazinga\GeocoderBundle\Tests\Doctrine\ORM\Dummy'),
        ]);
    }

    public function testPersist()
    {
        $dummy = new Dummy();
        $dummy->address = 'Berlin, Germany';

        $this->em->persist($dummy);
        $this->em->flush();

        $this->assertNotNull($dummy->latitude);
        $this->assertNotNull($dummy->longitude);

        $clone = clone $dummy;
        $dummy->address = 'Paris, France';

        $this->em->persist($dummy);
        $this->em->flush();

        $this->assertNotEquals($clone->latitude, $dummy->latitude);
        $this->assertNotEquals($clone->longitude, $dummy->longitude);
    }
}

/**
 * @Geocodeable
 * @Entity
 */
class Dummy
{
    /**
     * @Id @GeneratedValue
     * @Column(type="integer")
     */
    public $id;

    /**
     * @Latitude
     * @Column
     */
    public $latitude;

    /**
     * @Longitude
     * @Column
     */
    public $longitude;

    /**
     * @Address
     * @Column
     */
    public $address;
}
