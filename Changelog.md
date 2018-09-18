# Changelog

The changelog describes what have been "Added", "Changed", "Removed" or "Fixed" between versions. 

## Version 5.3.0

### Added 

- Support for Ipstack provider
- Support for adding precision argument for the Cache plugin. 

## Version 5.2.0

### Added 

- Support for Nominatim 5.0

### Fixed

- Issue when defining plugins. 
- Fixed invalid HTML profiler details table.

## Version 5.1.2

### Fixed

- Make sure commands not using the container. 
- Fixed issue with using custom factories. We do not validate custom factories better. 
- We are more relaxed in our requirements for HTTPClients. You may now use the option `http_client`. 

## Version 5.1.1

### Fixed

- Adding commands as services
- Fixed twig paths for webprofiler

## Version 5.1.0

### Added

- `Collector::clear` to be compatible with Symfony 4. 
- Added support for IpInfo. 

## Version 5.0.0

Version 5 does only support Symfony 3.3+ and PHP7. We dropped some complexity and added plenty of type hints.  

### Added

- Support for Geocoder 4.0
- Provider factories
- Support for plugins

### Changed

- Namespace changed from `Bazinga\Bundle\GeocoderBundle` to `Bazinga\GeocoderBundle`
- The "fake IP" feature does not change any environment or Symfony variables. 
- Configuration for providers has been changed. We now use factories.

Before:

```yaml
bazinga_geocoder:
  providers:
    bing_maps:
      api_key: "Foo"
      locale: 'sv'
``` 
After:

```yaml
bazinga_geocoder:
  providers:
    acme:
      factory: "Bazinga\GeocoderBundle\ProviderFactory\BingMapsFactory"
      locale: 'sv'
      options:
        api_key: "foo"
``` 

### Removed

- `DumperManager`
- `LoggableGeocoder`, use `LoggerPlugin` instead. 
- Configuration for default provider (`default_provider`)
- `Bazinga\Bundle\GeocoderBundle\Provider\Cache` was removed, use `CachePlugin` instead. 
- All services IDs was removed except `bazinga_geocoder.geocoder` and `geocoder`.

## Version 4.1.0

No changelog before this version
