<?php

/*
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Bazinga\Bundle\GeocoderBundle\Tests\EventListener;

use Bazinga\Bundle\GeocoderBundle\EventListener\FakeRequestListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class FakeRequestListenerTest extends TestCase
{
    public function testOnKernelRequest()
    {
        $listener = new FakeRequestListener('33.33.33.1');

        $kernel = $this->getMockBuilder('Symfony\\Component\\HttpKernel\\HttpKernelInterface')->getMock();
        $request = new Request();
        $event = new GetResponseEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST);

        $listener->onKernelRequest($event);

        $this->assertEquals('33.33.33.1', $request->server->get('REMOTE_ADDR'));
    }
}
