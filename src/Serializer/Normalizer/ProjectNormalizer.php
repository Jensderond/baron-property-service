<?php

namespace App\Serializer\Normalizer;

use App\Entity\ConstructionNumber;
use App\Entity\Project;
use App\Entity\ConstructionType;
use App\Model\Status;
use App\Helpers\ArrayHelper;
use App\Helpers\KeyTranslationsHelper;
use DateTimeImmutable;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ProjectNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function __construct(#[Autowire(service: 'app.object_normalizer')] private NormalizerInterface&DenormalizerInterface $objectNormalizer)
    {
    }

    public function supportsDenormalization($data, $type, $format = null, array $context = [])
    {
        return $type === Project::class; // Adjust the namespace accordingly
    }

    public function supportsNormalization($data, $format = null, array $context = [])
    {
        return $data instanceof Project; // Adjust the namespace accordingly
    }

    public function denormalize($data, $type, $format = null, array $context = [])
    {
        $data['externalId'] = $data['project']['id'];
        $data['algemeen'] = $data['project']['algemeen'];
        $data['province'] = $data['project']['algemeen']['provincie'];
        $data['zipcode'] = $data['project']['algemeen']['postcode'];
        $data['city'] = $data['project']['algemeen']['plaats'];
        $data['description'] = $data['teksten']['aanbiedingstekst'];
        $data['title'] = $data['project']['algemeen']['omschrijving'];
        if (isset($data['media'])) {
            $data['media'] = $data['media'];
        } else {
            $data['media'] = [];
        }

        $data['diversen'] = $data['project']['diversen'];

        $livingAreaFrom = $data['project']['algemeen']['woonoppervlakteVanaf'];
        $livingAreaTo = $data['project']['algemeen']['woonoppervlakteTot'];

        $livingAreaCombined = '';

        if (isset($livingAreaFrom)) {
            if (isset($livingAreaTo) && $livingAreaFrom !== $livingAreaTo) {
                $livingAreaCombined = $livingAreaFrom . ' tot ' . $livingAreaTo;
            } else {
                $livingAreaCombined  = $livingAreaFrom;
            }
        }


        $plotAreaFrom = $data['project']['algemeen']['perceeloppervlakteVanaf'];
        $plotAreaTo = $data['algemeen']['perceeloppervlakteTot'];
        $plotAreaCombined = '';

        if (isset($plotAreaFrom)) {
            if (isset($plotAreaTo) && $plotAreaFrom !== $plotAreaTo) {
                $plotAreaCombined = $plotAreaFrom . ' tot ' . $plotAreaTo;
            } else {
                $plotAreaCombined = $plotAreaFrom;
            }
        }

        /**
         * Op de aanbodpagina (bij nieuwbouw) mag er dan zo'n zelfde balkje komen als verhuurd/verkocht.
         * Wanneer de start verkoop nog niet gestart is, mag er inschrijving gestart vermeld worden.
         * Wanneer de start verkoop al wel is gestart, mag er start verkoop vermeld worden.
         * Wanneer de start verkoop + bouw wel gestart is, mag er start bouw vermeld worden.
         * Wanneer de opleveringen gestart zijn, mag er opleveringen gestart vermeld worden.
         * Wanneer ik hem op verkocht zet (het totale project, alle bouwnummers), mag dit balkje net als bij de rest verkocht vermeld worden.
         */
        $dateStartBuilding = $data['project']['algemeen']['datumStartBouw'];
        $dateStartBuilding = $dateStartBuilding ? new DateTimeImmutable($dateStartBuilding) : null;
        $dateEndBuilding = $data['project']['algemeen']['opleveringsdatum'];
        $dateEndBuilding = $dateEndBuilding ? new DateTimeImmutable($dateEndBuilding) : null;
        $dateStartSelling = $data['project']['algemeen']['datumStartVerkoop'];
        $dateStartSelling = $dateStartSelling ? new DateTimeImmutable($dateStartSelling) : null;

        $dateNow = new DateTimeImmutable();
        $status = "";
        if ($dateNow < $dateStartSelling) {
            $status = "Inschrijving gestart";
        } elseif ($dateNow < $dateStartBuilding || $dateStartBuilding === null) {
            $status = "Verkoop gestart";
        } elseif ($dateNow < $dateEndBuilding || $dateEndBuilding === null) {
            $status = "Bouw gestart";
        } elseif ($dateNow > $dateEndBuilding) {
            $status = "Oplevering gestart";
        }
        if ($data['project']['algemeen']['status'] === 'VERKOCHT') {
            $status = "Verkocht";
        }

        $project = new Project();
        $project->setExternalId($data['externalId']);
        $project->setAlgemeen($data['algemeen']);
        $project->setArchived(false);
        $project->setStatus($data['algemeen']['status']);
        $project->setReadableStatus($status);
        $project->setProvince($data['province']);
        $project->setZipcode($data['zipcode']);
        $project->setCity($data['city']);
        $project->setDescription($data['description']);
        if ($data['city']) {
            $project->setTitle($data['title'] . ', ' . $data['city']);
        } else {
            $project->setTitle($data['title']);
        }
        $project->setCategory(KeyTranslationsHelper::projectCategory($data['algemeen']['koopOfHuur']));

        /** Media */
        $mainImage = array_filter($data['media'], function ($media) {
            return $media['soort'] === 'HOOFDFOTO';
        });

        // get first item in $mainImage array
        $mainImage = array_values($mainImage);

        if (isset($mainImage[0])) {
            $project->setMainImage([$mainImage[0]]);
        } else {
            $project->setMainImage($data['media'][0] ?: null);
        }

        $project->setMedia($data['media']);
        ArrayHelper::sort($data['media']);
        $project->setMediaHash(md5(json_encode($data['media'])));
        $project->setDiversen($data['diversen']);
        $project->setCreatedAt(new DateTimeImmutable($data['marketing']['publicatiedatum']));
        $project->setUpdatedAt(new DateTimeImmutable($data['tijdstipLaatsteWijziging']));
        $project->setLivingArea($livingAreaCombined);
        $project->setPlot($plotAreaCombined);

        $lowestNumberOfRooms = 0;
        $highestNumberOfRooms = 0;

        foreach ($data['bouwtypen'] as $bouwType) {
            $type = new ConstructionType();
            $type->setExternalId($bouwType['id']);
            $type->setTitle($bouwType['algemeen']['omschrijving']);
            $type->setMedia($bouwType['media']);
            $type->setAlgemeen($bouwType['algemeen']);
            $type->setTeksten($bouwType['teksten']);

            $totalRooms = 0;
            $etages = $bouwType['detail']['etages'];
            foreach ($etages as $etage) {
                $totalRooms += $etage['aantalKamers'];
            }
            $type->setRooms($totalRooms);

            if (isset($bouwType['algemeen']['woonhuistype']) || isset($bouwType['algemeen']['appartementsoort'])) {
                $type->setType(KeyTranslationsHelper::houseType($bouwType['algemeen']['woonhuistype'] ?? $bouwType['algemeen']['appartementsoort']));
            }


            $typeLivingAreaFrom = $bouwType['algemeen']['woonoppervlakteVanaf'];
            $typeLivingAreaTo = $bouwType['algemeen']['woonoppervlakteTot'];

            if (isset($typeLivingAreaFrom)) {
                if (isset($typeLivingAreaTo) && $typeLivingAreaFrom !== $typeLivingAreaTo) {
                    $type->setLivingArea($typeLivingAreaFrom . ' tot ' . $typeLivingAreaTo);
                } else {
                    $type->setLivingArea($typeLivingAreaFrom);
                }
            }

            foreach ($bouwType['bouwnummers'] as $number) {
                $constructionNumber = new ConstructionNumber();
                $constructionNumber->setExternalId($number['id']);
                $constructionNumber->setTitle($number['adres']['straat']);
                $constructionNumber->setAddress($number['adres']);
                $constructionNumber->setAlgemeen($number['algemeen']);
                $constructionNumber->setFinancieel($number['financieel']);
                $constructionNumber->setStatus($number['financieel']['overdracht']['status']);
                $constructionNumber->setReadableStatus(KeyTranslationsHelper::status($number['financieel']['overdracht']['status']));
                if (isset($number['algemeen']['energieklasse'])) {
                    $constructionNumber->setEnergyClass(KeyTranslationsHelper::energyClass($number['algemeen']['energieklasse']));
                }

                if (isset($number['teksten']['aanbiedingstekst'])) {
                    $constructionNumber->setDescription($number['teksten']['aanbiedingstekst']);
                }

                $constructionNumber->setTeksten($number['teksten']);
                $constructionNumber->setDiversen($number['diversen']);
                $constructionNumber->setDetail($number['detail']);
                ArrayHelper::sort($number['media']);
                $constructionNumber->setMedia($number['media']);
                $constructionNumber->setMediaHash(md5(json_encode($number['media'])));
                $constructionNumber->setUpdatedAt(new DateTimeImmutable($number['diversen']['diversen']['wijzigingsdatum']));

                $totalCNRooms = 0;
                $totalCNBedrooms = 0;

                $etages = $number['detail']['etages'];

                foreach ($etages as $etage) {
                    $totalCNRooms += $etage['aantalKamers'];
                    $totalCNBedrooms += $etage['aantalSlaapkamers'];
                }

                $constructionNumber->setPrice(
                    new \Money\Money(($number['financieel']['overdracht']['koopprijs'] ?: $number['financieel']['overdracht']['huurprijs']) * 100, new \Money\Currency('EUR'))
                );

                if (isset($number['financieel']['overdracht']['koopconditie']) || isset($number['financieel']['overdracht']['huurconditie'])) {
                    $constructionNumber->setPriceCondition(match ($number['financieel']['overdracht']['koopconditie'] ?? $number['financieel']['overdracht']['huurconditie']) {
                        // huur: PER_JAAR, PER_MAAND
                        'PER_JAAR' => 'p.j.',
                        'PER_MAAND' => 'p.m.',
                        // Koop: KOSTEN_KOPER, VRIJ_OP_NAAM
                        'KOSTEN_KOPER' => 'k.k.',
                        'VRIJ_OP_NAAM' => 'v.o.n.',
                    });
                }
                $constructionNumber->setRooms($totalCNRooms);
                $constructionNumber->setBedrooms($totalCNBedrooms);
                $constructionNumber->setConstructionType($type);
                $constructionNumber->setLivingArea($number['algemeen']['woonoppervlakte']);
                $constructionNumber->createSlug();

                $type->addConstructionNumber($constructionNumber);
            }

            if ($type->getRooms() > $highestNumberOfRooms) {
                $highestNumberOfRooms = $type->getRooms();
            }

            if ($lowestNumberOfRooms === 0 || $type->getRooms() < $lowestNumberOfRooms) {
                $lowestNumberOfRooms = $type->getRooms();
            }

            $project->addConstructionType($type);
        }

        if ($lowestNumberOfRooms !== $highestNumberOfRooms) {
            $project->setRooms($lowestNumberOfRooms . ' tot ' . $highestNumberOfRooms);
        } else {
            $project->setRooms($lowestNumberOfRooms);
        }

        return $project;
    }

    /**
     * @param Project $project
     */
    public function normalize($project, ?string $format = null, array $context = [])
    {
        $data = $this->objectNormalizer->normalize($project, $format, $context);

        if (isset($data['construction_types']) && isset($data['construction_types']['hydra:member'])) {
            $data['construction_types'] = $data['construction_types']['hydra:member'];

            usort($data['construction_types'], function ($a, $b) {
                $aAvailable = array_values(array_filter($a['construction_numbers'], function ($item) {
                    return $item['status'] === Status::AVAILABLE->value;
                }))[0] ?? null;

                $bAvailable = array_values(array_filter($b['construction_numbers'], function ($item) {
                    return $item['status'] === Status::AVAILABLE->value;
                }))[0] ?? null;

                if ($aAvailable !== null && $bAvailable === null) {
                    return -1;
                }

                if ($aAvailable === null && $bAvailable !== null) {
                    return 1;
                }

                return 0;
            });
        }

        if (isset($data['algemeen'])) {
            $data['algemeen'] = $project->getAlgemeen();
        }
        if (isset($data['diversen'])) {
            $data['diversen'] = $project->getDiversen();
        }
        if (isset($data['main_image'])) {
            $data['main_image'] = $project->getMainImage();
        }

        return $data;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            '*' => true,
            Project::class => true,
        ];
    }
}
