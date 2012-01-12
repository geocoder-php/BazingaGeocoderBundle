BazingaGeocoderBundle
=====================

Integration of the [**Geocoder**](http://github.com/willdurand/Geocoder) library into Symfony2.


Installation
------------

Install this bundle as usual:

> git submodule add git://github.com/willdurand/BazingaGeocoderBundle.git vendor/bundles/Bazinga/Bundle/GeocoderBundle

Add the [Geocoder](https://github.com/willdurand/Geocoder) library:

> git submodule add git://github.com/willdurand/Geocoder.git vendor/geocoder

As an alternative, you can also manage your vendors via Symfony2's own `bin/vendor` command.
Add the following lines to your deps file (located in the root of the Symfony project:

    [BazingaGeocoderBundle]
        git=https://github.com/willdurand/BazingaGeocoderBundle.git
        target=/bundles/Bazinga/Bundle/GeocoderBundle
    [geocoder]
        git=https://github.com/willdurand/Geocoder.git

Update your vendor directory with:

    php bin/vendors install

Then register the namespace in `app/autoload.php`:

    // app/autoload.php
    $loader->registerNamespaces(array(
        // ...
        'Bazinga'       => __DIR__.'/../vendor/bundles',
        'Geocoder'      => __DIR__.'/../vendor/geocoder/src',
    ));

Register the bundle in `app/AppKernel.php`:

    // app/AppKernel.php
    public function registerBundles()
    {
        return array(
            // ...
            new Bazinga\Bundle\GeocoderBundle\BazingaGeocoderBundle(),
        );
    }


Usage
-----

This bundle registers a `bazinga_geocoder.geocoder` service which is an instance of `Geocoder`. You'll be able to do whatever you want with it.

#### Killer Feature ####

You can fake the `REMOTE_ADDR` HTTP parameter through this bundle in order to get information in your development environment, for instance:

``` php
<?php

// ...

    /**
     * @Template()
     */
    public function indexAction()
    {
        // Retrieve information from the current user (by its IP address)
        $result = $this->geocoder
            ->using('yahoo')
            ->geocode($this->getRequest()->server->get('REMOTE_ADDR'));

        // Find the 5 nearest objects from the current user.
        $objects = ObjectQuery::create()
            ->filterByDistanceFrom($result->getLatitude(), $result->getLongitude(), 15)
            ->limit(5)
            ->find();

        return array(
            'geocoded'        => $result,
            'nearest_objects' => $objects
        );
    }
```

In the example, we'll retrieve information from the user's IP address, and 5 objects nears him.
But it won't work on your local environment, that's why this bundle provides an easy way to fake this behavior by using a `fake_ip` parameter.

``` yaml
# app/config/config_dev.yml
bazinga_geocoder:
    fake_ip:    123.345.643.133
```

If set, the parameter will replace the `REMOTE_ADDR` value by the given one.


Reference Configuration
-----------------------

There is no required configuration in order to use the Geocoder service but some providers need information (API key for instance).

You'll find the reference configuration below:

``` yaml
# app/config/config*.yml

bazinga_geocoder:
    fake_ip:    999.999.999.999
    adapter:
        class:  \Your\CustomAdapter
    providers:
        bing_maps:
            api_key:    XXXXXXXXX
            locale:     xx_XX
        google_maps:
            api_key:    XXXXXXXXX
        ip_info_db:
            api_key:    XXXXXXXXX
        yahoo:
            api_key:    XXXXXXXXX
            locale:     xx_XX
```


Credits
-------

* William Durand <william.durand1@gmail.com>


License
-------

See `Resources/meta/LICENSE`.
