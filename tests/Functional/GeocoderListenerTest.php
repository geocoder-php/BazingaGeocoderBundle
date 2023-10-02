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
use Bazinga\GeocoderBundle\Tests\PublicServicePass;
use Doctrine\Bundle\DoctrineBundle\ConnectionFactory;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\ORM\Tools\SchemaTool;
use Http\Message\RequestMatcher\RequestMatcher;
use Http\Mock\Client;
use Nyholm\BundleTest\TestKernel;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
final class GeocoderListenerTest extends KernelTestCase
{
    protected function tearDown(): void
    {
        $em = self::getContainer()->get('doctrine.orm.entity_manager');

        $tool = new SchemaTool($em);
        $tool->dropSchema($em->getMetadataFactory()->getAllMetadata());
    }

    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    protected static function createKernel(array $options = []): KernelInterface
    {
        /**
         * @var TestKernel $kernel
         */
        $kernel = parent::createKernel($options);
        $kernel->addTestBundle(DoctrineBundle::class);
        $kernel->addTestBundle(BazingaGeocoderBundle::class);
        $kernel->addTestCompilerPass(new PublicServicePass('|[Bb]azinga:*|'));
        $kernel->addTestCompilerPass(new PublicServicePass('|[gG]eocoder:*|'));
        if (defined(ConnectionFactory::class.'::DEFAULT_SCHEME_MAP')) {
            $kernel->addTestConfig(static function (ContainerBuilder $container) {
                $container->prependExtensionConfig('doctrine', [
                    'orm' => [
                        'report_fields_where_declared' => true,
                    ],
                ]);
            });
        }
        $kernel->handleOptions($options);

        return $kernel;
    }

    public function testPersistForProperty(): void
    {
        $kernel = self::bootKernel(['config' => static function (TestKernel $kernel) {
            $kernel->addTestConfig(__DIR__.'/config/framework.yml');
            $kernel->addTestConfig(__DIR__.'/config/listener.yml');
            $kernel->addTestConfig(__DIR__.'/config/listener_'.(PHP_VERSION_ID >= 80000 ? 'php8' : 'php7').'.yml');
        }]);

        $container = method_exists(__CLASS__, 'getContainer') ? self::getContainer() : $kernel->getContainer();

        $httpClient = $container->get(Client::class);
        $httpClient->on(new RequestMatcher(), function (RequestInterface $request) {
            if ('https://nominatim.openstreetmap.org/search?format=jsonv2&q=Berlin%2C%20Germany&addressdetails=1&extratags=1&limit=5' === (string) $request->getUri()) {
                $response = $this->createMock(ResponseInterface::class);
                $response->expects(self::once())
                    ->method('getStatusCode')
                    ->willReturn(200);
                $response->expects(self::once())
                    ->method('getBody')
                    ->willReturn('[{"place_id":159647018,"licence":"Data © OpenStreetMap contributors, ODbL 1.0. http://osm.org/copyright","osm_type":"relation","osm_id":62422,"lat":"52.5170365","lon":"13.3888599","category":"boundary","type":"administrative","place_rank":8,"importance":0.7875390282491362,"addresstype":"city","name":"Berlin","display_name":"Berlin, Deutschland","address":{"city":"Berlin","ISO3166-2-lvl4":"DE-BE","country":"Deutschland","country_code":"de"},"extratags":{"ele": "35", "email": "info@berlin.de", "place": "city", "capital": "yes", "website": "http://www.berlin.de", "de:place": "city", "ref:nuts": "DE3;DE30;DE300", "wikidata": "Q64", "wikipedia": "de:Berlin", "population": "3769962", "ref:LOCODE": "DEBER", "ref:nuts:1": "DE3", "ref:nuts:2": "DE30", "ref:nuts:3": "DE300", "state_code": "BE", "name:prefix": "Land und Kreisfreie Stadt", "linked_place": "city", "official_status": "Land", "contact:facebook": "http://www.facebook.com/Berlin", "name:prefix:city": "Kreisfreie Stadt", "openGeoDB:loc_id": "14356", "capital_ISO3166-1": "yes", "name:prefix:state": "Land", "source:population": "https://download.statistik-berlin-brandenburg.de/fa93e3bd19a2e885/a5ecfb2fff6a/SB_A01-05-00_2020h02_BE.pdf", "license_plate_code": "B", "official_status:de": "Land", "official_status:en": "State", "official_status:ru": "земля", "geographical_region": "Barnim;Berliner Urstromtal;Teltow;Nauener Platte", "blind:description:de": "Auf www.berlinfuerblinde.de gibt es einen kostenlosen Audioguide und weitere Informationen.", "de:regionalschluessel": "110000000000", "openGeoDB:postal_codes": "10178,10115,10117,10119,10179,10243,10245,10247,10249,10315,10317,10318,10319,10365,10367,10369,10405,10407,10409,10435,10437,10439,10551,10553,10555,10557,10559,10585,10587,10589,10623,10625,10627,10629,10707,10709,10711,10713,10715,10717,10719,10777,10", "report_problems:website": "https://ordnungsamt.berlin.de/", "TMC:cid_58:tabcd_1:Class": "Area", "openGeoDB:license_plate_code": "B", "TMC:cid_58:tabcd_1:LCLversion": "12.0", "openGeoDB:telephone_area_code": "030", "TMC:cid_58:tabcd_1:LocationCode": "266", "de:amtlicher_gemeindeschluessel": "11000000", "openGeoDB:community_identification_number": "11000000"},"boundingbox":["52.3382448","52.6755087","13.0883450","13.7611609"]}]');

                return $response;
            }

            if ('https://nominatim.openstreetmap.org/search?format=jsonv2&q=Paris%2C%20France&addressdetails=1&extratags=1&limit=5' === (string) $request->getUri()) {
                $response = $this->createMock(ResponseInterface::class);
                $response->expects(self::once())
                    ->method('getStatusCode')
                    ->willReturn(200);
                $response->expects(self::once())
                    ->method('getBody')
                    ->willReturn('[{"place_id":115350921,"licence":"Data © OpenStreetMap contributors, ODbL 1.0. http://osm.org/copyright","osm_type":"relation","osm_id":7444,"lat":"48.8588897","lon":"2.3200410217200766","category":"boundary","type":"administrative","place_rank":15,"importance":0.8317101715588673,"addresstype":"suburb","name":"Paris","display_name":"Paris, Île-de-France, France métropolitaine, France","address":{"suburb":"Paris","city_district":"Paris","city":"Paris","ISO3166-2-lvl6":"FR-75","state":"Île-de-France","ISO3166-2-lvl4":"FR-IDF","region":"France métropolitaine","country":"France","country_code":"fr"},"extratags":{"capital": "yes", "wikidata": "Q90", "ref:INSEE": "75056", "wikipedia": "fr:Paris", "population": "2187526", "ref:FR:MGP": "T1", "source:population": "INSEE 2020"},"boundingbox":["48.8155755","48.9021560","2.2241220","2.4697602"]},{"place_id":114827617,"licence":"Data © OpenStreetMap contributors, ODbL 1.0. http://osm.org/copyright","osm_type":"relation","osm_id":71525,"lat":"48.8534951","lon":"2.3483915","category":"boundary","type":"administrative","place_rank":12,"importance":0.8317101715588673,"addresstype":"city","name":"Paris","display_name":"Paris, Île-de-France, France métropolitaine, France","address":{"city":"Paris","ISO3166-2-lvl6":"FR-75","state":"Île-de-France","ISO3166-2-lvl4":"FR-IDF","region":"France métropolitaine","country":"France","country_code":"fr"},"extratags":{"rank": "0", "capital": "yes", "ref:nuts": "FR101", "wikidata": "Q90", "ref:INSEE": "75", "wikipedia": "fr:Paris", "is_capital": "country", "population": "2165423", "ref:nuts:3": "FR101", "linked_place": "city", "source:name:oc": "ieo-bdtopoc", "contact:website": "http://www.paris.fr", "population:date": "2019", "capital_ISO3166-1": "yes", "source:population": "INSEE 2022"},"boundingbox":["48.8155755","48.9021560","2.2241220","2.4697602"]},{"place_id":114994164,"licence":"Data © OpenStreetMap contributors, ODbL 1.0. http://osm.org/copyright","osm_type":"relation","osm_id":1641193,"lat":"48.8588897","lon":"2.3200410217200766","category":"boundary","type":"administrative","place_rank":14,"importance":0.4283953917728152,"addresstype":"city_district","name":"Paris","display_name":"Paris, Île-de-France, France métropolitaine, France","address":{"city_district":"Paris","city":"Paris","ISO3166-2-lvl6":"FR-75","state":"Île-de-France","ISO3166-2-lvl4":"FR-IDF","region":"France métropolitaine","country":"France","country_code":"fr"},"extratags":{"wikidata": "Q2863958", "ref:INSEE": "751", "wikipedia": "fr:Arrondissement de Paris"},"boundingbox":["48.8155755","48.9021560","2.2241220","2.4697602"]}]');

                return $response;
            }

            self::fail(sprintf('Unexpected http call "%s %s".', $request->getMethod(), (string) $request->getUri()));
        });

        $dummy = new DummyWithProperty();
        $dummy->address = 'Berlin, Germany';

        $em = $container->get('doctrine.orm.entity_manager');

        $tool = new SchemaTool($em);
        $tool->createSchema($em->getMetadataFactory()->getAllMetadata());

        $em->persist($dummy);
        $em->flush();

        self::assertNotNull($dummy->latitude);
        self::assertNotNull($dummy->longitude);

        $clone = clone $dummy;
        $dummy->address = 'Paris, France';

        $em->persist($dummy);
        $em->flush();

        self::assertNotEquals($clone->latitude, $dummy->latitude);
        self::assertNotEquals($clone->longitude, $dummy->longitude);
    }

    public function testPersistForGetter(): void
    {
        $kernel = self::bootKernel(['config' => static function (TestKernel $kernel) {
            $kernel->addTestConfig(__DIR__.'/config/framework.yml');
            $kernel->addTestConfig(__DIR__.'/config/listener.yml');
            $kernel->addTestConfig(__DIR__.'/config/listener_'.(PHP_VERSION_ID >= 80000 ? 'php8' : 'php7').'.yml');
        }]);

        $container = method_exists(__CLASS__, 'getContainer') ? self::getContainer() : $kernel->getContainer();

        $httpClient = $container->get(Client::class);
        $httpClient->on(new RequestMatcher(), function (RequestInterface $request) {
            if ('https://nominatim.openstreetmap.org/search?format=jsonv2&q=Berlin%2C%20Germany&addressdetails=1&extratags=1&limit=5' === (string) $request->getUri()) {
                $response = $this->createMock(ResponseInterface::class);
                $response->expects(self::once())
                    ->method('getStatusCode')
                    ->willReturn(200);
                $response->expects(self::once())
                    ->method('getBody')
                    ->willReturn('[{"place_id":159647018,"licence":"Data © OpenStreetMap contributors, ODbL 1.0. http://osm.org/copyright","osm_type":"relation","osm_id":62422,"lat":"52.5170365","lon":"13.3888599","category":"boundary","type":"administrative","place_rank":8,"importance":0.7875390282491362,"addresstype":"city","name":"Berlin","display_name":"Berlin, Deutschland","address":{"city":"Berlin","ISO3166-2-lvl4":"DE-BE","country":"Deutschland","country_code":"de"},"extratags":{"ele": "35", "email": "info@berlin.de", "place": "city", "capital": "yes", "website": "http://www.berlin.de", "de:place": "city", "ref:nuts": "DE3;DE30;DE300", "wikidata": "Q64", "wikipedia": "de:Berlin", "population": "3769962", "ref:LOCODE": "DEBER", "ref:nuts:1": "DE3", "ref:nuts:2": "DE30", "ref:nuts:3": "DE300", "state_code": "BE", "name:prefix": "Land und Kreisfreie Stadt", "linked_place": "city", "official_status": "Land", "contact:facebook": "http://www.facebook.com/Berlin", "name:prefix:city": "Kreisfreie Stadt", "openGeoDB:loc_id": "14356", "capital_ISO3166-1": "yes", "name:prefix:state": "Land", "source:population": "https://download.statistik-berlin-brandenburg.de/fa93e3bd19a2e885/a5ecfb2fff6a/SB_A01-05-00_2020h02_BE.pdf", "license_plate_code": "B", "official_status:de": "Land", "official_status:en": "State", "official_status:ru": "земля", "geographical_region": "Barnim;Berliner Urstromtal;Teltow;Nauener Platte", "blind:description:de": "Auf www.berlinfuerblinde.de gibt es einen kostenlosen Audioguide und weitere Informationen.", "de:regionalschluessel": "110000000000", "openGeoDB:postal_codes": "10178,10115,10117,10119,10179,10243,10245,10247,10249,10315,10317,10318,10319,10365,10367,10369,10405,10407,10409,10435,10437,10439,10551,10553,10555,10557,10559,10585,10587,10589,10623,10625,10627,10629,10707,10709,10711,10713,10715,10717,10719,10777,10", "report_problems:website": "https://ordnungsamt.berlin.de/", "TMC:cid_58:tabcd_1:Class": "Area", "openGeoDB:license_plate_code": "B", "TMC:cid_58:tabcd_1:LCLversion": "12.0", "openGeoDB:telephone_area_code": "030", "TMC:cid_58:tabcd_1:LocationCode": "266", "de:amtlicher_gemeindeschluessel": "11000000", "openGeoDB:community_identification_number": "11000000"},"boundingbox":["52.3382448","52.6755087","13.0883450","13.7611609"]}]');

                return $response;
            }

            if ('https://nominatim.openstreetmap.org/search?format=jsonv2&q=Paris%2C%20France&addressdetails=1&extratags=1&limit=5' === (string) $request->getUri()) {
                $response = $this->createMock(ResponseInterface::class);
                $response->expects(self::once())
                    ->method('getStatusCode')
                    ->willReturn(200);
                $response->expects(self::once())
                    ->method('getBody')
                    ->willReturn('[{"place_id":115350921,"licence":"Data © OpenStreetMap contributors, ODbL 1.0. http://osm.org/copyright","osm_type":"relation","osm_id":7444,"lat":"48.8588897","lon":"2.3200410217200766","category":"boundary","type":"administrative","place_rank":15,"importance":0.8317101715588673,"addresstype":"suburb","name":"Paris","display_name":"Paris, Île-de-France, France métropolitaine, France","address":{"suburb":"Paris","city_district":"Paris","city":"Paris","ISO3166-2-lvl6":"FR-75","state":"Île-de-France","ISO3166-2-lvl4":"FR-IDF","region":"France métropolitaine","country":"France","country_code":"fr"},"extratags":{"capital": "yes", "wikidata": "Q90", "ref:INSEE": "75056", "wikipedia": "fr:Paris", "population": "2187526", "ref:FR:MGP": "T1", "source:population": "INSEE 2020"},"boundingbox":["48.8155755","48.9021560","2.2241220","2.4697602"]},{"place_id":114827617,"licence":"Data © OpenStreetMap contributors, ODbL 1.0. http://osm.org/copyright","osm_type":"relation","osm_id":71525,"lat":"48.8534951","lon":"2.3483915","category":"boundary","type":"administrative","place_rank":12,"importance":0.8317101715588673,"addresstype":"city","name":"Paris","display_name":"Paris, Île-de-France, France métropolitaine, France","address":{"city":"Paris","ISO3166-2-lvl6":"FR-75","state":"Île-de-France","ISO3166-2-lvl4":"FR-IDF","region":"France métropolitaine","country":"France","country_code":"fr"},"extratags":{"rank": "0", "capital": "yes", "ref:nuts": "FR101", "wikidata": "Q90", "ref:INSEE": "75", "wikipedia": "fr:Paris", "is_capital": "country", "population": "2165423", "ref:nuts:3": "FR101", "linked_place": "city", "source:name:oc": "ieo-bdtopoc", "contact:website": "http://www.paris.fr", "population:date": "2019", "capital_ISO3166-1": "yes", "source:population": "INSEE 2022"},"boundingbox":["48.8155755","48.9021560","2.2241220","2.4697602"]},{"place_id":114994164,"licence":"Data © OpenStreetMap contributors, ODbL 1.0. http://osm.org/copyright","osm_type":"relation","osm_id":1641193,"lat":"48.8588897","lon":"2.3200410217200766","category":"boundary","type":"administrative","place_rank":14,"importance":0.4283953917728152,"addresstype":"city_district","name":"Paris","display_name":"Paris, Île-de-France, France métropolitaine, France","address":{"city_district":"Paris","city":"Paris","ISO3166-2-lvl6":"FR-75","state":"Île-de-France","ISO3166-2-lvl4":"FR-IDF","region":"France métropolitaine","country":"France","country_code":"fr"},"extratags":{"wikidata": "Q2863958", "ref:INSEE": "751", "wikipedia": "fr:Arrondissement de Paris"},"boundingbox":["48.8155755","48.9021560","2.2241220","2.4697602"]}]');

                return $response;
            }

            self::fail(sprintf('Unexpected http call "%s %s".', $request->getMethod(), (string) $request->getUri()));
        });

        $em = $container->get('doctrine.orm.entity_manager');

        $tool = new SchemaTool($em);
        $tool->createSchema($em->getMetadataFactory()->getAllMetadata());

        $dummy = new DummyWithGetter();
        $dummy->setAddress('Berlin, Germany');

        $em->persist($dummy);
        $em->flush();

        self::assertNotNull($dummy->getLatitude());
        self::assertNotNull($dummy->getLongitude());

        $clone = clone $dummy;
        $dummy->setAddress('Paris, France');

        $em->persist($dummy);
        $em->flush();

        self::assertNotEquals($clone->getLatitude(), $dummy->getLatitude());
        self::assertNotEquals($clone->getLongitude(), $dummy->getLongitude());
    }

    public function testPersistForInvalidGetter(): void
    {
        $kernel = self::bootKernel(['config' => static function (TestKernel $kernel) {
            $kernel->addTestConfig(__DIR__.'/config/framework.yml');
            $kernel->addTestConfig(__DIR__.'/config/listener.yml');
            $kernel->addTestConfig(__DIR__.'/config/listener_'.(PHP_VERSION_ID >= 80000 ? 'php8' : 'php7').'.yml');
        }]);

        $container = method_exists(__CLASS__, 'getContainer') ? self::getContainer() : $kernel->getContainer();

        $em = $container->get('doctrine.orm.entity_manager');

        $tool = new SchemaTool($em);
        $tool->createSchema($em->getMetadataFactory()->getAllMetadata());

        $dummy = new DummyWithInvalidGetter();
        $dummy->setAddress('Berlin, Germany');

        $em->persist($dummy);

        $this->expectException(\Exception::class);

        $em->flush();
    }

    public function testPersistForEmptyProperty(): void
    {
        $kernel = self::bootKernel(['config' => static function (TestKernel $kernel) {
            $kernel->addTestConfig(__DIR__.'/config/framework.yml');
            $kernel->addTestConfig(__DIR__.'/config/listener.yml');
            $kernel->addTestConfig(__DIR__.'/config/listener_'.(PHP_VERSION_ID >= 80000 ? 'php8' : 'php7').'.yml');
        }]);

        $container = method_exists(__CLASS__, 'getContainer') ? self::getContainer() : $kernel->getContainer();

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
        $kernel = self::bootKernel(['config' => static function (TestKernel $kernel) {
            $kernel->addTestConfig(__DIR__.'/config/framework.yml');
            $kernel->addTestConfig(__DIR__.'/config/listener.yml');
            $kernel->addTestConfig(__DIR__.'/config/listener_'.(PHP_VERSION_ID >= 80000 ? 'php8' : 'php7').'.yml');
        }]);

        $container = method_exists(__CLASS__, 'getContainer') ? self::getContainer() : $kernel->getContainer();

        $httpRequests = 0;
        $httpClient = $container->get(Client::class);
        $httpClient->on(new RequestMatcher(), function (RequestInterface $request) use (&$httpRequests) {
            $response = $this->createMock(ResponseInterface::class);
            $response->expects(self::any())
                ->method('getStatusCode')
                ->willReturn(200);

            if ('https://nominatim.openstreetmap.org/search?format=jsonv2&q=Frankfurt%2C%20Germany&addressdetails=1&extratags=1&limit=5' === (string) $request->getUri() && 0 === $httpRequests) {
                $response->expects(self::once())
                    ->method('getBody')
                    ->willReturn('[{"place_id":152571305,"licence":"Data © OpenStreetMap contributors, ODbL 1.0. http://osm.org/copyright","osm_type":"relation","osm_id":62400,"lat":"50.1106444","lon":"8.6820917","category":"boundary","type":"administrative","place_rank":12,"importance":0.6941325622496303,"addresstype":"city","name":"Frankfurt am Main","display_name":"Frankfurt am Main, Hessen, Deutschland","address":{"city":"Frankfurt am Main","state":"Hessen","ISO3166-2-lvl4":"DE-HE","country":"Deutschland","country_code":"de"},"extratags":{"ele": "112", "flag": "File:Flag of Frankfurt am Main.svg", "logo": "File:Frankfurt am Main logo.svg", "de:place": "city", "nickname": "Europastadt", "wikidata": "Q1794", "wikipedia": "de:Frankfurt am Main", "population": "701350", "ref:LOCODE": "DEFRA", "ref:nuts:3": "DE712", "border_type": "county", "name:prefix": "Stadt", "nickname:de": "Europastadt", "nickname:la": "Urbem Europaeam", "nickname:nl": "Bankfurt", "coat_of_arms": "File:Wappen Frankfurt am Main.svg", "linked_place": "city", "wikimedia_commons": "Category:Frankfurt am Main", "license_plate_code": "F", "de:regionalschluessel": "064120000000", "TMC:cid_58:tabcd_1:Class": "Area", "TMC:cid_58:tabcd_1:LCLversion": "9.00", "TMC:cid_58:tabcd_1:LocationCode": "414", "de:amtlicher_gemeindeschluessel": "06412000"},"boundingbox":["50.0153529","50.2271424","8.4727605","8.8004049"]},{"place_id":160849350,"licence":"Data © OpenStreetMap contributors, ODbL 1.0. http://osm.org/copyright","osm_type":"relation","osm_id":62523,"lat":"52.3412273","lon":"14.549452","category":"boundary","type":"administrative","place_rank":12,"importance":0.5626903004005709,"addresstype":"city","name":"Frankfurt (Oder)","display_name":"Frankfurt (Oder), Brandenburg, Deutschland","address":{"city":"Frankfurt (Oder)","state":"Brandenburg","ISO3166-2-lvl4":"DE-BB","country":"Deutschland","country_code":"de"},"extratags":{"ele": "28", "place": "city", "website": "https://www.frankfurt-oder.de/", "de:place": "city", "wikidata": "Q4024", "wikipedia": "de:Frankfurt (Oder)", "population": "61969", "ref:LOCODE": "DEFFO", "ref:nuts:3": "DE403", "name:prefix": "Kreisfreie Stadt", "linked_place": "town", "license_plate_code": "FF", "telephone_area_code": "0335", "de:regionalschluessel": "120530000000", "TMC:cid_58:tabcd_1:Class": "Area", "TMC:cid_58:tabcd_1:LCLversion": "8.00", "TMC:cid_58:tabcd_1:LocationCode": "415", "de:amtlicher_gemeindeschluessel": "12053000"},"boundingbox":["52.2528709","52.3980721","14.3948254","14.6013644"]}]');
            } else {
                $response->expects(self::once())
                    ->method('getBody')
                    ->willReturn('[]');
            }

            ++$httpRequests;

            return $response;
        });

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
        self::assertSame(0, $dummy->latitude);
        self::assertSame(0, $dummy->longitude);
        self::assertSame(1, $httpRequests);
    }
}
