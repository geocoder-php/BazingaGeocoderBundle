BazingaGeocoderBundle
=====================

Integration of the [**Geocoder**](http://github.com/geocoder-php/Geocoder)
library into Symfony2.

* [Installation](#installation)
* [Usage](#usage)
  * [Killer Feature](#killer-feature)
  * [Registering Your Own Providers](#registering-your-own-providers)
  * [Dumpers](#dumpers)
  * [Cache Provider](#cache-provider)
  * [Symfony2 Profiler Integration](#symfony2-profiler-integration)
* [Reference Configuration](#reference-configuration)
* [Testing](#testing)


Installation
------------

Require
[`willdurand/geocoder-bundle`](https://packagist.org/packages/willdurand/geocoder-bundle)
to your `composer.json` file:


```json
{
    "require": {
        "willdurand/geocoder-bundle": "@stable"
    }
}
```

**Protip:** you should browse the
[`willdurand/geocoder-bundle`](https://packagist.org/packages/willdurand/geocoder-bundle)
page to choose a stable version to use, avoid the `@stable` meta constraint.

Register the bundle in `app/AppKernel.php`:

```php
// app/AppKernel.php
public function registerBundles()
{
    return array(
        // ...
        new Bazinga\Bundle\GeocoderBundle\BazingaGeocoderBundle(),
    );
}
```

Enable the bundle's configuration in `app/config/config.yml`:

``` yaml
# app/config/config.yml
bazinga_geocoder: ~
```

Usage
-----

This bundle registers a `bazinga_geocoder.geocoder` service which is an instance
of `Geocoder`. You'll be able to do whatever you want with it but be sure to
configure at least **one provider** first.

**NOTE:** When using `Request::getClientIp()` with Symfony 2.1+, ensure you have
a trusted proxy set in your `config.yml`:

``` yaml
# app/config/config.yml
framework:
    trusted_proxies: ['127.0.0.1']
    # ...
```

### Killer Feature

You can fake the `REMOTE_ADDR` HTTP parameter through this bundle in order to get
information in your development environment, for instance:

``` php
<?php

// ...

    /**
     * @Template()
     */
    public function indexAction(Request $request)
    {
        // Retrieve information from the current user (by its IP address)
        $result = $this->container
            ->get('bazinga_geocoder.geocoder')
            ->using('google_maps')
            ->geocode($request->server->get('REMOTE_ADDR'));

        // Find the 5 nearest objects (15km) from the current user.
        $address = $result->first();
        $objects = ObjectQuery::create()
            ->filterByDistanceFrom($address->getLatitude(), $address->getLongitude(), 15)
            ->limit(5)
            ->find();

        return array(
            'geocoded'        => $result,
            'nearest_objects' => $objects
        );
    }
```

In the example, we'll retrieve information from the user's IP address, and 5
objects nears him.
But it won't work on your local environment, that's why this bundle provides
an easy way to fake this behavior by using a `fake_ip` configuration.

``` yaml
# app/config/config_dev.yml
bazinga_geocoder:
    fake_ip:    123.345.643.133
```

If set, the parameter will replace the `REMOTE_ADDR` value by the given one.


Additionally if it interferes with your current
listeners, You can set up different fake ip listener priority.


``` yaml
# app/config/config_dev.yml
bazinga_geocoder:
    fake_ip:
        ip: 123.345.643.133
        priority: 128
```

### Registering Your Own Providers

If you want to use your own provider in your application, create a service,
and tag it as `bazinga_geocoder.provider`:

```xml
<service id="acme_demo.geocoder.my_provider" class="Acme\Demo\Geocoder\Provider\MyProvider">
    <tag name="bazinga_geocoder.provider" />
</service>
```

The bundle will automatically register your provider into the
`bazinga_geocoder.geocoder` service.

**Note:** the `bazinga_geocoder.geocoder.adapter` service represents the configured
Geocoder's adapter. If your provider needs an HTTP adapter, you should inject this
service into the service you have just created.

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
        ->get('bazinga_geocoder.geocoder')
        ->geocode($request->server->get('REMOTE_ADDR'));

    $body = $this->container
        ->get('bazinga_geocoder.dumper_manager')
        ->get('geojson')
        ->dump($result);

    $response = new Response();
    $response->setContent($body);

    return $response;
}
```

To register a new dumper, you must tag it with `bazinga_geocoder.dumper`.
The bundles will automatically register it.

A little example:

```xml
<service id="some.dumper" class="%some.dumper.class">
    <tag name="bazinga_geocoder.dumper" alias="custom" />
</service>
```

### Cache Provider

Sometimes you have to cache the results from a provider. For this case the bundle provides
a cache provider. The cache provider wraps another provider and delegate all calls
to this provider and cache the return value.

```yaml
# app/config/config*.yml
services:
    acme_cache_adapter:
        class: "Doctrine\\Common\\Cache\\ApcCache"

bazinga_geocoder:
    providers:
        cache:
            adapter:  acme_cache_adapter
            provider: google_maps
        google_maps: ~
```

> **Tip:** If you want to configure the cache adapter,
> we recommend the [doctrine/doctrine-cache-bundle](https://github.com/doctrine/DoctrineCacheBundle).

### Custom HTTP Adapter

Geocoder Bundle integrates with [egeloen/http-adapter] (https://github.com/egeloen/ivory-http-adapter). By default the bundle use a simple cURL Adapter. 
You can use any [PSR-7] (http://www.php-fig.org/psr/psr-7/) compliant adapter.

An example, if you want to use Guzzle.

```yaml
# app/config/config*.yml
services:
    guzzle_http_adapter:
        class: "Ivory\HttpAdapter\Guzzle6HttpAdapter"

bazinga_geocoder:
    adapter: guzzle_http_adapter
```

### Symfony2 Profiler Integration

Geocoder bundle additionally integrates with Symfony2 profiler. You can
check number of queries executed by each provider, total execution time
and geocoding results.

![Example
Toolbar](https://raw.github.com/geocoder-php/BazingaGeocoderBundle/master/Resources/doc/toolbar.png)


Reference Configuration
-----------------------

You MUST define the providers you want to use in your configuration.  Some of
them need information (API key for instance).

You'll find the reference configuration below:

``` yaml
# app/config/config*.yml
bazinga_geocoder:
    fake_ip:
        enabled:              true
        ip:                   null
        priority:             0
    adapter:  					~ # An adapter service id
    default_provider:         ~ # Name of provider, e.g. chain
    providers:
        bing_maps:
            api_key:              ~ # Required
            locale:               null
        cache:
            adapter:              ~ # Required
            provider:             ~ # Required
            locale:               null
            lifetime:             86400
        ip_info_db:
            api_key:              ~ # Required
        google_maps:
            locale:               null
            region:               null
            use_ssl:              false
            api_key:              null
        google_maps_business:
            client_id:            ~ # Required
            api_key:              null
            locale:               null
            region:               null
            use_ssl:              false
        openstreetmap:
            locale:               null
        host_ip:              []
        geoip:                []
        free_geo_ip:          []
        mapquest:
            api_key:              ~ # Required
        data_science_toolkit:  []
        yandex:
            locale:               null
            toponym:              null
        geo_ips:
            api_key:              null
        geo_plugin:           []
        maxmind:
            api_key:              ~ # Required
        maxmind_binary:
            binary_file:          ~ # Required
            open_flag:            null
        opencage:
            locale:               null
            use_ssl:              false
            api_key:              null
        chain:
            providers:            []
        tom_tom:
            api_key:              ~ # Required
            locale:               null
```


Testing
-------

Setup the test suite using [Composer](http://getcomposer.org/):

    $ composer install --dev --prefer-source

**Important:** this command must be run with `--prefer-source`, otherwise the
`Doctrine\Tests\OrmTestCase` class won't be found.

Run it using PHPUnit:

    $ phpunit
