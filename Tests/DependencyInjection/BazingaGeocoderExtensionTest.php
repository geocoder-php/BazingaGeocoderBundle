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
        $configs = Yaml::parse(file_get_contents(__DIR__.'/Fixtures/config.yml'));
        unset($configs['bazinga_geocoder']['default_provider']);

        $container = new ContainerBuilder();
        $extension = new BazingaGeocoderExtension();

        $container->setParameter('fixtures_dir', __DIR__.'/Fixtures');

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

        $geocoder = $container->get('bazinga_geocoder.geocoder');
        $providers = $geocoder->getProviders();
        foreach (array(
            'bing_maps' => 'Geocoder\\Provider\\BingMaps',
            'cache' => 'Bazinga\\Bundle\\GeocoderBundle\\Provider\\Cache',
            'ip_info_db' => 'Geocoder\\Provider\\IpInfoDb',
            'google_maps' => 'Geocoder\\Provider\\GoogleMaps',
            'google_maps_business' => 'Geocoder\\Provider\\GoogleMapsBusiness',
            'openstreetmap' => 'Geocoder\\Provider\\OpenStreetMap',
            'host_ip' => 'Geocoder\\Provider\\HostIp',
            'free_geo_ip' => 'Geocoder\\Provider\\FreeGeoIp',
            'map_quest' => 'Geocoder\\Provider\\MapQuest',
            'yandex' => 'Geocoder\\Provider\\Yandex',
            'geo_ips' => 'Geocoder\\Provider\\GeoIps',
            'geo_plugin' => 'Geocoder\\Provider\\GeoPlugin',
            'maxmind' => 'Geocoder\\Provider\\Maxmind',
            'chain' => 'Geocoder\\Provider\\Chain',
            'maxmind_binary' => 'Geocoder\\Provider\\MaxmindBinary',
            'opencage' => 'Geocoder\\Provider\\OpenCage',
        ) as $name => $class) {
            $this->assertInstanceOf($class, $providers[$name], sprintf('-> Assert that %s is instance of %s', $name, $class));
        }
    }

    public function testDefaultProvider()
    {
        $configs = Yaml::parse(file_get_contents(__DIR__.'/Fixtures/config.yml'));
        $container = new ContainerBuilder();
        $extension = new BazingaGeocoderExtension();

        $container->setParameter('fixtures_dir', __DIR__.'/Fixtures');

        $container->set('doctrine.apc.cache', new ArrayCache());

        $container->addCompilerPass(new AddProvidersPass());
        $extension->load($configs, $container);

        $container->compile();

        $this->assertEquals('bing_maps', $container->getParameter('bazinga_geocoder.default_provider'));
    }

    public function testLoadingFakeIpOldWay()
    {
        $configs = Yaml::parse(file_get_contents(__DIR__.'/Fixtures/old_fake_ip.yml'));
        $container = new ContainerBuilder();
        $extension = new BazingaGeocoderExtension();

        $container->setParameter('fixtures_dir', __DIR__.'/Fixtures');

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
