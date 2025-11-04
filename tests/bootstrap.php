<?php

declare(strict_types=1);

#use Symfony\Component\ErrorHandler\ErrorHandler;

#ErrorHandler::register(null, false);

/*
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

if (!$loader = @require_once __DIR__.'/../vendor/autoload.php') {
    exit('You must set up the project dependencies, run the following commands:
wget http://getcomposer.org/composer.phar
php composer.phar install --dev --prefer-source
');
}

require_once __DIR__.'/../vendor/geoip/geoip/src/geoip.inc';
require_once __DIR__.'/../vendor/geoip/geoip/src/geoipcity.inc';
