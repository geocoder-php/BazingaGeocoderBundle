# Public services

*[<< Back to documentation index](/doc/index.md)*

This is a list of our public services. They are all part of [BC promise](/doc/index.md#backwards-compatibility).

### Provider Factories

Here is a list of all provider factories and their options.

| Service | Options |
| ------- | ------- |
| `Bazinga\GeocoderBundle\ProviderFactory\AlgoliaPlaceFactory` | http_client, api_key, app_id
| `Bazinga\GeocoderBundle\ProviderFactory\ArcGISOnlineFactory` | http_client, source_country
| `Bazinga\GeocoderBundle\ProviderFactory\BingMapsFactory` | http_client, api_key
| `Bazinga\GeocoderBundle\ProviderFactory\ChainFactory` | services
| `Bazinga\GeocoderBundle\ProviderFactory\FreeGeoIpFactory` | http_client, base_url
| `Bazinga\GeocoderBundle\ProviderFactory\GeoIP2Factory` | provider, database_filename, user_id, license_key, webservice_options, locales, provider_service
| `Bazinga\GeocoderBundle\ProviderFactory\GeoipFactory` |
| `Bazinga\GeocoderBundle\ProviderFactory\GeoIPsFactory` | http_client, api_key
| `Bazinga\GeocoderBundle\ProviderFactory\GeonamesFactory` | http_client, username
| `Bazinga\GeocoderBundle\ProviderFactory\GeoPluginFactory` | http_client
| `Bazinga\GeocoderBundle\ProviderFactory\GoogleMapsFactory` | http_client, api_key, region
| `Bazinga\GeocoderBundle\ProviderFactory\GoogleMapsPlacesFactory` | http_client, api_key
| `Bazinga\GeocoderBundle\ProviderFactory\HereFactory` | http_client, app_id, app_code, use_cit
| `Bazinga\GeocoderBundle\ProviderFactory\HostIpFactory` | http_client
| `Bazinga\GeocoderBundle\ProviderFactory\IpInfoFactory` | http_client
| `Bazinga\GeocoderBundle\ProviderFactory\IpInfoDbFactory` | http_client, api_key, precision
| `Bazinga\GeocoderBundle\ProviderFactory\IpstackFactory` | http_client, api_key
| `Bazinga\GeocoderBundle\ProviderFactory\LocationIQFactory` | http_client, api_key
| `Bazinga\GeocoderBundle\ProviderFactory\MapboxFactory` | http_client, api_key, country, mode
| `Bazinga\GeocoderBundle\ProviderFactory\MapQuestFactory` | http_client, api_key, licensed
| `Bazinga\GeocoderBundle\ProviderFactory\MapzenFactory` | http_client, api_key
| `Bazinga\GeocoderBundle\ProviderFactory\MaxMindBinaryFactory` | dat_file, open_flag
| `Bazinga\GeocoderBundle\ProviderFactory\MaxMindFactory` | http_client, api_key, endpoint
| `Bazinga\GeocoderBundle\ProviderFactory\NominatimFactory` | http_client, root_url
| `Bazinga\GeocoderBundle\ProviderFactory\OpenCageFactory` | http_client, api_key
| `Bazinga\GeocoderBundle\ProviderFactory\OpenRouteServiceFactory` | http_client, api_key
| `Bazinga\GeocoderBundle\ProviderFactory\PickPointFactory` | http_client, api_key
| `Bazinga\GeocoderBundle\ProviderFactory\TomTomFactory` | http_client, api_key
| `Bazinga\GeocoderBundle\ProviderFactory\YandexFactory` | http_client, toponym

### Services

Except for the provider factories, here is a list of services this bundle exposes are:

* `Geocoder\ProviderAggregator`
* `Geocoder\Dumper\GeoArray`
* `Geocoder\Dumper\GeoJson`
* `Geocoder\Dumper\Gpx`
* `Geocoder\Dumper\Kml`
* `Geocoder\Dumper\Wkb`
* `Geocoder\Dumper\Wkt`
