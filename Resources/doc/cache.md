# Caching the geocoder response

*[<< Back to documentation index](Resources/doc/index.md)*

It is quite rare that a location response gets updated. That is why it is a good idea to cache the responses. The second 
request will be both quicker and free of charge. To get started with caching you may use the `CachePlugin` which is supported
by default in our configuration. 

```yaml
# config.yml
bazinga_geocoder:
  providers:
    acme:
      factory: Bazinga\GeocoderBundle\ProviderFactory\GoogleMapsFactory
      cache: 'any.psr16.service'
      cache_lifetime: 3600
```

You may use any [PSR16](http://www.php-fig.org/psr/psr-16/) cache [implementation](https://packagist.org/providers/psr/simple-cache-implementation).
The `CachePlugin` helps you to cache all responses. 

## Decorator pattern

If you do not like using the `CachePlugin` for some reason you may use the [`CacheProvider`](https://github.com/geocoder-php/cache-provider).
The `CacheProvider` is using the [Decorator pattern](https://en.wikipedia.org/wiki/Decorator_pattern) to do caching. Which 
means that you wrap the `CacheProvider` around your existing provider. 

```bash
composer require geocoder-php/cache-provider
```

```yaml
# config.yml
bazinga_geocoder:
  providers:
    acme:
      factory: Bazinga\GeocoderBundle\ProviderFactory\GoogleMapsFactory
```

```yaml
# services.yml
servies:
  my_cached_geocoder:
    class: Geocoder\Provider\Cache\ProviderCache
    arguments: ['@bazinga_geocoder.provider.acme', '@any.psr16.service', 3600]
```

## Installing a PSR16 cache

You may use any adapter from [PHP-cache.com](http://www.php-cache.com/en/latest/) or `symfony/cache`. 

## Using Symfony cache

Symfony 3.3 does not support SimpleCache, but fear not. You can use a bridge between PSR-6 and PSR-16. Install the 
[bridge](https://github.com/php-cache/simple-cache-bridge) by:

```bash
composer require cache/simple-cache-bridge
```

Then register a service: 

```yaml
# services.yml
app.simple_cache:
    class: Cache\Bridge\SimpleCache\SimpleCacheBridge
    arguments: ['@app.cache.acme']
```

Then configure the framework and the bundle. 

```yaml
# config.yml
framework:
    cache:
        app: cache.adapter.redis
        pools:
            app.cache.acme:
                adapter: cache.app
                default_lifetime: 600

bazinga_geocoder:
  providers:
    my_google_maps:
      factory: Bazinga\GeocoderBundle\ProviderFactory\GoogleMapsFactory
      cache: 'app.simple_cache'
      cache_lifetime: 3600
```

