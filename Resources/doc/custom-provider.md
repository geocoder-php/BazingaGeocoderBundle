# Registering Your Own Providers

*[<< Back to documentation index](Resources/doc/index.md)*

If you want to use your own provider in your application, create a service, and tag it as `bazinga_geocoder.provider`:

```xml
<service id="acme_demo.geocoder.my_provider" class="Acme\Demo\Geocoder\Provider\MyProvider">
    <argument type="service" id="foo_service" />
    <tag name="bazinga_geocoder.provider" />
</service>
```

The bundle will automatically register your provider into the`Geocoder\ProviderAggregator` service. However, it will not
show up the the web profiler because it is not registered with the [PluginProvider](Resources/doc/plguins.md).

If you want your provider to show up the web profiler you have to create a custom factory for your provider.

```php
namespace Acme\Demo\Geocoder\Factory;

use Bazinga\GeocoderBundle\ProviderFactory\AbstractFactory;
use Acme\Demo\Geocoder\Provider\MyProvider;
use Acme\Demo\Service\Foo;

final class MyFactory extends AbstractFactory
{
    private $fooService;
    
    public function __construct(Foo $service) {
        $this->someService = $service;
    }
 
    protected function getProvider(array $config)
    {
        return new MyProvider($this->fooService);
    }
}
``` 

```yaml
bazinga_geocoder:
  providers:
    acme:
      factory: Acme\Demo\Geocoder\Factory\MyFactory
      aliases: ['acme_demo.geocoder.my_provider']
```
