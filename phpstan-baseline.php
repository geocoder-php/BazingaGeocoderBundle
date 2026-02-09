<?php

declare(strict_types=1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$name of method Geocoder\\\\ProviderAggregator\\:\\:using\\(\\) expects string, mixed given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__.'/src/Command/GeocodeCommand.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$string of function strtolower expects string, string\\|null given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__.'/src/Command/GeocodeCommand.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$text of static method Geocoder\\\\Query\\\\GeocodeQuery\\:\\:create\\(\\) expects string, mixed given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__.'/src/Command/GeocodeCommand.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#3 \\.\\.\\.\\$values of function sprintf expects bool\\|float\\|int\\|string\\|null, mixed given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__.'/src/Command/GeocodeCommand.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Bazinga\\\\GeocoderBundle\\\\DataCollector\\\\GeocoderDataCollector\\:\\:getProviderQueries\\(\\) should return list\\<array\\{query\\: Geocoder\\\\Query\\\\Query, queryString\\: string, duration\\: float, providerName\\: string, result\\: mixed, resultCount\\: int\\}\\> but returns array\\.$#',
    'identifier' => 'return.type',
    'count' => 1,
    'path' => __DIR__.'/src/DataCollector/GeocoderDataCollector.php',
];
$ignoreErrors[] = [
    'message' => '#^Argument of an invalid type mixed supplied for foreach, only iterables are supported\\.$#',
    'identifier' => 'foreach.nonIterable',
    'count' => 3,
    'path' => __DIR__.'/src/DependencyInjection/BazingaGeocoderExtension.php',
];
$ignoreErrors[] = [
    'message' => '#^Binary operation "\\." between \'bazinga_geocoder…\' and mixed results in an error\\.$#',
    'identifier' => 'binaryOp.invalid',
    'count' => 1,
    'path' => __DIR__.'/src/DependencyInjection/BazingaGeocoderExtension.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access offset \'aliases\' on mixed\\.$#',
    'identifier' => 'offsetAccess.nonOffsetAccessible',
    'count' => 1,
    'path' => __DIR__.'/src/DependencyInjection/BazingaGeocoderExtension.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access offset \'enabled\' on mixed\\.$#',
    'identifier' => 'offsetAccess.nonOffsetAccessible',
    'count' => 1,
    'path' => __DIR__.'/src/DependencyInjection/BazingaGeocoderExtension.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access offset \'factory\' on mixed\\.$#',
    'identifier' => 'offsetAccess.nonOffsetAccessible',
    'count' => 5,
    'path' => __DIR__.'/src/DependencyInjection/BazingaGeocoderExtension.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access offset \'id\' on mixed\\.$#',
    'identifier' => 'offsetAccess.nonOffsetAccessible',
    'count' => 1,
    'path' => __DIR__.'/src/DependencyInjection/BazingaGeocoderExtension.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access offset \'options\' on mixed\\.$#',
    'identifier' => 'offsetAccess.nonOffsetAccessible',
    'count' => 4,
    'path' => __DIR__.'/src/DependencyInjection/BazingaGeocoderExtension.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access offset \'reference\' on mixed\\.$#',
    'identifier' => 'offsetAccess.nonOffsetAccessible',
    'count' => 2,
    'path' => __DIR__.'/src/DependencyInjection/BazingaGeocoderExtension.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call static method validate\\(\\) on mixed\\.$#',
    'identifier' => 'staticMethod.nonObject',
    'count' => 1,
    'path' => __DIR__.'/src/DependencyInjection/BazingaGeocoderExtension.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot cast mixed to string\\.$#',
    'identifier' => 'cast.string',
    'count' => 1,
    'path' => __DIR__.'/src/DependencyInjection/BazingaGeocoderExtension.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$alias of method Symfony\\\\Component\\\\DependencyInjection\\\\ContainerBuilder\\:\\:setAlias\\(\\) expects string, mixed given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__.'/src/DependencyInjection/BazingaGeocoderExtension.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of function array_map expects \\(callable\\(mixed\\)\\: mixed\\)\\|null, Closure\\(string\\)\\: Symfony\\\\Component\\\\DependencyInjection\\\\Reference given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__.'/src/DependencyInjection/BazingaGeocoderExtension.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$factoryServiceId of static method Bazinga\\\\GeocoderBundle\\\\DependencyInjection\\\\Compiler\\\\FactoryValidatorPass\\:\\:addFactoryServiceId\\(\\) expects non\\-empty\\-string, mixed given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__.'/src/DependencyInjection/BazingaGeocoderExtension.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$id of class Symfony\\\\Component\\\\DependencyInjection\\\\Reference constructor expects string, mixed given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__.'/src/DependencyInjection/BazingaGeocoderExtension.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$id of method Symfony\\\\Component\\\\DependencyInjection\\\\ContainerBuilder\\:\\:getDefinition\\(\\) expects string, mixed given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__.'/src/DependencyInjection/BazingaGeocoderExtension.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$object_or_class of function class_implements expects object\\|string, mixed given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__.'/src/DependencyInjection/BazingaGeocoderExtension.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$options of method Bazinga\\\\GeocoderBundle\\\\DependencyInjection\\\\BazingaGeocoderExtension\\:\\:findReferences\\(\\) expects array\\<mixed\\>, mixed given\\.$#',
    'identifier' => 'argument.type',
    'count' => 2,
    'path' => __DIR__.'/src/DependencyInjection/BazingaGeocoderExtension.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$string of function ltrim expects string, mixed given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__.'/src/DependencyInjection/BazingaGeocoderExtension.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$array of function array_key_exists expects array, array\\|bool\\|float\\|int\\|string\\|UnitEnum\\|null given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__.'/src/DependencyInjection/BazingaGeocoderExtension.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$config of method Bazinga\\\\GeocoderBundle\\\\DependencyInjection\\\\BazingaGeocoderExtension\\:\\:configureProviderPlugins\\(\\) expects array\\<mixed\\>, mixed given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__.'/src/DependencyInjection/BazingaGeocoderExtension.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\.\\.\\.\\$values of function sprintf expects bool\\|float\\|int\\|string\\|null, mixed given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__.'/src/DependencyInjection/BazingaGeocoderExtension.php',
];
$ignoreErrors[] = [
    'message' => '#^Part \\$providerName \\(mixed\\) of encapsed string cannot be cast to string\\.$#',
    'identifier' => 'encapsedStringPart.nonString',
    'count' => 1,
    'path' => __DIR__.'/src/DependencyInjection/BazingaGeocoderExtension.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method getLatitude\\(\\) on Geocoder\\\\Model\\\\Coordinates\\|null\\.$#',
    'identifier' => 'method.nonObject',
    'count' => 1,
    'path' => __DIR__.'/src/Doctrine/ORM/GeocodeEntityListener.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method getLongitude\\(\\) on Geocoder\\\\Model\\\\Coordinates\\|null\\.$#',
    'identifier' => 'method.nonObject',
    'count' => 1,
    'path' => __DIR__.'/src/Doctrine/ORM/GeocodeEntityListener.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$text of method Geocoder\\\\Query\\\\GeocodeQuery\\:\\:withText\\(\\) expects string, string\\|null given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__.'/src/Plugin/FakeIpPlugin.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$accountId of class GeoIp2\\\\WebService\\\\Client constructor expects int, int\\|null given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__.'/src/ProviderFactory/GeoIP2Factory.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$filename of class GeoIp2\\\\Database\\\\Reader constructor expects string, string\\|null given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__.'/src/ProviderFactory/GeoIP2Factory.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$geoIpProvider of class Geocoder\\\\Provider\\\\GeoIP2\\\\GeoIP2Adapter constructor expects GeoIp2\\\\ProviderInterface, GeoIp2\\\\ProviderInterface\\|null given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__.'/src/ProviderFactory/GeoIP2Factory.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$licenseKey of class GeoIp2\\\\WebService\\\\Client constructor expects string, string\\|null given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__.'/src/ProviderFactory/GeoIP2Factory.php',
];
// See https://github.com/phpstan/phpstan/issues/14067
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$provider of class Bazinga\\\\GeocoderBundle\\\\Mapping\\\\ClassMetadata constructor expects non-empty-string, ReflectionMethod\\|ReflectionProperty\\|non-empty-string given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__.'/src/Mapping/Driver/AttributeDriver.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
