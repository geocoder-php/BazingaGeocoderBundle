<?php

declare(strict_types=1);

namespace Bazinga\Bundle\GeocoderBundle\Tests\Functional\Helper;

use Psr\SimpleCache\CacheInterface;

/**
 * @internal
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class CacheHelper implements CacheInterface
{
    public function get($key, $default = null)
    {
        return null;
    }

    public function set($key, $value, $ttl = null)
    {
        return true;
    }

    public function delete($key)
    {
        return true;
    }

    public function clear()
    {
        return true;
    }

    public function getMultiple($keys, $default = null)
    {
        return [];
    }

    public function setMultiple($values, $ttl = null)
    {
        return true;
    }

    public function deleteMultiple($keys)
    {
        return true;
    }

    public function has($key)
    {
        return false;
    }

}