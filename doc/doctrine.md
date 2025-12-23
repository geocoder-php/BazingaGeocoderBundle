# Doctrine support

*[<< Back to documentation index](/doc/index.md)*

Wouldn't it be great if you could automatically save the coordinates of a user's
address every time it is updated? Well, wait no more—here is the feature you've
always wanted!

First of all, update your entity:

```php

use Bazinga\GeocoderBundle\Mapping\Attributes as Geocoder;

#[Geocoder\Geocodeable(provider: 'acme')]
class User
{
    #[Geocoder\Address()]
    private $address;

    #[Geocoder\Latitude()]
    private $latitude;

    #[Geocoder\Longitude()]
    private $longitude;
}
```

Instead of annotating a property, you can also annotate a getter:

```php

use Bazinga\GeocoderBundle\Mapping\Attributes as Geocoder;

#[Geocoder\Geocodeable(provider: 'acme')]
class User
{
    #[Geocoder\Latitude()]
    private $latitude;

    #[Geocoder\Longitude()]
    private $longitude;

    #[Geocoder\Address()]
    public function getAddress(): \Stringable|string
    {
        // Your code...
    }
}
```

Secondly, enable Doctrine ORM listener in the configuration:

```yaml
bazinga_geocoder:
    orm:
        enabled: true
```

That's it! Now you can use it:

```php
$user = new User();
$user->setAddress('Brandenburger Tor, Pariser Platz, Berlin');

$em->persist($user);
$em->flush();

echo $user->getLatitude(); // will output 52.516325
echo $user->getLongitude(); // will output 13.377264
```
