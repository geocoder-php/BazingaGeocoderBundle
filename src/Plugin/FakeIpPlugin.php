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

use Faker\Generator;
use Faker\Provider\Internet;
use Geocoder\Plugin\Plugin;
use Geocoder\Query\GeocodeQuery;
use Geocoder\Query\Query;
use Http\Promise\Promise;

/**
 * Replace local IP with something better.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class FakeIpPlugin implements Plugin
{
    private ?string $needle;
    private ?string $replacement;
    private ?Generator $faker = null;

    public function __construct(?string $needle, ?string $replacement = null, bool $useFaker = false)
    {
        $this->needle = $needle;
        $this->replacement = $replacement;

        if ($useFaker) {
            $this->faker = new Generator();
            $this->faker->addProvider(new Internet($this->faker));
        }
    }

    /**
     * @return Promise
     */
    public function handleQuery(Query $query, callable $next, callable $first)
    {
        if (!$query instanceof GeocodeQuery) {
            return $next($query);
        }

        $replacement = $this->replacement;

        if (null !== $this->faker) {
            $replacement = $this->faker->ipv4();
        }

        if (null !== $this->needle && '' !== $this->needle) {
            $text = str_replace($this->needle, $replacement, $query->getText(), $count);

            if ($count > 0) {
                $query = $query->withText($text);
            }
        } else {
            $query = $query->withText($replacement);
        }

        return $next($query);
    }
}
