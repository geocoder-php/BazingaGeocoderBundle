<?php

/**
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */
namespace Bazinga\Bundle\GeocoderBundle\Tests\Command;

use Bazinga\Bundle\GeocoderBundle\Command\GeocodeCommand;
use Geocoder\Model\Address;
use Geocoder\Model\AddressCollection;
use Geocoder\Model\Bounds;
use Geocoder\Model\Coordinates;
use Geocoder\Model\Country;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class GeocodeCommandTest extends \PHPUnit_Framework_TestCase
{
    private static $address = '10 rue Gambetta, Paris, France';

    public function testExecute()
    {
        $coordinates = new Coordinates(1, 2);
        $bounds = new Bounds(1, 2, 3, 4);
        $country = new Country('France', 'FR');
        $address = new Address($coordinates, $bounds, '10', 'rue Gambetta', '75020', 'Paris', null, null, $country);

        $geocoder = $this->getMockBuilder('Geocoder\\ProviderAggregator')->getMock();
        $geocoder->expects($this->once())
            ->method('geocode')
            ->with(self::$address)
            ->will($this->returnValue(new AddressCollection(array($address))));

        $container = $this->getMockBuilder('Symfony\\Component\\DependencyInjection\\Container')->getMock();
        $container->expects($this->once())
            ->method('get')
            ->with('bazinga_geocoder.geocoder')
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
        $tester->execute(array(
            'command' => 'geocoder:geocode',
            'address' => self::$address,
        ));
    }
}
