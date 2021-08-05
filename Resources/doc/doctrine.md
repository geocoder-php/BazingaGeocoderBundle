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

If you are using PHP 8, then you can use [Attributes](https://www.php.net/manual/en/language.attributes.overview.php) in your entity:

```php

use Bazinga\GeocoderBundle\Mapping\Annotations as Geocoder;

#[Geocoder\Geocodeable()]
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
