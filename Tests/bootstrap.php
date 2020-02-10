<?php

declare(strict_types=1);

/*
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

if (!$loader = @require_once __DIR__.'/../vendor/autoload.php') {
    die('You must set up the project dependencies, run the following commands:
wget http://getcomposer.org/composer.phar
php composer.phar install --dev --prefer-source
');
}

require_once __DIR__.'/../vendor/geoip/geoip/src/geoip.inc';
require_once __DIR__.'/../vendor/geoip/geoip/src/geoipcity.inc';

if (!is_bool($loader)) {
    $loader->add('Doctrine\Tests', __DIR__.'/../vendor/doctrine/orm/tests');
} else {
    echo "Warning: Doctrine\Tests could not be added to the autoloader. \n";
    // Fake class
    class_alias('\PHPUnit\Framework\TestCase', 'Doctrine\Tests\OrmTestCase');
}
