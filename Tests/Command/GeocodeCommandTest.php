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
use Geocoder\Result\Geocoded;

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
        $geocoder = $this->getMock('Geocoder\\Geocoder');

        $geocoder->expects($this->once())
            ->method('geocode')
            ->with(self::$address)
            ->will($this->returnValue(new Geocoded()));

        $container = $this->getMock('Symfony\\Component\\DependencyInjection\\Container');
        $container->expects($this->once())
            ->method('get')
            ->with('bazinga_geocoder.geocoder')
            ->will($this->returnValue($geocoder));

        $kernel = $this->getMockBuilder('Symfony\\Component\\HttpKernel\\Kernel')
            ->disableOriginalConstructor()
            ->getMock();

        $kernel->expects($this->once())
            ->method('getContainer')
            ->will($this->returnValue($container));

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
