<?php

declare(strict_types=1);

/*
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Bazinga\GeocoderBundle\Plugin;

use Geocoder\Plugin\Plugin;
use Geocoder\Query\GeocodeQuery;
use Geocoder\Query\Query;

/**
 * Replace local IP with something better.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class FakeIpPlugin implements Plugin
{
    /**
     * @var string
     */
    private $needle;

    /**
     * @var string
     */
    private $replacement;

    /**
     * @param string $needle
     * @param string $replacement
     */
    public function __construct(string $needle, string $replacement)
    {
        $this->needle = $needle;
        $this->replacement = $replacement;
    }

    /**
     * {@inheritdoc}
     */
    public function handleQuery(Query $query, callable $next, callable $first)
    {
        if (!$query instanceof GeocodeQuery) {
            return $next($query);
        }

        $text = str_replace($this->needle, $this->replacement, $query->getText(), $count);
        if ($count > 0) {
            $query = $query->withText($text);
        }

        return $next($query);
    }
}
