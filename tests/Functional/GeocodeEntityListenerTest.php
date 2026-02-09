<?php

declare(strict_types=1);

/*
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Bazinga\GeocoderBundle\Tests\Functional;

use Bazinga\GeocoderBundle\BazingaGeocoderBundle;
use Bazinga\GeocoderBundle\Tests\Functional\Fixtures\Entity\DummyWithEmptyProperty;
use Bazinga\GeocoderBundle\Tests\Functional\Fixtures\Entity\DummyWithGetter;
use Bazinga\GeocoderBundle\Tests\Functional\Fixtures\Entity\DummyWithInvalidGetter;
use Bazinga\GeocoderBundle\Tests\Functional\Fixtures\Entity\DummyWithProperty;
use Bazinga\GeocoderBundle\Tests\Functional\Fixtures\Entity\DummyWithStringableGetter;
use Bazinga\GeocoderBundle\Tests\Functional\Fixtures\Entity\StringableAddress;
use Composer\InstalledVersions;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Nyholm\BundleTest\TestKernel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpKernel\Kernel;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
final class GeocodeEntityListenerTest extends KernelTestCase
{
    private const QUERY_BERLIN = 'Brandenburger Tor Berlin, Germany';
    private const QUERY_PARIS = 'Tour Eiffel Paris, France';

    protected function tearDown(): void
    {
        /** @var EntityManagerInterface $em */
        $em = self::getContainer()->get('doctrine.orm.entity_manager');

        $tool = new SchemaTool($em);
        $tool->dropSchema($em->getMetadataFactory()->getAllMetadata());
    }

    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    /**
     * @param array<mixed> $options
     */
    protected static function createKernel(array $options = []): TestKernel
    {
        /**
         * @var TestKernel $kernel
         */
        $kernel = parent::createKernel($options);
        $kernel->addTestBundle(DoctrineBundle::class);
        $kernel->addTestBundle(BazingaGeocoderBundle::class);
        $kernel->addTestCompilerPass(new class implements CompilerPassInterface {
            public function process(ContainerBuilder $container): void
            {
                $container
                    ->getDefinition('http_client')
                    ->setPublic(true);
            }
        });
        $kernel->addTestConfig(static function (ContainerBuilder $container) {
            $orm = [];
            $ormVersion = InstalledVersions::getVersion('doctrine/orm');
            // doctrine-bundle
            if (null !== $doctrineBundleVersion = InstalledVersions::getVersion('doctrine/doctrine-bundle')) {
                // v2
                if (version_compare($doctrineBundleVersion, '3.0.0', '<')) {
                    $orm['auto_generate_proxy_classes'] = true;
                    $orm['controller_resolver']['auto_mapping'] = false;
                    $orm['report_fields_where_declared'] = true;
                }
                if (method_exists(Configuration::class, 'setLazyGhostObjectEnabled')
                    && Kernel::VERSION_ID >= 60100
                    && version_compare($doctrineBundleVersion, '2.8.0', '>=')
                    && version_compare($ormVersion, '3.0', '<=')
                ) {
                    $orm['enable_lazy_ghost_objects'] = true;
                }
                if (\PHP_VERSION_ID >= 80400
                    && version_compare($doctrineBundleVersion, '2.15.0', '>=')
                    && version_compare($doctrineBundleVersion, '3.1.0', '<')
                    && version_compare($ormVersion, '3.4.0', '>=')
                ) {
                    $orm['enable_native_lazy_objects'] = true;
                }
            }
            $container->prependExtensionConfig('doctrine', [
                'orm' => $orm,
            ]);
        });

        $kernel->handleOptions($options);
        $kernel->setClearCacheAfterShutdown(false);

        return $kernel;
    }

    public function testPersistForProperty(): void
    {
        self::bootKernel(['config' => static function (TestKernel $kernel) {
            $kernel->addTestConfig(__DIR__.'/config/framework.yml');
            $kernel->addTestConfig(__DIR__.'/config/framework_sf'.$kernel::MAJOR_VERSION.'.yml');
            $kernel->addTestConfig(__DIR__.'/config/listener.yml');
        }]);

        $container = self::getContainer();
        $container->set('http_client', self::createHttpClientForBerlinQuery());

        $dummy = new DummyWithProperty();
        $dummy->address = self::QUERY_BERLIN;

        /** @var EntityManagerInterface $em */
        $em = $container->get('doctrine.orm.entity_manager');

        $tool = new SchemaTool($em);
        $tool->createSchema($em->getMetadataFactory()->getAllMetadata());

        $em->persist($dummy);
        $em->flush();

        self::assertNotNull($dummy->latitude);
        self::assertNotNull($dummy->longitude);
        self::assertNotNull($dummy->north);
        self::assertNotNull($dummy->south);
        self::assertNotNull($dummy->east);
        self::assertNotNull($dummy->west);
        self::assertNotNull($dummy->streetNumber);
        self::assertNotNull($dummy->streetName);
        self::assertNotNull($dummy->locality);
        self::assertNotNull($dummy->postalCode);
        self::assertNotNull($dummy->subLocality);
        self::assertNotNull($dummy->country);

        $clone = clone $dummy;
        $dummy->address = self::QUERY_PARIS;

        $em->persist($dummy);
        $em->flush();

        self::assertNotEquals($clone->latitude, $dummy->latitude);
        self::assertNotEquals($clone->longitude, $dummy->longitude);
        self::assertNotEquals($clone->north, $dummy->north);
        self::assertNotEquals($clone->south, $dummy->south);
        self::assertNotEquals($clone->east, $dummy->east);
        self::assertNotEquals($clone->west, $dummy->west);
        self::assertNotEquals($clone->streetNumber, $dummy->streetNumber);
        self::assertNotEquals($clone->streetName, $dummy->streetName);
        self::assertNotEquals($clone->locality, $dummy->locality);
        self::assertNotEquals($clone->postalCode, $dummy->postalCode);
        self::assertNotEquals($clone->subLocality, $dummy->subLocality);
        self::assertNotEquals($clone->country, $dummy->country);
    }

    public function testPersistForGetter(): void
    {
        self::bootKernel(['config' => static function (TestKernel $kernel) {
            $kernel->addTestConfig(__DIR__.'/config/framework.yml');
            $kernel->addTestConfig(__DIR__.'/config/framework_sf'.$kernel::MAJOR_VERSION.'.yml');
            $kernel->addTestConfig(__DIR__.'/config/listener.yml');
        }]);

        $container = self::getContainer();
        $container->set('http_client', self::createHttpClientForBerlinQuery());

        /** @var EntityManagerInterface $em */
        $em = $container->get('doctrine.orm.entity_manager');

        $tool = new SchemaTool($em);
        $tool->createSchema($em->getMetadataFactory()->getAllMetadata());

        $dummy = new DummyWithGetter();
        $dummy->setAddress(self::QUERY_BERLIN);

        $em->persist($dummy);
        $em->flush();

        self::assertNotNull($dummy->getLatitude());
        self::assertNotNull($dummy->getLongitude());

        $clone = clone $dummy;
        $dummy->setAddress(self::QUERY_PARIS);

        $em->persist($dummy);
        $em->flush();

        self::assertNotEquals($clone->getLatitude(), $dummy->getLatitude());
        self::assertNotEquals($clone->getLongitude(), $dummy->getLongitude());
    }

    public function testPersistForStringableGetter(): void
    {
        self::bootKernel(['config' => static function (TestKernel $kernel) {
            $kernel->addTestConfig(__DIR__.'/config/framework.yml');
            $kernel->addTestConfig(__DIR__.'/config/framework_sf'.$kernel::MAJOR_VERSION.'.yml');
            $kernel->addTestConfig(__DIR__.'/config/listener.yml');
        }]);

        $container = self::getContainer();
        $container->set('http_client', self::createHttpClientForBerlinQuery());

        /** @var EntityManagerInterface $em */
        $em = $container->get('doctrine.orm.entity_manager');

        $tool = new SchemaTool($em);
        $tool->createSchema($em->getMetadataFactory()->getAllMetadata());

        $dummy = new DummyWithStringableGetter(new StringableAddress(self::QUERY_BERLIN));

        $em->persist($dummy);
        $em->flush();

        self::assertNotNull($dummy->latitude);
        self::assertNotNull($dummy->longitude);

        $clone = clone $dummy;
        $dummy->setAddress(new StringableAddress(self::QUERY_PARIS));

        $em->persist($dummy);
        $em->flush();

        self::assertNotEquals($clone->latitude, $dummy->latitude);
        self::assertNotEquals($clone->longitude, $dummy->longitude);
    }

    public function testPersistForInvalidGetter(): void
    {
        self::bootKernel(['config' => static function (TestKernel $kernel) {
            $kernel->addTestConfig(__DIR__.'/config/framework.yml');
            $kernel->addTestConfig(__DIR__.'/config/framework_sf'.$kernel::MAJOR_VERSION.'.yml');
            $kernel->addTestConfig(__DIR__.'/config/listener.yml');
        }]);

        $container = self::getContainer();
        $container->set('http_client', new MockHttpClient(static function () {
            self::fail('I shall not be called');
        }));

        /** @var EntityManagerInterface $em */
        $em = $container->get('doctrine.orm.entity_manager');

        $tool = new SchemaTool($em);
        $tool->createSchema($em->getMetadataFactory()->getAllMetadata());

        $dummy = new DummyWithInvalidGetter();
        $dummy->setAddress(self::QUERY_BERLIN);

        $em->persist($dummy);

        $this->expectException(\Exception::class);

        $em->flush();
    }

    public function testPersistForEmptyProperty(): void
    {
        self::bootKernel(['config' => static function (TestKernel $kernel) {
            $kernel->addTestConfig(__DIR__.'/config/framework.yml');
            $kernel->addTestConfig(__DIR__.'/config/framework_sf'.$kernel::MAJOR_VERSION.'.yml');
            $kernel->addTestConfig(__DIR__.'/config/listener.yml');
        }]);

        $container = self::getContainer();
        $container->set('http_client', new MockHttpClient(static function () {
            self::fail('I shall not be called');
        }));

        /** @var EntityManagerInterface $em */
        $em = $container->get('doctrine.orm.entity_manager');

        $tool = new SchemaTool($em);
        $tool->createSchema($em->getMetadataFactory()->getAllMetadata());

        $dummy = new DummyWithEmptyProperty();
        $dummy->address = '';

        $em->persist($dummy);
        $em->flush();

        self::assertNull($dummy->latitude);
        self::assertNull($dummy->longitude);
    }

    public function testDoesNotGeocodeIfAddressNotChanged(): void
    {
        self::bootKernel(['config' => static function (TestKernel $kernel) {
            $kernel->addTestConfig(__DIR__.'/config/framework.yml');
            $kernel->addTestConfig(__DIR__.'/config/framework_sf'.$kernel::MAJOR_VERSION.'.yml');
            $kernel->addTestConfig(__DIR__.'/config/listener.yml');
        }]);

        $httpRequests = 0;

        $container = self::getContainer();
        $container->set('http_client', self::createHttpClientForFrankfurtQuery($httpRequests));

        /** @var EntityManagerInterface $em */
        $em = $container->get('doctrine.orm.entity_manager');

        $tool = new SchemaTool($em);
        $tool->createSchema($em->getMetadataFactory()->getAllMetadata());

        $dummy = new DummyWithProperty();
        $dummy->address = 'Frankfurt, Germany';

        $em->persist($dummy);
        $em->flush();

        $dummy->latitude = 0;
        $dummy->longitude = 0;

        $em->flush();

        self::assertSame('Frankfurt, Germany', $dummy->address);
        self::assertSame(0.0, $dummy->latitude);
        self::assertSame(0.0, $dummy->longitude);
        self::assertSame(1, $httpRequests);
    }

    private static function createHttpClientForBerlinQuery(): MockHttpClient
    {
        return new MockHttpClient(static function (string $method, string $url): MockResponse {
            if ('https://nominatim.openstreetmap.org/search?format=jsonv2&q=Brandenburger%20Tor%20Berlin%2C%20Germany&addressdetails=1&extratags=1&limit=5' === $url) {
                self::assertSame('GET', $method);

                return new MockResponse('[{"place_id":134111822,"licence":"Data © OpenStreetMap contributors, ODbL 1.0. http://osm.org/copyright","osm_type":"way","osm_id":518071791,"lat":"52.5162699","lon":"13.3777034","category":"historic","type":"monument","place_rank":30,"importance":0.5504719991549523,"addresstype":"historic","name":"Porte de Brandebourg","display_name":"Porte de Brandebourg, 1, Pariser Platz, Friedrich-Wilhelm-Stadt, Mitte, Berlin, 10117, Allemagne","address":{"historic":"Porte de Brandebourg","house_number":"1","road":"Pariser Platz","quarter":"Friedrich-Wilhelm-Stadt","suburb":"Mitte","borough":"Mitte","city":"Berlin","ISO3166-2-lvl4":"DE-BE","postcode":"10117","country":"Allemagne","country_code":"de"},"extratags":{"image": "http://upload.wikimedia.org/wikipedia/commons/7/71/Brandenburger_Tor_2005_006.JPG", "height": "26", "image:0": "https://photos.app.goo.gl/58KgcLTZ3qNYYhjc8", "ref:lda": "09065019", "tourism": "attraction", "building": "gate", "heritage": "4", "landmark": "20", "wikidata": "Q82425", "architect": "Carl Gotthard Langhans", "direction": "W", "wikipedia": "de:Brandenburger Tor", "start_date": "1788..1791", "wheelchair": "yes", "lda:criteria": "Baudenkmal", "opening_hours": "24/7", "heritage:website": "https://denkmaldatenbank.berlin.de/daobj.php?obj_dok_nr=09065019", "heritage:operator": "lda", "wikimedia_commons": "Category:Brandenburg Gate", "architect:wikidata": "Q313181", "toilets:wheelchair": "no", "architect:wikipedia": "de:Carl Gotthard Langhans", "heritage:description": "Baudenkmal", "construction:start_date": "1788"},"boundingbox":["52.5161170","52.5164328","13.3775798","13.3778251"]},{"place_id":133613781,"licence":"Data © OpenStreetMap contributors, ODbL 1.0. http://osm.org/copyright","osm_type":"node","osm_id":3862767512,"lat":"52.5166047","lon":"13.3809897","category":"railway","type":"station","place_rank":30,"importance":0.42779598793275175,"addresstype":"railway","name":"Brandenburger Tor","display_name":"Brandenburger Tor, Unter den Linden, Friedrich-Wilhelm-Stadt, Mitte, Berlin, 10117, Allemagne","address":{"railway":"Brandenburger Tor","road":"Unter den Linden","quarter":"Friedrich-Wilhelm-Stadt","suburb":"Mitte","borough":"Mitte","city":"Berlin","ISO3166-2-lvl4":"DE-BE","postcode":"10117","country":"Allemagne","country_code":"de"},"extratags":{"level": "-3", "subway": "yes", "station": "subway", "wikidata": "Q477185", "wikipedia": "de:Bahnhof Berlin Brandenburger Tor", "start_date": "2009-08-09", "wheelchair": "yes", "public_transport": "station"},"boundingbox":["52.5116047","52.5216047","13.3759897","13.3859897"]},{"place_id":134207485,"licence":"Data © OpenStreetMap contributors, ODbL 1.0. http://osm.org/copyright","osm_type":"node","osm_id":30353086,"lat":"52.5164415","lon":"13.3818418","category":"railway","type":"halt","place_rank":30,"importance":0.42779598793275175,"addresstype":"railway","name":"Brandenburger Tor","display_name":"Brandenburger Tor, Unter den Linden, Friedrich-Wilhelm-Stadt, Mitte, Berlin, 10117, Allemagne","address":{"railway":"Brandenburger Tor","road":"Unter den Linden","quarter":"Friedrich-Wilhelm-Stadt","suburb":"Mitte","borough":"Mitte","city":"Berlin","ISO3166-2-lvl4":"DE-BE","postcode":"10117","country":"Allemagne","country_code":"de"},"extratags":{"network": "Verkehrsverbund Berlin-Brandenburg", "station": "light_rail", "uic_ref": "8089044", "operator": "DB Netz AG", "wikidata": "Q477185", "wikipedia": "de:Bahnhof Berlin Brandenburger Tor", "light_rail": "yes", "wheelchair": "no", "railway:ref": "BTOR", "contact:phone": "+49 30 297 43333", "network:short": "VBB", "contact:website": "http://www.s-bahn-berlin.de/fahrplanundnetz/bahnhof/brandenburger-tor/153", "public_transport": "station", "railway:station_category": "4"},"boundingbox":["52.5163915","52.5164915","13.3817918","13.3818918"]}]', ['response_headers' => ['content-type' => 'application/json']]);
            }

            if ('https://nominatim.openstreetmap.org/search?format=jsonv2&q=Tour%20Eiffel%20Paris%2C%20France&addressdetails=1&extratags=1&limit=5' === $url) {
                self::assertSame('GET', $method);

                return new MockResponse('[{"place_id":89233259,"licence":"Data © OpenStreetMap contributors, ODbL 1.0. http://osm.org/copyright","osm_type":"way","osm_id":5013364,"lat":"48.8582599","lon":"2.2945006","category":"man_made","type":"tower","place_rank":30,"importance":0.6205937724353116,"addresstype":"man_made","name":"Tour Eiffel","display_name":"Tour Eiffel, 5, Avenue Anatole France, Quartier du Gros-Caillou, Paris 7e Arrondissement, Paris, Île-de-France, France métropolitaine, 75007, France","address":{"man_made":"Tour Eiffel","house_number":"5","road":"Avenue Anatole France","city_block":"Quartier du Gros-Caillou","suburb":"Paris 7e Arrondissement","city_district":"Paris","city":"Paris","ISO3166-2-lvl6":"FR-75C","state":"Île-de-France","ISO3166-2-lvl4":"FR-IDF","region":"France métropolitaine","postcode":"75007","country":"France","country_code":"fr"},"extratags":{"fee": "10-25€", "3dmr": "4", "image": "https://fr.wikipedia.org/wiki/Fichier:Tour_Eiffel_Wikimedia_Commons.jpg", "layer": "2", "height": "330", "image:0": "https://photos.app.goo.gl/y43gmbzPz7jToV9D7", "ref:mhs": "PA00088801", "tourism": "attraction", "website": "https://www.toureiffel.paris/", "building": "tower", "engineer": "Gustave Eiffel;Maurice Koechlin;Émile Nouguier", "heritage": "3", "historic": "monument", "landmark": "1", "operator": "Société d’Exploitation de la Tour Eiffel", "wikidata": "Q243", "architect": "Stephen Sauvestre", "panoramax": "b57848f0-a526-4bab-8cb3-ede3df7b41c3;7e69687f-3956-41f2-bd85-03af73df2b2b;7ca4fd32-0e0a-498f-826c-d106ee360948&xyz=3.20/53.64/30", "wikipedia": "fr:Tour Eiffel", "importance": "international", "min_height": "0", "start_date": "1889", "tower:type": "communication;observation", "wheelchair": "no", "ref:FR:ANFR": "548894", "opening_hours": "09:30-23:45; Jun 21-Sep 2: 09:00-00:45; Jul 14,Jul 15 off", "building:shape": "pyramidal", "ref:FR:FANTOIR": "75107P320D", "building:colour": "#706550", "historic:period": "modern", "tourism:visitors": "7000000", "building:material": "iron", "heritage:operator": "mhs", "wikimedia_commons": "Category:Eiffel Tower", "tower:construction": "lattice", "communication:radio": "fm", "mhs:inscription_date": "1964-06-24", "communication:television": "dvb-t"},"boundingbox":["48.8574753","48.8590453","2.2933119","2.2956897"]},{"place_id":89203605,"licence":"Data © OpenStreetMap contributors, ODbL 1.0. http://osm.org/copyright","osm_type":"node","osm_id":3135278479,"lat":"48.8581328","lon":"2.2944968","category":"amenity","type":"restaurant","place_rank":30,"importance":0.2917175836992075,"addresstype":"amenity","name":"Le Jules Verne","display_name":"Le Jules Verne, Avenue Gustave Eiffel, Quartier du Gros-Caillou, Paris 7e Arrondissement, Paris, Île-de-France, France métropolitaine, 75007, France","address":{"amenity":"Le Jules Verne","road":"Avenue Gustave Eiffel","city_block":"Quartier du Gros-Caillou","suburb":"Paris 7e Arrondissement","city_district":"Paris","city":"Paris","ISO3166-2-lvl6":"FR-75C","state":"Île-de-France","ISO3166-2-lvl4":"FR-IDF","region":"France métropolitaine","postcode":"75007","country":"France","country_code":"fr"},"extratags":{"level": "2", "stars": "1", "cuisine": "french", "website": "https://www.lejulesverne-paris.com", "wikidata": "Q3223818", "check_date": "2025-10-23", "wheelchair": "yes", "indoor_seating": "no", "outdoor_seating": "yes"},"boundingbox":["48.8580828","48.8581828","2.2944468","2.2945468"]}]', ['response_headers' => ['content-type' => 'application/json']]);
            }

            self::fail(sprintf('Unexpected http call "%s %s".', $method, $url));
        });
    }

    private static function createHttpClientForFrankfurtQuery(int &$requestCount): MockHttpClient
    {
        return new MockHttpClient(static function (string $method, string $url) use (&$requestCount): MockResponse {
            if ('https://nominatim.openstreetmap.org/search?format=jsonv2&q=Frankfurt%2C%20Germany&addressdetails=1&extratags=1&limit=5' === $url) {
                self::assertSame('GET', $method);

                ++$requestCount;

                return new MockResponse('[{"place_id":152571305,"licence":"Data © OpenStreetMap contributors, ODbL 1.0. http://osm.org/copyright","osm_type":"relation","osm_id":62400,"lat":"50.1106444","lon":"8.6820917","category":"boundary","type":"administrative","place_rank":12,"importance":0.6941325622496303,"addresstype":"city","name":"Frankfurt am Main","display_name":"Frankfurt am Main, Hessen, Deutschland","address":{"city":"Frankfurt am Main","state":"Hessen","ISO3166-2-lvl4":"DE-HE","country":"Deutschland","country_code":"de"},"extratags":{"ele": "112", "flag": "File:Flag of Frankfurt am Main.svg", "logo": "File:Frankfurt am Main logo.svg", "de:place": "city", "nickname": "Europastadt", "wikidata": "Q1794", "wikipedia": "de:Frankfurt am Main", "population": "701350", "ref:LOCODE": "DEFRA", "ref:nuts:3": "DE712", "border_type": "county", "name:prefix": "Stadt", "nickname:de": "Europastadt", "nickname:la": "Urbem Europaeam", "nickname:nl": "Bankfurt", "coat_of_arms": "File:Wappen Frankfurt am Main.svg", "linked_place": "city", "wikimedia_commons": "Category:Frankfurt am Main", "license_plate_code": "F", "de:regionalschluessel": "064120000000", "TMC:cid_58:tabcd_1:Class": "Area", "TMC:cid_58:tabcd_1:LCLversion": "9.00", "TMC:cid_58:tabcd_1:LocationCode": "414", "de:amtlicher_gemeindeschluessel": "06412000"},"boundingbox":["50.0153529","50.2271424","8.4727605","8.8004049"]},{"place_id":160849350,"licence":"Data © OpenStreetMap contributors, ODbL 1.0. http://osm.org/copyright","osm_type":"relation","osm_id":62523,"lat":"52.3412273","lon":"14.549452","category":"boundary","type":"administrative","place_rank":12,"importance":0.5626903004005709,"addresstype":"city","name":"Frankfurt (Oder)","display_name":"Frankfurt (Oder), Brandenburg, Deutschland","address":{"city":"Frankfurt (Oder)","state":"Brandenburg","ISO3166-2-lvl4":"DE-BB","country":"Deutschland","country_code":"de"},"extratags":{"ele": "28", "place": "city", "website": "https://www.frankfurt-oder.de/", "de:place": "city", "wikidata": "Q4024", "wikipedia": "de:Frankfurt (Oder)", "population": "61969", "ref:LOCODE": "DEFFO", "ref:nuts:3": "DE403", "name:prefix": "Kreisfreie Stadt", "linked_place": "town", "license_plate_code": "FF", "telephone_area_code": "0335", "de:regionalschluessel": "120530000000", "TMC:cid_58:tabcd_1:Class": "Area", "TMC:cid_58:tabcd_1:LCLversion": "8.00", "TMC:cid_58:tabcd_1:LocationCode": "415", "de:amtlicher_gemeindeschluessel": "12053000"},"boundingbox":["52.2528709","52.3980721","14.3948254","14.6013644"]}]', ['response_headers' => ['content-type' => 'application/json']]);
            }

            self::fail(sprintf('Unexpected http call "%s %s".', $method, $url));
        });
    }
}
