# Doctrine annotation support

*[<< Back to documentation index](/Resources/doc/index.md)*

Wouldn't it be great if you could automatically save the coordinates of a users
address every time it is updated? Wait not more here is the feature you been always
wanted.

First of all, update your entity:

```php

use Bazinga\GeocoderBundle\Mapping\Annotations as Geocoder;

/**
 * @Geocoder\Geocodeable
 */
class User
{
    /**
     * @Geocoder\Address
     */
    private $address;

    /**
     * @Geocoder\Latitude
     */
    private $latitude;

    /**
     * @Geocoder\Longitude
     */
    private $longitude;
}
```

Instead of annotating a property, you can also annotate a getter:

```php

use Bazinga\GeocoderBundle\Mapping\Annotations as Geocoder;

/**
 * @Geocoder\Geocodeable
 */
class User
{
    /**
     * @Geocoder\Latitude
     */
    private $latitude;

    /**
     * @Geocoder\Longitude
     */
    private $longitude;
    
    /**
     * @Geocoder\Address
     */
    public function getAddress(): string
    {
        // Your code...
    }
}
```

Secondly, register the Doctrine event listener and its dependencies in your `services.yaml` file.  
You have to indicate which provider to use to reverse geocode the address. Here we use `acme` provider we declared in bazinga_geocoder configuration earlier.

```yaml
    Bazinga\GeocoderBundle\Mapping\Driver\AnnotationDriver:
        class: Bazinga\GeocoderBundle\Mapping\Driver\AnnotationDriver
        arguments:
            - '@annotations.reader'

    Bazinga\GeocoderBundle\Doctrine\ORM\GeocoderListener:
        class: Bazinga\GeocoderBundle\Doctrine\ORM\GeocoderListener
        arguments:
            - '@bazinga_geocoder.provider.acme'
            - '@Bazinga\GeocoderBundle\Mapping\Driver\AnnotationDriver'
        tags:
            - doctrine.event_subscriber
```

It is done!  
Now you can use it:

```php
$user = new User();
$user->setAddress('Brandenburger Tor, Pariser Platz, Berlin');

$em->persist($event);
$em->flush();

echo $user->getLatitude(); // will output 52.516325
echo $user->getLongitude(); // will output 13.377264
```
