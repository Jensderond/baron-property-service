<?php

namespace App\Serializer\Normalizer;

use App\Entity\LandRegistryData;
use App\Entity\PropertyDetail;
use App\Service\AddressService;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class PropertyNormalizer implements DenormalizerInterface
{
    public function __construct(protected AddressService $addressService)
    {
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = [])
    {
        $serializer = new Serializer(
            [new ObjectNormalizer()],
            [new JsonEncoder()]
        );

        $property = new \App\Entity\Property();
        $geoData = $this->addressService->getLatLngFromAddress($data['adres']['huisnummer'], $data['adres']['straat'], $data['adres']['plaats'], $data['adres']['land']);

        /** Address */
        $property->setAddress($data['adres']['straat'] . ' ' . $data['adres']['huisnummer'] . ', ' . $data['adres']['plaats'] . ' ' . $data['adres']['land']);
        $property->setHouseNumber($data['adres']['huisnummer']);
        $property->setHouseNumberAddition($data['adres']['huisnummertoevoeging']);
        $property->setCity($data['adres']['plaats']);
        $property->setZip($data['adres']['postcode']);
        $property->setStreet($data['adres']['straat']);
        $property->setLat($geoData['lat']);
        $property->setLng($geoData['lng']);


        /** Generic */
        $property->setExternalId($data['id']);
        $property->setCategory($data['object']['type']['objecttype']);
        $property->setTitle($property->getStreet() . ' ' . $property->getHouseNumber() . $property->getHouseNumberAddition() . ', ' . $property->getCity());
        $property->setAlgemeen($data['algemeen']);
        $property->setFinancieel($data['financieel']);
        $property->setTeksten($data['teksten']);

        /** Price */


        /** Detail */
        $landRegistryData = $serializer->deserialize(json_encode($data['detail']['kadaster'][0]['kadastergegevens'], true), LandRegistryData::class, 'json');

        $propertyDetail = new PropertyDetail();
        $propertyDetail->setKadaster($landRegistryData);
        $propertyDetail->setBuitenruimte($data['detail']['buitenruimte']);
        $property->setDetail($propertyDetail);
        $property->setStatus($data['financieel']['overdracht']['status']);

        return $property;
    }

    public function supportsDenormalization($data, string $type, ?string $format = null): bool
    {
        return $type === \App\Entity\Property::class;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return false;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            '*' => true,
            \App\Entity\Property::class => true,
            'App\Entity\Property[]' => true,
        ];
    }
}
