<?php

namespace Bazinga\Bundle\GeocoderBundle\Tests\Doctrine\ORM;

use Bazinga\Bundle\GeocoderBundle\Doctrine\ORM\GeocoderListener;
use Bazinga\Bundle\GeocoderBundle\Mapping\Driver\AnnotationDriver;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\SimpleAnnotationReader;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\Tests\OrmTestCase;
use Ivory\HttpAdapter\CurlHttpAdapter;
use Geocoder\Provider\GoogleMaps;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class GeocoderListenerTest extends OrmTestCase
{
    /**
     * @var EntityManager
     */
    private $em;

    protected function setUp()
    {
        AnnotationRegistry::registerLoader('class_exists');

        $conn = DriverManager::getConnection(array(
            'driver' => 'pdo_sqlite',
            'memory' => true,
        ));

        $this->em = $this->_getTestEntityManager($conn);

        $reader = new SimpleAnnotationReader();
        $reader->addNamespace('Bazinga\Bundle\GeocoderBundle\Mapping\Annotations');
        $reader->addNamespace('Doctrine\ORM\Mapping');

        $driver = new AnnotationDriver($reader);

        $geocoder = new GoogleMaps(new CurlHttpAdapter());

        $this->listener = new GeocoderListener($geocoder, $driver);

        $this->em->getEventManager()->addEventSubscriber($this->listener);

        $sm = new SchemaTool($this->em);
        $sm->createSchema(array(
            $this->em->getClassMetadata('Bazinga\Bundle\GeocoderBundle\Tests\Doctrine\ORM\Dummy'),
        ));
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
