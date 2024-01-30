<?php

declare(strict_types=1);

/*
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Bazinga\GeocoderBundle\Tests\Plugin;

use Bazinga\GeocoderBundle\Plugin\FakeIpPlugin;
use Geocoder\Query\GeocodeQuery;
use Geocoder\Query\Query;
use PHPUnit\Framework\TestCase;

/**
 * @author Quentin Dequippe <quentin@dequippe.tech
 */
final class FakeIpPluginTest extends TestCase
{
    public function testSimpleHandleQuery(): void
    {
        $fakeIpPlugin = new FakeIpPlugin('127.0.0.1', '123.123.123.123');
        $query = GeocodeQuery::create('127.0.0.1');

        /** @var Query $query */
        $query = $fakeIpPlugin->handleQuery($query, static fn (Query $query): Query => $query, static function (): void {});

        self::assertSame($query->getText(), '123.123.123.123');
    }

    /**
     * @testWith [null]
     *           [""]
     */
    public function testEmptyLocalIpQuery(?string $localIp): void
    {
        $fakeIpPlugin = new FakeIpPlugin($localIp, '123.123.123.123');
        $query = GeocodeQuery::create('124.124.124.124');

        /** @var Query $query */
        $query = $fakeIpPlugin->handleQuery($query, static fn (Query $query): Query => $query, static function (): void {});

        self::assertSame($query->getText(), '123.123.123.123');
    }

    public function testHandleQueryUsingFaker(): void
    {
        $fakeIpPlugin = new FakeIpPlugin('127.0.0.1', '192.168.1.1', true);
        $query = GeocodeQuery::create('127.0.0.1');

        /** @var Query $query */
        $query = $fakeIpPlugin->handleQuery($query, static fn (Query $query): Query => $query, static function (): void {});

        self::assertNotSame($query->getText(), '192.168.1.1');
    }
}
