<?php

declare(strict_types=1);

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
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\Kernel;

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

        $geocoder = $this->createMock(ProviderAggregator::class);
        $query = GeocodeQuery::create(self::$address);
        $geocoder->expects($this->once())
            ->method('geocodeQuery')
            ->with($query)
            ->willReturn(new AddressCollection([$address]));

        $container = $this->createMock(Container::class);

        $kernel = $this->createMock(Kernel::class);

        $kernel->expects($this->any())
            ->method('getContainer')
            ->will($this->returnValue($container));

        $kernel->expects($this->any())
            ->method('getBundles')
            ->willReturn([]);

        $app = new Application($kernel);
        $app->add(new GeocodeCommand($geocoder));

        $command = $app->find('geocoder:geocode');

        $tester = new CommandTester($command);
        $tester->execute([
            'command' => 'geocoder:geocode',
            'address' => self::$address,
        ]);
    }
}
