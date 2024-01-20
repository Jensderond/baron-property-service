<?php

namespace App\Serializer\Normalizer;

use App\Entity\ConstructionNumber;
use App\Entity\Project;
use App\Entity\ConstructionType;
use App\Model\Status;
use App\Helpers\ArrayHelper;
use DateTimeImmutable;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ProjectNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function __construct(#[Autowire(service: 'app.object_normalizer')] private NormalizerInterface&DenormalizerInterface $objectNormalizer) {
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
        $data['status'] = $data['project']['algemeen']['status'];
        $data['province'] = $data['project']['algemeen']['provincie'];
        $data['zipcode'] = $data['project']['algemeen']['postcode'];
        $data['city'] = $data['project']['algemeen']['plaats'];
        $data['description_site'] = $data['teksten']['eigenSiteTekst'];
        $data['description'] = $data['teksten']['aanbiedingstekst'];
        $data['title'] = $data['project']['algemeen']['omschrijving'];
        if (isset($data['media'])) {
            $data['media'] = $data['media'];
        } else {
            $data['media'] = [];
        }
        $data['diversen'] = $data['project']['diversen'];

        if (isset($data['project']['algemeen']['woonoppervlakteVanaf'])) {
            if(isset($data['project']['algemeen']['woonoppervlakteTot'])) {
                $data['woonoppervlakte'] = $data['project']['algemeen']['woonoppervlakteVanaf'] . ' tot ' . $data['project']['algemeen']['woonoppervlakteTot'];
            } else {
                $data['woonoppervlakte'] = $data['project']['algemeen']['woonoppervlakteVanaf'];
            }
        }

        if (isset($data['project']['algemeen']['perceeloppervlakteVanaf'])) {
            if(isset($data['project']['algemeen']['perceeloppervlakteTot'])) {
                $data['perceeloppervlakte'] = $data['project']['algemeen']['perceeloppervlakteVanaf'] . ' tot ' . $data['project']['algemeen']['perceeloppervlakteTot'];
            } else {
                $data['perceeloppervlakte'] = $data['project']['algemeen']['perceeloppervlakteVanaf'];
            }
        }

        $project = new Project();
        $project->setExternalId($data['externalId']);
        $project->setAlgemeen($data['algemeen']);
        $project->setArchived(false);
        $project->setStatus($data['status']);
        $project->setProvince($data['province']);
        $project->setZipcode($data['zipcode']);
        $project->setCity($data['city']);
        $project->setDescriptionSite($data['description_site']);
        $project->setDescription($data['description']);
        $project->setTitle($data['title']);

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
        $project->setLivingArea($data['woonoppervlakte']);
        $project->setPlot($data['perceeloppervlakte'] ?? null);

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

            if(isset($bouwType['algemeen']['woonhuistype']) || isset($bouwType['algemeen']['appartementsoort'])) {
                $type->setType(match($bouwType['algemeen']['woonhuistype'] ?? $bouwType['algemeen']['appartementsoort']) {
                    // woonhuizen
                    'VRIJSTAANDE_WONING' => 'Vrijstaande woning',
                    'GESCHAKELDE_WONING' => 'Geschakelde woning',
                    'TWEE_ONDER_EEN_KAPWONING' => '2-onder-1-kapwoning',
                    'TUSSENWONING' => 'Tussenwoning',
                    'HOEKWONING' => 'Hoekwoning',
                    'EINDWONING' => 'Eindwoning',
                    'HALFVRIJSTAANDE_WONING' => 'Halfvrijstaande woning',
                    'GESCHAKELDE_TWEE_ONDER_EEN_KAPWONING' => 'Geschakelde 2-onder-1-kapwoning',
                    'VERSPRINGEND' => 'Verspringend',
                    // Appartementen
                    'BOVENWONING' => 'Bovenwoning',
                    'BENEDENWONING' => 'Benedenwoning',
                    'MAISONNETTE' => 'Maisonnette',
                    'GALERIJFLAT' => 'Galerijflat',
                    'PORTIEKFLAT' => 'Portiekflat',
                    'BENEDEN_PLUS_BOVENWONING' => 'Beneden plus bovenwoning',
                    'PENTHOUSE' => 'Penthouse',
                    'PORTIEKWONING' => 'Portiekwoning',
                    'STUDENTENKAMER' => 'Studentenkamer',
                    'DUBBEL_BENEDENHUIS' => 'Dubbel benedenhuis',
                    'TUSSENVERDIEPING' => 'Tussenverdieping',
                });
            }

            if (isset($bouwType['algemeen']['woonoppervlakteVanaf'])) {
                if(isset($bouwType['algemeen']['woonoppervlakteTot'])) {
                    $type->setLivingArea($bouwType['algemeen']['woonoppervlakteVanaf'] . ' tot ' . $bouwType['algemeen']['woonoppervlakteTot']);
                } else {
                    $type->setLivingArea($bouwType['algemeen']['woonoppervlakteVanaf']);
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
                if (isset($number['teksten']['eigenSiteTekst'])) {
                    $constructionNumber->setDescription($number['teksten']['eigenSiteTekst']);
                }
                if (!isset($number['teksten']['eigenSiteTekst']) && isset($number['teksten']['aanbiedingstekst'])) {
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
                    new \Money\Money(($number['financieel']['overdracht']['koopprijs'] ?? $number['financieel']['overdracht']['huurprijs']) * 100, new \Money\Currency('EUR'))
                );
                if(isset($number['financieel']['overdracht']['koopconditie']) || isset($number['financieel']['overdracht']['huurconditie'])) {
                    $constructionNumber->setPriceCondition(match($number['financieel']['overdracht']['koopconditie'] ?? $number['financieel']['overdracht']['huurconditie']) {
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

            if($type->getRooms() > $highestNumberOfRooms) {
                $highestNumberOfRooms = $type->getRooms();
            }

            if($lowestNumberOfRooms === 0 || $type->getRooms() < $lowestNumberOfRooms) {
                $lowestNumberOfRooms = $type->getRooms();
            }

            $project->addConstructionType($type);
        }

        if($lowestNumberOfRooms !== $highestNumberOfRooms) {
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

        if(isset($data['construction_types']) && isset($data['construction_types']['hydra:member'])) {
            $data['construction_types'] = $data['construction_types']['hydra:member'];

            usort($data['construction_types'], function($a, $b) {
                $aAvailable = array_values(array_filter($a['construction_numbers'], function($item) {
                    return $item['status'] === Status::AVAILABLE->value;
                }))[0] ?? null;

                $bAvailable = array_values(array_filter($b['construction_numbers'], function($item) {
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

        if(isset($data['algemeen'])) {
            $data['algemeen'] = $project->getAlgemeen();
        }
        if(isset($data['diversen'])) {
            $data['diversen'] = $project->getDiversen();
        }
        if(isset($data['main_image'])) {
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
