<?php

namespace App\Serializer\Normalizer;

use App\Entity\PropertyDetail;
use App\Service\AddressService;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class PropertyNormalizer implements DenormalizerInterface
{
    public function __construct(protected AddressService $addressService)
    {
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = [])
    {
        $property = new \App\Entity\Property();

        $geoData = $this->addressService->getLatLngFromAddress($data['adres']['huisnummer'], $data['adres']['straat'], $data['adres']['plaats'], $data['adres']['land']);

        $property->setAddress($data['adres']['straat'] . ' ' . $data['adres']['huisnummer'] . ', ' . $data['adres']['plaats'] . ' ' . $data['adres']['land']);
        $property->setHouseNumber($data['adres']['huisnummer']);
        $property->setHouseNumberAddition($data['adres']['huisnummertoevoeging']);
        $property->setCity($data['adres']['plaats']);
        $property->setZip($data['adres']['postcode']);
        $property->setCategory($data['object']['type']['objecttype']);
        $property->setLat($geoData['lat']);
        $property->setLng($geoData['lng']);
        $propertyDetail = new PropertyDetail();
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
