<?php

namespace App\Serializer\Normalizer;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;

class PropertyNormalizer implements DenormalizerInterface
{
    use NormalizerAwareTrait;

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = [])
    {
        $property = new \App\Entity\Property();

        /** Address */
        if(isset($data['adres']['huisnummer'])) {
            $property->setHouseNumber($data['adres']['huisnummer']);
        }
        if(isset($data['adres']['huisnummertoevoeging'])) {
            $property->setHouseNumberAddition($data['adres']['huisnummertoevoeging']);
        }
        if(isset($data['adres']['plaats'])) {
            $property->setCity($data['adres']['plaats']);
        }
        if(isset($data['adres']['postcode'])) {
            $property->setZip($data['adres']['postcode']);
        }
        if(isset($data['adres']['straat'])) {
            $property->setStreet($data['adres']['straat']);
        }

        $numberIsZero = $property->getHouseNumber() === "0" && null !== $property->getHouseNumber();

        if($numberIsZero) {
            $property->setAddress("{$property->getStreet()}, {$property->getCity()}");
        } else {
            $property->setAddress("{$property->getStreet()} {$property->getHouseNumber()}{$property->getHouseNumberAddition()}, {$property->getCity()}");
        }

        /** Generic */
        $property->setCreatedAt(new \DateTimeImmutable($data['diversen']['diversen']['invoerdatum']));
        $property->setUpdatedAt(new \DateTimeImmutable($data['tijdstipLaatsteWijziging']));
        $property->setExternalId($data['id']);
        $property->setCategory($data['object']['type']['objecttype']);
        if($numberIsZero) {
            $property->setTitle("{$property->getStreet()}, {$property->getCity()}");
        } else {
            $property->setTitle("{$property->getStreet()} {$property->getHouseNumber()}{$property->getHouseNumberAddition()}, {$property->getCity()}");
        }
        $property->setAlgemeen($data['algemeen']);
        $property->setFinancieel($data['financieel']);
        $property->setTeksten($data['teksten']);

        if (isset($data['algemeen']['bouwjaar'])) {
            $property->setBuildYear($data['algemeen']['bouwjaar']);
        }
        if (isset($data['algemeen']['energieklasse'])) {
            $property->setEnergyClass($data['algemeen']['energieklasse']);
        }

        /** Media */
        $mainImage = array_filter($data['media'], function ($media) {
            return $media['soort'] === 'HOOFDFOTO';
        });

        // get first item in $mainImage array
        $mainImage = array_values($mainImage);

        if (isset($mainImage[0])) {
            $property->setImage([$mainImage[0]]);
        } else {
            $property->setImage($data['media']);
        }

        $property->setMedia($data['media']);

        /** Price */
        $property->setPrice($data['financieel']['overdracht']['koopprijs']);
        $property->setRentalPrice($data['financieel']['overdracht']['huurprijs']);
        $property->setStatus($data['financieel']['overdracht']['status']);

        return $property;
    }

    public function supportsDenormalization($data, string $type, ?string $format = null): bool
    {
        return $type === \App\Entity\Property::class;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof \App\Entity\Property;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            \App\Entity\Property::class => true,
            'App\Entity\Property[]' => true,
        ];
    }
}
