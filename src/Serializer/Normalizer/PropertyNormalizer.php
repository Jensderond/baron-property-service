<?php

namespace App\Serializer\Normalizer;

use App\Entity\LandRegistryData;
use App\Entity\Media;
use App\Entity\PropertyDetail;
use App\Service\AddressService;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
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
        dump($data['algemeen']['bouwjaar']);
        $data['algemeen']['bouwjaar'] ?? $property->setBuildYear($data['algemeen']['bouwjaar']);
        $data['algemeen']['energieklasse'] ?? $property->setEnergyClass($data['algemeen']['energieklasse']);

        /** Media */
        $mainImage = array_filter($data['media'], function ($media) {
            return $media['soort'] === 'HOOFDFOTO';
        });

        // get first item in $mainImage array
        $mainImage = array_values($mainImage);

        if (isset($mainImage[0])) {
            $property->setImage(
                new Media($mainImage[0]['mimetype'], $mainImage[0]['link'])
            );
        } else {
            $property->setImage(
                new Media($data['media'][0]['mimetype'], $data['media'][0]['link'])
            );
        }

        foreach ($data['media'] as $media) {
            $property->addMedium(
                new Media($media['mimetype'], $media['link'], $media['soort'], $media['volgnummer'])
            );
        }

        /** Price */
        $property->setPrice($data['financieel']['overdracht']['koopprijs']);
        $property->setRentalPrice($data['financieel']['overdracht']['huurprijs']);

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
