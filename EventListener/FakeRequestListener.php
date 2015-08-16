<?php

/**
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */
namespace Bazinga\Bundle\GeocoderBundle\EventListener;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * @author William Durand <william.durand1@gmail.com>
 */
class FakeRequestListener
{
    /**
     * @var string
     */
    protected $fakeIp = null;

    public function __construct($fakeIp)
    {
        $this->fakeIp = $fakeIp;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        if (null !== $this->fakeIp && !empty($this->fakeIp)) {
            $event->getRequest()->server->set('REMOTE_ADDR', $this->fakeIp);
        }
    }

    public function getFakeIp()
    {
        return $this->fakeIp;
    }
}
