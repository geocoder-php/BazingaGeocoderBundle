<?php

if (!$loader = @require_once __DIR__.'/../vendor/autoload.php') {
    die('You must set up the project dependencies, run the following commands:
wget http://getcomposer.org/composer.phar
php composer.phar install --dev --prefer-source
');
}

require_once __DIR__.'/../vendor/maxromanovsky/php-maxmind-geoip/geoip.inc';
require_once __DIR__.'/../vendor/maxromanovsky/php-maxmind-geoip/geoipcity.inc';

$loader->add('Doctrine\Tests', __DIR__.'/../vendor/doctrine/orm/tests');
