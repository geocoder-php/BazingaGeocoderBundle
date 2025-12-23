<?php

declare(strict_types=1);

$ignoreErrors = [];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$name of method Geocoder\\\\ProviderAggregator\\:\\:using\\(\\) expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__.'/src/Command/GeocodeCommand.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$string of function strtolower expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__.'/src/Command/GeocodeCommand.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$text of static method Geocoder\\\\Query\\\\GeocodeQuery\\:\\:create\\(\\) expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__.'/src/Command/GeocodeCommand.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#3 \\.\\.\\.\\$values of function sprintf expects bool\\|float\\|int\\|string\\|null, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__.'/src/Command/GeocodeCommand.php',
];
$ignoreErrors[] = [
    // identifier: foreach.nonIterable
    'message' => '#^Argument of an invalid type mixed supplied for foreach, only iterables are supported\\.$#',
    'count' => 3,
    'path' => __DIR__.'/src/DependencyInjection/BazingaGeocoderExtension.php',
];
$ignoreErrors[] = [
    // identifier: offsetAccess.nonOffsetAccessible
    'message' => '#^Cannot access offset \'aliases\' on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__.'/src/DependencyInjection/BazingaGeocoderExtension.php',
];
$ignoreErrors[] = [
    // identifier: offsetAccess.nonOffsetAccessible
    'message' => '#^Cannot access offset \'enabled\' on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__.'/src/DependencyInjection/BazingaGeocoderExtension.php',
];
$ignoreErrors[] = [
    // identifier: offsetAccess.nonOffsetAccessible
    'message' => '#^Cannot access offset \'factory\' on mixed\\.$#',
    'count' => 5,
    'path' => __DIR__.'/src/DependencyInjection/BazingaGeocoderExtension.php',
];
$ignoreErrors[] = [
    // identifier: offsetAccess.nonOffsetAccessible
    'message' => '#^Cannot access offset \'id\' on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__.'/src/DependencyInjection/BazingaGeocoderExtension.php',
];
$ignoreErrors[] = [
    // identifier: offsetAccess.nonOffsetAccessible
    'message' => '#^Cannot access offset \'options\' on mixed\\.$#',
    'count' => 4,
    'path' => __DIR__.'/src/DependencyInjection/BazingaGeocoderExtension.php',
];
$ignoreErrors[] = [
    // identifier: offsetAccess.nonOffsetAccessible
    'message' => '#^Cannot access offset \'reference\' on mixed\\.$#',
    'count' => 2,
    'path' => __DIR__.'/src/DependencyInjection/BazingaGeocoderExtension.php',
];
$ignoreErrors[] = [
    // identifier: staticMethod.nonObject
    'message' => '#^Cannot call static method validate\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__.'/src/DependencyInjection/BazingaGeocoderExtension.php',
];
$ignoreErrors[] = [
    // identifier: cast.string
    'message' => '#^Cannot cast mixed to string\\.$#',
    'count' => 1,
    'path' => __DIR__.'/src/DependencyInjection/BazingaGeocoderExtension.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$alias of method Symfony\\\\Component\\\\DependencyInjection\\\\ContainerBuilder\\:\\:setAlias\\(\\) expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__.'/src/DependencyInjection/BazingaGeocoderExtension.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of function array_map expects \\(callable\\(mixed\\)\\: mixed\\)\\|null, Closure\\(string\\)\\: Symfony\\\\Component\\\\DependencyInjection\\\\Reference given\\.$#',
    'count' => 1,
    'path' => __DIR__.'/src/DependencyInjection/BazingaGeocoderExtension.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$factoryServiceId of static method Bazinga\\\\GeocoderBundle\\\\DependencyInjection\\\\Compiler\\\\FactoryValidatorPass\\:\\:addFactoryServiceId\\(\\) expects non\\-empty\\-string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__.'/src/DependencyInjection/BazingaGeocoderExtension.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$id of class Symfony\\\\Component\\\\DependencyInjection\\\\Reference constructor expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__.'/src/DependencyInjection/BazingaGeocoderExtension.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$id of method Symfony\\\\Component\\\\DependencyInjection\\\\ContainerBuilder\\:\\:getDefinition\\(\\) expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__.'/src/DependencyInjection/BazingaGeocoderExtension.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$object_or_class of function class_implements expects object\\|string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__.'/src/DependencyInjection/BazingaGeocoderExtension.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$options of method Bazinga\\\\GeocoderBundle\\\\DependencyInjection\\\\BazingaGeocoderExtension\\:\\:findReferences\\(\\) expects array\\<int\\|string, mixed\\>, mixed given\\.$#',
    'count' => 2,
    'path' => __DIR__.'/src/DependencyInjection/BazingaGeocoderExtension.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$string of function ltrim expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__.'/src/DependencyInjection/BazingaGeocoderExtension.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$config of method Bazinga\\\\GeocoderBundle\\\\DependencyInjection\\\\BazingaGeocoderExtension\\:\\:configureProviderPlugins\\(\\) expects array, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__.'/src/DependencyInjection/BazingaGeocoderExtension.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\.\\.\\.\\$values of function sprintf expects bool\\|float\\|int\\|string\\|null, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__.'/src/DependencyInjection/BazingaGeocoderExtension.php',
];
$ignoreErrors[] = [
    // identifier: encapsedStringPart.nonString
    'message' => '#^Part \\$providerName \\(mixed\\) of encapsed string cannot be cast to string\\.$#',
    'count' => 1,
    'path' => __DIR__.'/src/DependencyInjection/BazingaGeocoderExtension.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method getLatitude\\(\\) on Geocoder\\\\Model\\\\Coordinates\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__.'/src/Doctrine/ORM/GeocoderListener.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method getLongitude\\(\\) on Geocoder\\\\Model\\\\Coordinates\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__.'/src/Doctrine/ORM/GeocoderListener.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$text of method Geocoder\\\\Query\\\\GeocodeQuery\\:\\:withText\\(\\) expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__.'/src/Plugin/FakeIpPlugin.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$replace of function str_replace expects array\\|string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__.'/src/Plugin/FakeIpPlugin.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$accountId of class GeoIp2\\\\WebService\\\\Client constructor expects int, int\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__.'/src/ProviderFactory/GeoIP2Factory.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$filename of class GeoIp2\\\\Database\\\\Reader constructor expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__.'/src/ProviderFactory/GeoIP2Factory.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$geoIpProvider of class Geocoder\\\\Provider\\\\GeoIP2\\\\GeoIP2Adapter constructor expects GeoIp2\\\\ProviderInterface, GeoIp2\\\\ProviderInterface\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__.'/src/ProviderFactory/GeoIP2Factory.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$licenseKey of class GeoIp2\\\\WebService\\\\Client constructor expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__.'/src/ProviderFactory/GeoIP2Factory.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
