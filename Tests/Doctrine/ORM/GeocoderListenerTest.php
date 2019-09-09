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
use Geocoder\Provider\Nominatim\Nominatim;
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
        $geocoder = Nominatim::withOpenStreetMapServer(new Client(), 'BazingaGeocoderBundle/Test');
        $this->listener = new GeocoderListener($geocoder, $driver);

        $this->em->getEventManager()->addEventSubscriber($this->listener);

        $sm = new SchemaTool($this->em);
        $sm->createSchema([
            $this->em->getClassMetadata('Bazinga\GeocoderBundle\Tests\Doctrine\ORM\DummyWithProperty'),
            $this->em->getClassMetadata('Bazinga\GeocoderBundle\Tests\Doctrine\ORM\DummyWithEmptyProperty'),
            $this->em->getClassMetadata('Bazinga\GeocoderBundle\Tests\Doctrine\ORM\DummyWithGetter'),
            $this->em->getClassMetadata('Bazinga\GeocoderBundle\Tests\Doctrine\ORM\DummyWithInvalidGetter'),
        ]);
    }

    public function testPersistForProperty()
    {
        $dummy = new DummyWithProperty();
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

    public function testPersistForGetter()
    {
        $dummy = new DummyWithGetter();
        $dummy->setAddress('Berlin, Germany');

        $this->em->persist($dummy);
        $this->em->flush();

        $this->assertNotNull($dummy->getLatitude());
        $this->assertNotNull($dummy->getLongitude());

        $clone = clone $dummy;
        $dummy->setAddress('Paris, France');

        $this->em->persist($dummy);
        $this->em->flush();

        $this->assertNotEquals($clone->getLatitude(), $dummy->getLatitude());
        $this->assertNotEquals($clone->getLongitude(), $dummy->getLongitude());
    }

    public function testPersistForInvalidGetter()
    {
        $dummy = new DummyWithInvalidGetter();
        $dummy->setAddress('Berlin, Germany');

        $this->em->persist($dummy);

        $this->expectException(\Exception::class);

        $this->em->flush();
    }

    public function testPersistForEmptyProperty()
    {
        $dummy = new DummyWithEmptyProperty();
        $dummy->address = '';

        $this->em->persist($dummy);
        $this->em->flush();

        $this->assertNull($dummy->latitude);
        $this->assertNull($dummy->longitude);
    }
}

/**
 * @Geocodeable
 * @Entity
 */
class DummyWithProperty
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

/**
 * @Geocodeable
 * @Entity
 */
class DummyWithGetter
{
    /**
     * @Id @GeneratedValue
     * @Column(type="integer")
     */
    private $id;

    /**
     * @Latitude
     * @Column
     */
    private $latitude;

    /**
     * @Longitude
     * @Column
     */
    private $longitude;

    /**
     * @Column
     */
    private $_address;

    public function setAddress($address)
    {
        $this->_address = $address;
    }

    /**
     * @Address
     */
    public function getAddress()
    {
        return $this->_address;
    }

    public function getLatitude()
    {
        return $this->latitude;
    }

    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }

    public function getLongitude()
    {
        return $this->longitude;
    }

    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    }
}

/**
 * @Geocodeable
 * @Entity
 */
class DummyWithInvalidGetter
{
    /**
     * @Id @GeneratedValue
     * @Column(type="integer")
     */
    private $id;

    /**
     * @Latitude
     * @Column
     */
    private $latitude;

    /**
     * @Longitude
     * @Column
     */
    private $longitude;

    /**
     * @Column
     */
    private $_address;

    public function setAddress($address)
    {
        $this->_address = $address;
    }

    /**
     * @Address
     */
    public function getAddress($requiredParameter)
    {
        return $this->_address;
    }

    public function getLatitude()
    {
        return $this->latitude;
    }

    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }

    public function getLongitude()
    {
        return $this->longitude;
    }

    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    }
}

/**
 * @Geocodeable
 * @Entity
 */
class DummyWithEmptyProperty
{
    /**
     * @Id @GeneratedValue
     * @Column(type="integer")
     */
    public $id;

    /**
     * @Latitude
     * @Column(nullable=true)
     */
    public $latitude;

    /**
     * @Longitude
     * @Column(nullable=true)
     */
    public $longitude;

    /**
     * @Address
     * @Column
     */
    public $address;
}
