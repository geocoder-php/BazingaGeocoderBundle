BazingaGeocoderBundle
=====================

Integration of the [**Geocoder**](http://github.com/geocoder-php/Geocoder)
library into Symfony.

* [Installation](#installation)
* [Usage](#usage)
  * [Provider factories](#provider-factories)
  * [Services](#services)
  * [Fake local ip](#fake-local-ip)
  * [Registering Your Own Provider](#registering-your-own-provider)
  * [Dumpers](#dumper)
  * [Cache](#cache-results)
  * [Custom HTTP clients](#custom-http-clients)
  * [Doctrine support](Resources/doc/doctrine.md)
* [Reference Configuration](#reference-configuration)
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
        - my_geocoer
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

### Provider Factories

Here is a list of all provider factories and their options. 

| Service | Options |
| ------- | ------- |
| `Bazinga\GeocoderBundle\ProviderFactory\ChainFactory` | services
| `Bazinga\GeocoderBundle\ProviderFactory\GoogleMapsFactory` | httplug_client, region, api_key


### Services

Except for the provider factories, here is a list of services this bundle exposes are: 

* `Geocoder\ProviderAggregator`
* `Geocoder\Dumper\GeoArray`
* `Geocoder\Dumper\GeoJson`
* `Geocoder\Dumper\Gpx`
* `Geocoder\Dumper\Kml`
* `Geocoder\Dumper\Wkb`
* `Geocoder\Dumper\Wkt`


### Fake local ip

You can fake the `REMOTE_ADDR` HTTP parameter through this bundle in order to get
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


### Registering Your Own Providers

If you want to use your own provider in your application, create a service,
and tag it as `bazinga_geocoder.provider`:

```xml
<service id="acme_demo.geocoder.my_provider" class="Acme\Demo\Geocoder\Provider\MyProvider">
    <tag name="bazinga_geocoder.provider" />
</service>
```

The bundle will automatically register your provider into the
`Geocoder\ProviderAggregator` service and you provider will show up in the profiler. 

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
            cache: ~
            cache_lifetime: ~
            aliases: 
                - acme
                - acme_geocoder
            options:
                foo: bar
                biz: baz
```

Testing
-------

Setup the test suite using [Composer](http://getcomposer.org/):

```bash
composer install --dev --prefer-source
```

**Important:** this command must be run with `--prefer-source`, otherwise the
`Doctrine\Tests\OrmTestCase` class won't be found.

Run it using PHPUnit:

```bash
composer test
```
