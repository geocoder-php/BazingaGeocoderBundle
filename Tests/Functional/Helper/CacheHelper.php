<?php

declare(strict_types=1);

/*
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Bazinga\GeocoderBundle\Tests\Functional\Helper;

use Psr\SimpleCache\CacheInterface;

/**
 * @internal
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class CacheHelper implements CacheInterface
{
    /**
     * @return mixed
     */
    public function get($key, $default = null)
    {
    }

    /**
     * @return bool
     */
    public function set($key, $value, $ttl = null)
    {
        return true;
    }

    /**
     * @return bool
     */
    public function delete($key)
    {
        return true;
    }

    /**
     * @return bool
     */
    public function clear()
    {
        return true;
    }

    /**
     * @return iterable
     */
    public function getMultiple($keys, $default = null)
    {
        return [];
    }

    /**
     * @return bool
     */
    public function setMultiple($values, $ttl = null)
    {
        return true;
    }

    /**
     * @return bool
     */
    public function deleteMultiple($keys)
    {
        return true;
    }

    /**
     * @return bool
     */
    public function has($key)
    {
        return false;
    }
}
