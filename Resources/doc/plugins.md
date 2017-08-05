# Plugins

*[<< Back to documentation index](Resources/doc/index.md)*

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
