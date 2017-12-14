# Public services

*[<< Back to documentation index](Resources/doc/index.md)*

This is a list of our public services. They are all part of [BC promise](Resources/doc/index.md#backwards-compatibility).

### Provider Factories

Here is a list of all provider factories and their options. 

| Service | Options |
| ------- | ------- |
| `Bazinga\GeocoderBundle\ProviderFactory\ArcGISOnlineFactory` | httplug_client, source_country
| `Bazinga\GeocoderBundle\ProviderFactory\BingMapsFactory` | httplug_client, api_key
| `Bazinga\GeocoderBundle\ProviderFactory\ChainFactory` | services
| `Bazinga\GeocoderBundle\ProviderFactory\FreeGeoIpFactory` | httplug_client, base_url
| `Bazinga\GeocoderBundle\ProviderFactory\GeoIP2Factory` | provider, database_filename, user_id, license_key, webservice_options, locales, provider_service
| `Bazinga\GeocoderBundle\ProviderFactory\GeoipFactory` | 
| `Bazinga\GeocoderBundle\ProviderFactory\GeoIPsFactory` | httplug_client, api_key
| `Bazinga\GeocoderBundle\ProviderFactory\GeonamesFactory` | httplug_client, username
| `Bazinga\GeocoderBundle\ProviderFactory\GeoPluginFactory` | httplug_client
| `Bazinga\GeocoderBundle\ProviderFactory\GoogleMapsFactory` | httplug_client, api_key, region
| `Bazinga\GeocoderBundle\ProviderFactory\HostIpFactory` | httplug_client
| `Bazinga\GeocoderBundle\ProviderFactory\IpInfoFactory` | httplug_client
| `Bazinga\GeocoderBundle\ProviderFactory\IpInfoDbFactory` | httplug_client, api_key, precision
| `Bazinga\GeocoderBundle\ProviderFactory\MapQuestFactory` | httplug_client, api_key, licensed
| `Bazinga\GeocoderBundle\ProviderFactory\MapzenFactory` | httplug_client, api_key
| `Bazinga\GeocoderBundle\ProviderFactory\MaxMindBinaryFactory` | dat_file, open_flag
| `Bazinga\GeocoderBundle\ProviderFactory\MaxMindFactory` | httplug_client, api_key, endpoint
| `Bazinga\GeocoderBundle\ProviderFactory\NominatimFactory` | httplug_client, root_url
| `Bazinga\GeocoderBundle\ProviderFactory\OpenCageFactory` | httplug_client, api_key
| `Bazinga\GeocoderBundle\ProviderFactory\PickPointFactory` | httplug_client, api_key
| `Bazinga\GeocoderBundle\ProviderFactory\TomTomFactory` | httplug_client, api_key
| `Bazinga\GeocoderBundle\ProviderFactory\YandexFactory` | httplug_client, toponym

### Services

Except for the provider factories, here is a list of services this bundle exposes are: 

* `Geocoder\ProviderAggregator`
* `Geocoder\Dumper\GeoArray`
* `Geocoder\Dumper\GeoJson`
* `Geocoder\Dumper\Gpx`
* `Geocoder\Dumper\Kml`
* `Geocoder\Dumper\Wkb`
* `Geocoder\Dumper\Wkt`
