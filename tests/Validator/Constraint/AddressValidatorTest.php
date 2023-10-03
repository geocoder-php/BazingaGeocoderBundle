<?php

declare(strict_types=1);

/*
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Bazinga\GeocoderBundle\Tests\Validator\Constraint;

use Bazinga\GeocoderBundle\Validator\Constraint\Address;
use Bazinga\GeocoderBundle\Validator\Constraint\AddressValidator;
use Geocoder\Provider\Nominatim\Nominatim;
use Http\Message\RequestMatcher\RequestMatcher;
use Http\Mock\Client;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

final class AddressValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator(): AddressValidator
    {
        $requestMatcher = new RequestMatcher('search', 'nominatim.openstreetmap.org', 'GET', 'https');

        $httpClient = new Client();
        $httpClient->on($requestMatcher, function (RequestInterface $request) {
            switch ((string) $request->getUri()) {
                case 'https://nominatim.openstreetmap.org/search?format=jsonv2&q=Berlin%2C%20Germany&addressdetails=1&extratags=1&limit=5':
                    $response = $this->createMock(ResponseInterface::class);
                    $response->expects(self::once())
                        ->method('getStatusCode')
                        ->willReturn(200);
                    $response->expects(self::once())
                        ->method('getBody')
                        ->willReturn('[{"place_id":159647018,"licence":"Data © OpenStreetMap contributors, ODbL 1.0. http://osm.org/copyright","osm_type":"relation","osm_id":62422,"lat":"52.5170365","lon":"13.3888599","category":"boundary","type":"administrative","place_rank":8,"importance":0.7875390282491362,"addresstype":"city","name":"Berlin","display_name":"Berlin, Deutschland","address":{"city":"Berlin","ISO3166-2-lvl4":"DE-BE","country":"Deutschland","country_code":"de"},"extratags":{"ele": "35", "email": "info@berlin.de", "place": "city", "capital": "yes", "website": "http://www.berlin.de", "de:place": "city", "ref:nuts": "DE3;DE30;DE300", "wikidata": "Q64", "wikipedia": "de:Berlin", "population": "3769962", "ref:LOCODE": "DEBER", "ref:nuts:1": "DE3", "ref:nuts:2": "DE30", "ref:nuts:3": "DE300", "state_code": "BE", "name:prefix": "Land und Kreisfreie Stadt", "linked_place": "city", "official_status": "Land", "contact:facebook": "http://www.facebook.com/Berlin", "name:prefix:city": "Kreisfreie Stadt", "openGeoDB:loc_id": "14356", "capital_ISO3166-1": "yes", "name:prefix:state": "Land", "source:population": "https://download.statistik-berlin-brandenburg.de/fa93e3bd19a2e885/a5ecfb2fff6a/SB_A01-05-00_2020h02_BE.pdf", "license_plate_code": "B", "official_status:de": "Land", "official_status:en": "State", "official_status:ru": "земля", "geographical_region": "Barnim;Berliner Urstromtal;Teltow;Nauener Platte", "blind:description:de": "Auf www.berlinfuerblinde.de gibt es einen kostenlosen Audioguide und weitere Informationen.", "de:regionalschluessel": "110000000000", "openGeoDB:postal_codes": "10178,10115,10117,10119,10179,10243,10245,10247,10249,10315,10317,10318,10319,10365,10367,10369,10405,10407,10409,10435,10437,10439,10551,10553,10555,10557,10559,10585,10587,10589,10623,10625,10627,10629,10707,10709,10711,10713,10715,10717,10719,10777,10", "report_problems:website": "https://ordnungsamt.berlin.de/", "TMC:cid_58:tabcd_1:Class": "Area", "openGeoDB:license_plate_code": "B", "TMC:cid_58:tabcd_1:LCLversion": "12.0", "openGeoDB:telephone_area_code": "030", "TMC:cid_58:tabcd_1:LocationCode": "266", "de:amtlicher_gemeindeschluessel": "11000000", "openGeoDB:community_identification_number": "11000000"},"boundingbox":["52.3382448","52.6755087","13.0883450","13.7611609"]}]');

                    return $response;
                case 'https://nominatim.openstreetmap.org/search?format=jsonv2&q=Bifrost%2C%20Nine%20Realms&addressdetails=1&extratags=1&limit=5':
                    $response = $this->createMock(ResponseInterface::class);
                    $response->expects(self::once())
                        ->method('getStatusCode')
                        ->willReturn(200);
                    $response->expects(self::once())
                        ->method('getBody')
                        ->willReturn('[]');

                    return $response;
            }

            self::fail(sprintf('Unexpected http call "%s %s".', $request->getMethod(), (string) $request->getUri()));
        });

        $geocoder = Nominatim::withOpenStreetMapServer($httpClient, 'BazingaGeocoderBundle/Test');

        return new AddressValidator($geocoder);
    }

    public function testNullIsValid(): void
    {
        $this->validator->validate(null, new Address());

        $this->assertNoViolation();
    }

    public function testEmptyStringIsValid(): void
    {
        $this->validator->validate('', new Address());

        $this->assertNoViolation();
    }

    public function testExpectsStringCompatibleType(): void
    {
        $this->expectException(UnexpectedValueException::class);

        $this->validator->validate(new \stdClass(), new Address());
    }

    public function testValidAddress(): void
    {
        $this->validator->validate('Berlin, Germany', new Address());

        $this->assertNoViolation();
    }

    public function testInvalidAddress(): void
    {
        $address = 'Bifrost, Nine Realms';

        $constraint = new Address([
            'message' => 'myMessage {{ address }}',
        ]);

        $this->validator->validate($address, $constraint);

        $this->buildViolation('myMessage {{ address }}')
            ->setParameter('{{ address }}', '"'.$address.'"')
            ->setInvalidValue($address)
            ->setCode(Address::INVALID_ADDRESS_ERROR)
            ->assertRaised();
    }
}
