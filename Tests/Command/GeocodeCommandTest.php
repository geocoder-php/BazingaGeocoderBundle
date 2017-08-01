<?php

/*
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Bazinga\GeocoderBundle\Tests\Command;

use Bazinga\GeocoderBundle\Command\GeocodeCommand;
use Geocoder\Model\Address;
use Geocoder\Model\AddressCollection;
use Geocoder\Model\Coordinates;
use Geocoder\Model\Country;
use Geocoder\ProviderAggregator;
use Geocoder\Query\GeocodeQuery;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class GeocodeCommandTest extends TestCase
{
    private static $address = '10 rue Gambetta, Paris, France';

    public function testExecute()
    {
        $coordinates = new Coordinates(1, 2);
        $country = new Country('France', 'FR');
        $address = Address::createFromArray([
            'coordinates' => $coordinates,
            'streetNumber' => '10',
            'streetName' => 'rue Gambetta',
            'zipCode' => '75020',
            'locality' => 'Paris',
            'countryName' => $country->getName(),
            'countryCode' => $country->getCode(),
        ]);

        $geocoder = $this->getMockBuilder(ProviderAggregator::class)->getMock();
        $query = GeocodeQuery::create(self::$address);
        $geocoder->expects($this->once())
            ->method('geocodeQuery')
            ->with($query)
            ->will($this->returnValue(new AddressCollection([$address])));

        $container = $this->getMockBuilder('Symfony\\Component\\DependencyInjection\\Container')->getMock();
        $container->expects($this->once())
            ->method('get')
            ->with(ProviderAggregator::class)
            ->will($this->returnValue($geocoder));

        $kernel = $this->getMockBuilder('Symfony\\Component\\HttpKernel\\Kernel')
            ->disableOriginalConstructor()
            ->getMock();

        $kernel->expects($this->any())
            ->method('getContainer')
            ->will($this->returnValue($container));

        $kernel->expects($this->any())
            ->method('getBundles')
            ->will($this->returnValue([]));

        $app = new Application($kernel);
        $app->add(new GeocodeCommand());

        $command = $app->find('geocoder:geocode');

        $tester = new CommandTester($command);
        $tester->execute([
            'command' => 'geocoder:geocode',
            'address' => self::$address,
        ]);
    }
}
