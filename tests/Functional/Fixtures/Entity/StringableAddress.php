<?php

declare(strict_types=1);

/*
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Bazinga\GeocoderBundle\Tests\Functional\Fixtures\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Embeddable;

#[Embeddable]
class StringableAddress implements \Stringable
{
    public function __construct(
        #[Column(type: Types::STRING)]
        private readonly string $address,
    ) {
    }

    public function __toString(): string
    {
        return $this->address;
    }
}
