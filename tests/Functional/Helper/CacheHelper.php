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

if (PHP_VERSION_ID >= 80000) {
    /**
     * @internal
     *
     * @author Tobias Nyholm <tobias.nyholm@gmail.com>
     */
    class CacheHelper implements CacheInterface
    {
        use CacheHelperV8;
    }
} else {
    /**
     * @internal
     *
     * @author Tobias Nyholm <tobias.nyholm@gmail.com>
     */
    class CacheHelper implements CacheInterface
    {
        use CacheHelperV7;
    }
}
