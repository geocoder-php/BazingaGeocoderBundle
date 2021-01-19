# Changelog

The changelog describes what have been "Added", "Changed", "Removed" or "Fixed" between versions.

## Version 5.14.0

### Added

- Add support for PHP 8.0

### Changed

- Upgrade dependencies to up to date versions

### Removed

- Remove PHP 7.2 support
- Remove Symfony 3.4 support

## Version 5.13.0

### Added

- Add support for api-key based authentication in here factory

## Version 5.12.0

### Added

- Allow configuring local IP when using FakeIpPlugin

### Changed

- Skip geocoding if address has not changed

## Version 5.11.0

### Added

- Allow an HTTP client to be injected via the constructor of providers
- Add MapboxFactory to docs

### Fixed

- replace empty() check

## Version 5.10.0

### Added

- Add MapboxFactory
- Add GoogleMapsPlacesFactory

### Fixed

- Fix tests/deprecations and Symfony 5 support

## Version 5.9.2

### Fixed

- Only trigger provider deprecation notices when they are explicitly configured

## Version 5.9.1

### Fixed

- Fix extension cannot replace non-defined argument

## Version 5.9.0

### Added

- Auto tag dumpers with `bazinga_geocoder.dumper`
- Add api_key for the YandexFactory
- Add option to use Faker library for FakeIpPlugin

## Version 5.8.0

### Added

- Add logger support to ChainFactory

### Changed

- Update FreeGeoIpFactory with freegeoip.app

## Version 5.7.0

### Added

- Add Algolia provider factory

### Changed

- Skip empty address values in listener
- Allow cache lifetime as null
- Update dev dependencies
- Make sure we run action on PRs

## Version 5.6.0

### Added

- Added missing step in Doctrine documentation
- Address annotation can be used over a getter
- Add docs about autowiring
- Integrate phpstan at level 2
- Adding github actions

### Changed

- Deprecate MapzenFactory
- Deprecate GeoIPsFactory
- Rename Changelog.md to CHANGELOG.md

### Removed

- Remove useless phpdocs

## Version 5.5.0

### Added

- Add autowiring bindings by Provider interface + providerName
- Exposes Here provider to the bundle

### Fixed

- Add missing tag for AddressValidator constraint

### Changed

- Update readme
- Fix method name
- Drop unmaintained Symfony versions support

## Version 5.4.0

### Added

- Add address validator constraint

### Fixed

- SF 4.2 Compliance
- Fix another SF 4.2 deprecation
- Doc fixes
- Custom vendor location symfony 4

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
