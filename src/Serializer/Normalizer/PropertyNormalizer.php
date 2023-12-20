<?php

namespace App\Serializer\Normalizer;

use App\Entity\LandRegistryData;
use App\Entity\Media;
use App\Entity\PropertyDetail;
use App\Service\AddressService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class PropertyNormalizer implements DenormalizerInterface
{
    public function __construct(protected EntityManagerInterface $entityManager)
    {
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = [])
    {
        $serializer = new Serializer(
            [new ObjectNormalizer()],
            [new JsonEncoder()]
        );

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

        /** @var PropertyDetailRepository $propertyDetailRepo */
        $propertyDetailRepo = $this->entityManager->getRepository(PropertyDetail::class);

        $propertyDetail = $propertyDetailRepo->findOneBy(['id' => $property->getExternalId()]);
        if (!$propertyDetail) {
            $propertyDetail = new PropertyDetail($property->getExternalId());
        }

        $propertyDetail->setEtages($data['detail']['etages']);
        $propertyDetail->setOverigOnroerendGoed($data['detail']['overigOnroerendGoed']);
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
            \App\Entity\Property::class => true,
            'App\Entity\Property[]' => true,
        ];
    }
}
