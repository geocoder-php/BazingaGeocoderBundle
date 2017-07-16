# Doctrine annotation support

*[<< Back to documentation index](Resources/doc/index.md)*

Wouldn't it be great if you could automatically save the coordinates of a users 
address every time it is updated? Wait not more here is the feature you been always
wanted.

``` php

use Bazinga\GeocoderBundle\Mapping\Annotation as Geocoder;

/**
 * @Geocoder\Geocodable
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

$user = new User();
$user->setAddress('Brandenburger Tor, Pariser Platz, Berlin');

$em->persist($event);
$em->flush();

echo $user->getLatitude(); // will output 52.516325
echo $user->getLongitude(); // will output 13.377264
```
