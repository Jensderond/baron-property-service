<?php

namespace App\Serializer\Normalizer;

use App\Helpers\ArrayHelper;
use \App\Entity\Property;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class PropertyNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function __construct(#[Autowire(service: 'app.object_normalizer')] private NormalizerInterface&DenormalizerInterface $objectNormalizer) {
    }

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
        $property->setCreatedAt(new \DateTimeImmutable($data['marketing']['publicatiedatum']));
        $property->setUpdatedAt(new \DateTimeImmutable($data['tijdstipLaatsteWijziging']));
        $property->setExternalId($data['id']);
        $property->setArchived(false);
        $property->setCategory($data['object']['type']['objecttype']);
        if($numberIsZero) {
            $property->setTitle("{$property->getStreet()}, {$property->getCity()}");
        } else {
            $property->setTitle("{$property->getStreet()} {$property->getHouseNumber()}{$property->getHouseNumberAddition()}, {$property->getCity()}");
        }
        $property->setAlgemeen($data['algemeen']);
        $property->setFinancieel($data['financieel']);

        if (isset($data['teksten']['eigenSiteTekst'])) {
            $property->setDescription($data['teksten']['eigenSiteTekst']);
        }
        if (!isset($data['teksten']['eigenSiteTekst']) && isset($data['teksten']['aanbiedingstekst'])) {
            $property->setDescription($data['teksten']['aanbiedingstekst']);
        }

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
            $property->setImage($data['media'][0] ?: null);
        }

        $property->setMedia($data['media']);
        ArrayHelper::sort($data['media']);
        $property->setMediaHash(md5(json_encode($data['media'])));
        $property->setEtages($data['detail']['etages']);

        $etages = $data['detail']['etages'];

        $slaapkamers = array_reduce($etages, function ($carry, $item) {
            return $carry + $item['aantalSlaapkamers'];
        }, 0);

        $property->setBedrooms($slaapkamers);


        $totalRooms = 0;
        $etages = $data['detail']['etages'];
        foreach ($etages as $etage) {
            $totalRooms += $etage['aantalKamers'];
        }
        $property->setRooms($totalRooms);
        $property->setLivingArea($data['algemeen']['woonoppervlakte']);

        $property->setOverigOnroerendGoed($data['detail']['overigOnroerendGoed']);
        $property->setBuitenruimte($data['detail']['buitenruimte']);
        $property->setPlot($data['algemeen']['totaleWoonkameroppervlakte'] ?: $data['algemeen']['totaleKadestraleOppervlakte']);

        /** Price */
        $condition = $data['financieel']['overdracht']['koopconditie'] ?? $data['financieel']['overdracht']['huurconditie'];
        if(isset($condition)) {
            $property->setPriceCondition(match($condition) {
                // huur: PER_JAAR, PER_MAAND
                'PER_JAAR' => 'p.j.',
                'PER_MAAND' => 'p.m.',
                // Koop: KOSTEN_KOPER, VRIJ_OP_NAAM
                'KOSTEN_KOPER' => 'k.k.',
                'VRIJ_OP_NAAM' => 'v.o.n.',
            });
        }
        $property->setPrice($data['financieel']['overdracht']['koopprijs'] ?? $data['financieel']['overdracht']['huurprijs']);
        $property->setStatus($data['financieel']['overdracht']['status']);

        return $property;
    }

    /**
     * @param Property $project
     */
    public function normalize($project, ?string $format = null, array $context = [])
    {
        $data = $this->objectNormalizer->normalize($project, $format, $context);

        if(isset($data['algemeen'])) {
            $data['algemeen'] = $project->getAlgemeen();
        }
        if(isset($data['financieel'])) {
            $data['financieel'] = $project->getFinancieel();
        }
        if(isset($data['teksten'])) {
            $data['teksten'] = $project->getTeksten();
        }
        if(isset($data['image'])) {
            $data['image'] = $project->getImage();
        }
        if(isset($data['media'])) {
            $data['media'] = $project->getMedia();
        }
        if(isset($data['etages'])) {
            $data['etages'] = $project->getEtages();
        }
        if(isset($data['buitenruimte'])) {
            $data['buitenruimte'] = $project->getBuitenruimte();
        }

        return $data;
    }

    public function supportsDenormalization($data, string $type, ?string $format = null): bool
    {
        return $type === Property::class;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof Property;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            \App\Entity\Property::class => true,
            'App\Entity\Property[]' => true,
        ];
    }
}
