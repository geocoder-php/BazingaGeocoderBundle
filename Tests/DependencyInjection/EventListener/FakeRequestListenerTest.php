<?php

namespace Bazinga\Bundle\GeocoderBundle\Tests\EventListener;

use Bazinga\Bundle\GeocoderBundle\EventListener\FakeRequestListener;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class FakeRequestListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testOnKernelRequest()
    {
        $listener = new FakeRequestListener('33.33.33.1');

        $kernel = $this->getMock('Symfony\\Component\\HttpKernel\\HttpKernelInterface');
        $request = new Request();
        $event = new GetResponseEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST);

        $listener->onKernelRequest($event);

        $this->assertEquals('33.33.33.1', $request->server->get('REMOTE_ADDR'));
    }
}
