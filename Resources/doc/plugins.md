# Plugins

*[<< Back to documentation index](Resources/doc/index.md)*

Plugins lets you modify or take actions the `Query` or the result. There are a few plugins from the `geocoder-php/plugin` 
package like `LimitPlugin`, `LoggerPlugin` and `CachePlugin`. Some of them are supported in the configuration. 

```yaml
# config.yml
bazinga_geocoder:
  fake_ip:
    ip: '123.123.123.123' # Uses the FakeIpPlugin
  providers:
    acme:
      factory: Bazinga\GeocoderBundle\ProviderFactory\GoogleMapsFactory
      cache: 'app.cache' # Uses the CachePlugin
      limit: 5           # Uses the LimitPlugin
      locale: 'sv'       # Uses the LocalePlugin
      logger: 'logger'   # Uses the LoggerPlugin
``` 

To use a any other plugins you must first register them as a service. Say you want to add some data to each query. You
may then use the `QueryDataPlugin`. 

```yaml
# services.yml
sevices: 
  app.query_data_plugin:
    class: Geocoder\Plugin\Plugin\QueryDataPlugin
    arguments: 
      - ['foo': 'bar']
      - true
```

```yaml
# config.yml
bazinga_geocoder:
  providers:
    acme:
      factory: Bazinga\GeocoderBundle\ProviderFactory\GoogleMapsFactory
      plugins: 
        - "app.query_data_plugin"
``` 

This will execute `$query = $query->withData('foo', 'bar');` on all queries executed by the acme provider.

Read more about plugins at the [Geocoder's documentation](https://github.com/geocoder-php/Geocoder). 
