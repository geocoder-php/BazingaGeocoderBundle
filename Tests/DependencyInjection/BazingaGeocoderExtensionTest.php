<?php

namespace Bazinga\Bundle\GeocoderBundle\Tests\DependencyInjection;

use Symfony\Component\Yaml\Yaml;
use Doctrine\Common\Cache\ArrayCache;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Bazinga\Bundle\GeocoderBundle\DependencyInjection\BazingaGeocoderExtension;
use Bazinga\Bundle\GeocoderBundle\DependencyInjection\Compiler\AddDumperPass;
use Bazinga\Bundle\GeocoderBundle\DependencyInjection\Compiler\AddProvidersPass;
use Bazinga\Bundle\GeocoderBundle\DependencyInjection\Compiler\LoggablePass;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class BazingaGeocoderExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testLoad()
    {
        $configs   = Yaml::parse(file_get_contents(__DIR__.'/Fixtures/config.yml'));
        unset($configs['bazinga_geocoder']['default_provider']);

        $container = new ContainerBuilder();
        $extension = new BazingaGeocoderExtension();

        $container->setParameter('fixtures_dir', __DIR__ . '/Fixtures');

        $container->set('doctrine.apc.cache', new ArrayCache());

        $container->addCompilerPass(new AddDumperPass());
        $container->addCompilerPass(new AddProvidersPass());
        $container->addCompilerPass(new LoggablePass());

        $extension->load($configs, $container);
        $container->compile();

        $this->assertInstanceOf(
            'Bazinga\Bundle\GeocoderBundle\EventListener\FakeRequestListener',
            $container->get('bazinga_geocoder.event_listener.fake_request')
        );
        $this->assertNotNull(
            $container->get('bazinga_geocoder.event_listener.fake_request')->getFakeIp()
        );

        $dumperManager = $container->get('bazinga_geocoder.dumper_manager');

        foreach (array('geojson', 'gpx', 'kmp', 'wkb', 'wkt') as $name) {
            $this->assertTrue($dumperManager->has($name));
        }

        $this->assertFalse($container->hasParameter('bazinga_geocoder.default_provider'));

        $geocoder  = $container->get('bazinga_geocoder.geocoder');
        $providers = $geocoder->getProviders();
        foreach (array(
            'bing_maps'            => 'Geocoder\\Provider\\BingMapsProvider',
            'cache'                => 'Bazinga\\Bundle\\GeocoderBundle\\Provider\\CacheProvider',
            'ip_info_db'           => 'Geocoder\\Provider\\IpInfoDbProvider',
            'google_maps'          => 'Geocoder\\Provider\\GoogleMapsProvider',
            'google_maps_business' => 'Geocoder\\Provider\\GoogleMapsBusinessProvider',
            'openstreetmap'        => 'Geocoder\\Provider\\OpenStreetMapProvider',
            'host_ip'              => 'Geocoder\\Provider\\HostIpProvider',
            'free_geo_ip'          => 'Geocoder\\Provider\\FreeGeoIpProvider',
            'map_quest'            => 'Geocoder\\Provider\\MapQuestProvider',
            'oio_rest'             => 'Geocoder\\Provider\\OIORestProvider',
            'geocoder_ca'          => 'Geocoder\\Provider\\GeocoderCaProvider',
            'geocoder_us'          => 'Geocoder\\Provider\\GeocoderUsProvider',
            'ign_openls'           => 'Geocoder\\Provider\\IGNOpenLSProvider',
            'data_science_toolkit' => 'Geocoder\\Provider\\DataScienceToolkitProvider',
            'yandex'               => 'Geocoder\\Provider\\YandexProvider',
            'geo_ips'              => 'Geocoder\\Provider\\GeoIpsProvider',
            'geo_plugin'           => 'Geocoder\\Provider\\GeoPluginProvider',
            'maxmind'              => 'Geocoder\\Provider\\MaxmindProvider',
            'chain'                => 'Geocoder\\Provider\\ChainProvider',
            'maxmind_binary'       => 'Geocoder\\Provider\\MaxmindBinaryProvider',
            'geonames'             => 'Geocoder\\Provider\\GeonamesProvider',
        ) as $name => $class) {
            $this->assertInstanceOf($class, $providers[$name], sprintf('-> Assert that %s is instance of %s', $name, $class));
        }
    }

    public function testDefaultProvider()
    {
        $configs   = Yaml::parse(file_get_contents(__DIR__.'/Fixtures/config.yml'));
        $container = new ContainerBuilder();
        $extension = new BazingaGeocoderExtension();

        $container->setParameter('fixtures_dir', __DIR__ . '/Fixtures');

        $container->set('doctrine.apc.cache', new ArrayCache());

        $container->addCompilerPass(new AddProvidersPass());
        $extension->load($configs, $container);

        $container->compile();

        $this->assertEquals('bing_maps', $container->getParameter('bazinga_geocoder.default_provider'));
    }

    public function testLoadingFakeIpOldWay()
    {
        $configs   = Yaml::parse(file_get_contents(__DIR__.'/Fixtures/old_fake_ip.yml'));
        $container = new ContainerBuilder();
        $extension = new BazingaGeocoderExtension();

        $container->setParameter('fixtures_dir', __DIR__ . '/Fixtures');

        $container->set('doctrine.apc.cache', new ArrayCache());

        $container->addCompilerPass(new AddDumperPass());
        $container->addCompilerPass(new AddProvidersPass());

        $extension->load($configs, $container);
        $container->compile();

        $this->assertInstanceOf(
            'Bazinga\Bundle\GeocoderBundle\EventListener\FakeRequestListener',
            $container->get('bazinga_geocoder.event_listener.fake_request')
        );

        $this->assertNotNull(
            $container->get('bazinga_geocoder.event_listener.fake_request')->getFakeIp()
        );
    }
}
