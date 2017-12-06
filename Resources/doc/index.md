BazingaGeocoderBundle
=====================

Integration of the [**Geocoder**](http://github.com/geocoder-php/Geocoder) library into Symfony.

Our documentation has the following sections:

* [Index](index.md) (This page)
* [Public services](services.md) (Providers)
* [Registering Your Own Provider](custom-provider.md)
* [All about Cache](cache.md)
* [Plugins](plugins.md)
* [Doctrine support](doctrine.md)

Table of contents
-----------------

* [Installation](#installation)
* [Usage](#usage)
  * [Chain providers](#chain-providers)
  * [Fake local ip](#fake-local-ip)
  * [Cache](#cache-results)
  * [Dumpers](#dumper)
  * [Custom HTTP clients](#custom-http-clients)
* [Reference Configuration](#reference-configuration)
* [Backwards compatibility](#backwards-compatibility)
* [Testing](#testing)


Installation
------------

To install this bundle you need to know how to [install the geocoder and providers](https://github.com/geocoder-php/Geocoder#installation)
and then you may just install the bundle like normal: 

```bash
composer require willdurand/geocoder-bundle:^5.0
```

Register the bundle in `app/AppKernel.php`:

```php
// app/AppKernel.php
public function registerBundles()
{
    return array(
        // ...
        new Bazinga\GeocoderBundle\BazingaGeocoderBundle(),
    );
}
```

Usage
-----

The bundle helps you register your providers and to enable profiling support. To 
configure a provider you must use a `ProviderFactory`. See the following example
using Google Maps. 

```yaml
bazinga_geocoder:
  providers:
    acme:
      factory: Bazinga\GeocoderBundle\ProviderFactory\GoogleMapsFactory
```

This will create a service named `bazinga_geocoder.provider.acme` which is a 
`GoogleMapsProvider`.

You can also configure **all ``ProviderFactories``** to adjust the behavior of the 
provider.

```yaml
bazinga_geocoder:
  providers:
    acme:
      factory: Bazinga\GeocoderBundle\ProviderFactory\GoogleMapsFactory
      cache: 'any.psr16.service'
      cache_lifetime: 3600
      aliases: 
        - my_geocoder
```

This will create a service named `my_geocoder` that caches the responses for one 
hour.

**Most ``ProviderFactories``** do also take an array with options. This is usually 
parameters to the constructor of the provider. In the example of Google Maps:

```yaml
bazinga_geocoder:
  providers:
    acme:
      factory: Bazinga\GeocoderBundle\ProviderFactory\GoogleMapsFactory
      options: 
        httplug_client: 'httplug.client' # When using HTTPlugBundle
        region: 'Sweden'
        api_key: 'xxyy'
```

### Chain providers

Someone was thinking ahead here. Wouldn't it be nice if you could pass your request through different `ProviderFactories`? You can!! With the `ChainFactory`, see the configuration below.

```yaml
bazinga_geocoder:
  providers:
    acme:
      aliases:
        - my_geocoder
      cache: 'any.psr16.service'
      cache_lifetime: 3600
      factory: Bazinga\GeocoderBundle\ProviderFactory\GoogleMapsFactory
      options:
        api_key: 'xxxx'
    acme_ii:
      aliases:
        - my_geocoder_ii
      factory: Bazinga\GeocoderBundle\ProviderFactory\TomTomFactory
      options:
        api_key: 'xxyy'
        httplug_client: 'httplug.client' # When using HTTPlugBundle
        region: 'Sweden'
    chain:
      factory: Bazinga\GeocoderBundle\ProviderFactory\ChainFactory
      options:
        services: ['@bazinga_geocoder.provider.acme', '@bazinga_geocoder.provider.acme_ii']
```

The `services` key could also be as follows `services: ['@my_geocoder', '@my_geocoder_ii']`. Notice these are the values from the `aliases` key.

### Fake local ip

You can fake your local IP through this bundle in order to get location
information in your development environment, for instance:

```php
/**
 * @Template()
 */
public function indexAction(Request $request)
{
    // Retrieve information from the current user (by its IP address)
    $result = $this->container
        ->get('bazinga_geocoder.provider.acme')
        ->geocodeQuery(GeocodeQuery::create($request->server->get('REMOTE_ADDR')));

    // Find the 5 nearest objects (15km) from the current user.
    $coords = $result->first()->getCoordinates();;
    $objects = ObjectQuery::create()
        ->filterByDistanceFrom($coords->getLatitude(), $coords->getLongitude(), 15)
        ->limit(5)
        ->find();

    return array(
        'geocoded'        => $result,
        'nearest_objects' => $objects
    );
}
```

In the example above, we'll retrieve information from the user's IP address, and 5
objects nears him.
But it won't work on your local environment, that's why this bundle provides
an easy way to fake this behavior by using a `fake_ip` configuration.

```yaml
# app/config/config_dev.yml
bazinga_geocoder:
    fake_ip:    123.123.123.123
```

If set, the parameter will replace all instances of "127.0.0.1" in your queries and replace them with the given one.


### Cache Results

Sometimes you have to cache the results from a provider. For this case the bundle provides
simple configuration. You only need to provide a service name for you SimpleCache (PSR-16)
service and you are good to go. 

```yaml
bazinga_geocoder:
  providers:
    acme:
      factory: Bazinga\GeocoderBundle\ProviderFactory\GoogleMapsFactory
      cache: 'any.psr16.service'
      cache_lifetime: 3600
```

Read more about cache [here](cache.md).

### Dumpers

If you need to dump your geocoded data to a specific format, you can use the
__Dumper__ component. The following dumper's are supported:

 * Geojson
 * GPX
 * KMP
 * WKB
 * WKT

Here is an example:

```php
<?php

public function geocodeAction(Request $request)
{
    $result = $this->container
        ->get('bazinga_geocoder.provider.acme')
        ->geocodeQuery(GeocodeQuery::create($request->server->get('REMOTE_ADDR')));

    $body = $this->container
        ->get('Geocoder\Dumper\GeoJson')
        ->dump($result);

    $response = new Response();
    $response->setContent($body);

    return $response;
}
```

To register a new dumper, you must tag it with `bazinga_geocoder.dumper`.

```xml
<service id="some.dumper" class="%some.dumper.class">
    <tag name="bazinga_geocoder.dumper" alias="custom" />
</service>
```

### Custom HTTP Client

The HTTP geocoder providers integrates with [HTTPlug](http://httplug.io/). It will give you all
the power of the HTTP client. You have to select which one you want to use and how 
you want to configure it. 

Read their [usage page](http://docs.php-http.org/en/latest/httplug/users.html), you 
may also be interested in checking out the [HTTPlugBundle](https://github.com/php-http/HttplugBundle).

An example, if you want to use Guzzle6.

```bash
composer require php-http/guzzle6-adapter php-http/message
```

Reference Configuration
-----------------------

You'll find the reference configuration below:

``` yaml
# app/config/config.yml
bazinga_geocoder:
    profiling: 
        enabled: ~                # Default is same as kernel.debug
    fake_ip:
        enabled:              true
        ip:                   null
    providers:
        # ... 
        acme:
            factory:  ~           # Required
            cache: 'app.cache'
            cache_lifetime: 3600
            limit: 5
            locale: 'sv'
            logger: 'logger'
            plugins: 
                - my_custom_plugin
            aliases: 
                - acme
                - acme_geocoder
            options:
                foo: bar
                biz: baz
        # ...
        free_chain:
            aliases:
                - free_geo_chain
            factory: Bazinga\GeocoderBundle\ProviderFactory\ChainFactory
            options:
                services: ['@acme', '@acme_ii']
```

Backwards compatibility
-----------------------

The BazingaGeocoderBundle is just a Symfony integration for Geocoder-PHP and it
does not have any classes which falls under the BC promise. The backwards compatibility 
of the bundle is only the configuration and its values (and of course the behavior
of those values).

The public service names (excluding the ones related to profiling/DataCollector)
falls under the backwards compatibility promise. 

Bottom line is, that you can trust that your configuration will not break and that 
the services you use will still be working. 

Testing
-------

Setup the test suite using [Composer](http://getcomposer.org/):


```bash
composer update
composer test
```

### Doctrine test

There is also a test that tests the doctrine integration. It runs automatically on
Traivs but if you want to run it locally you must do the following.

```bash
composer require phpunit/phpunit:^5.7 --no-update
composer update --prefer-source
wget https://phar.phpunit.de/phpunit-5.7.phar
php phpunit-5.7.phar --testsuit doctrine 
```

**Important:** this command must be run with `--prefer-source`, otherwise the
`Doctrine\Tests\OrmTestCase` class won't be found.

