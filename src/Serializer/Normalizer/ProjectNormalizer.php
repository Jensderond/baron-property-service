<?php

namespace App\Serializer\Normalizer;

use App\Entity\ConstructionNumber;
use App\Entity\ConstructionType;
use App\Entity\Project;
use App\Helpers\ArrayHelper;
use App\Helpers\KeyTranslationsHelper;
use App\Model\Status;
use DateTimeImmutable;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ProjectNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function __construct(#[Autowire(service: 'app.object_normalizer')] private NormalizerInterface&DenormalizerInterface $objectNormalizer)
    {
    }

    public function supportsDenormalization($data, $type, $format = null, array $context = []): bool
    {
        return $type === Project::class; // Adjust the namespace accordingly
    }

    public function supportsNormalization($data, $format = null, array $context = []): bool
    {
        return $data instanceof Project; // Adjust the namespace accordingly
    }

    public function denormalize($data, $type, $format = null, array $context = []): mixed
    {
        $data['externalId'] = ArrayHelper::safeGet($data, 'project.id');
        $data['algemeen'] = ArrayHelper::safeGet($data, 'project.algemeen', []);
        $data['province'] = ArrayHelper::safeGet($data, 'project.algemeen.provincie', '');
        $data['zipcode'] = ArrayHelper::safeGet($data, 'project.algemeen.postcode', '');
        $data['city'] = ArrayHelper::safeGet($data, 'project.algemeen.plaats', '');
        $data['description'] = ArrayHelper::safeGet($data, 'teksten.aanbiedingstekst', '');
        $data['title'] = ArrayHelper::safeGet($data, 'project.algemeen.omschrijving', '');
        $data['media'] = ArrayHelper::safeGet($data, 'media', []);
        $data['diversen'] = ArrayHelper::safeGet($data, 'project.diversen', []);

        $livingAreaFrom = ArrayHelper::safeGet($data, 'project.algemeen.woonoppervlakteVanaf');
        $livingAreaTo = ArrayHelper::safeGet($data, 'project.algemeen.woonoppervlakteTot');
        $livingAreaCombined = ArrayHelper::combineAreaValues($livingAreaFrom, $livingAreaTo);

        $plotAreaFrom = ArrayHelper::safeGet($data, 'project.algemeen.perceeloppervlakteVanaf');
        $plotAreaTo = ArrayHelper::safeGet($data, 'project.algemeen.perceeloppervlakteTot');
        $plotAreaCombined = ArrayHelper::combineAreaValues($plotAreaFrom, $plotAreaTo);

        /**
         * Op de aanbodpagina (bij nieuwbouw) mag er dan zo'n zelfde balkje komen als verhuurd/verkocht.
         * Wanneer de start verkoop nog niet gestart is, mag er inschrijving gestart vermeld worden.
         * Wanneer de start verkoop al wel is gestart, mag er start verkoop vermeld worden.
         * Wanneer de start verkoop + bouw wel gestart is, mag er start bouw vermeld worden.
         * Wanneer de opleveringen gestart zijn, mag er opleveringen gestart vermeld worden.
         * Wanneer ik hem op verkocht zet (het totale project, alle bouwnummers), mag dit balkje net als bij de rest verkocht vermeld worden.
         */
        $dateStartBuilding = ArrayHelper::safeGetDate($data, 'project.algemeen.datumStartBouw');
        $dateEndBuilding = ArrayHelper::safeGetDate($data, 'project.algemeen.opleveringsdatum');
        $dateStartSelling = ArrayHelper::safeGetDate($data, 'project.algemeen.datumStartVerkoop');

        $dateNow = new DateTimeImmutable();
        $status = '';
        if ($dateStartSelling && $dateNow < $dateStartSelling) {
            $status = 'Inschrijving gestart';
        } elseif ($dateStartBuilding === null || $dateNow < $dateStartBuilding) {
            $status = 'Verkoop gestart';
        } elseif ($dateEndBuilding === null || $dateNow < $dateEndBuilding) {
            $status = 'Bouw gestart';
        } elseif ($dateEndBuilding && $dateNow > $dateEndBuilding) {
            $status = 'Oplevering gestart';
        }
        if (ArrayHelper::safeGet($data, 'project.algemeen.status') === 'VERKOCHT') {
            $status = 'Verkocht';
        }

        $project = new Project();
        $project->setExternalId($data['externalId']);
        $project->setAlgemeen($data['algemeen']);
        $project->setArchived(false);
        $project->setStatus(ArrayHelper::safeGet($data['algemeen'], 'status', ''));
        $project->setReadableStatus($status);
        $project->setProvince($data['province']);
        $project->setZipcode($data['zipcode']);
        $project->setCity($data['city']);
        $project->setDescription($data['description']);
        if ($data['city']) {
            $project->setTitle($data['title'].', '.$data['city']);
        } else {
            $project->setTitle($data['title']);
        }
        $koopOfHuur = ArrayHelper::safeGet($data['algemeen'], 'koopOfHuur', '');
        if ($koopOfHuur) {
            $project->setCategory(KeyTranslationsHelper::projectCategory($koopOfHuur));
        }

        /** Media */
        $mediaArray = $data['media'];
        $mainImage = [];
        if (is_array($mediaArray) && ! empty($mediaArray)) {
            $mainImage = array_filter($mediaArray, function ($media) {
                return is_array($media) && ($media['soort'] ?? '') === 'HOOFDFOTO';
            });
            $mainImage = array_values($mainImage);
        }

        if (isset($mainImage[0])) {
            $project->setMainImage([$mainImage[0]]);
        } else {
            $project->setMainImage($mediaArray[0] ?? null);
        }

        $project->setMedia($mediaArray);
        if (is_array($mediaArray)) {
            ArrayHelper::sort($mediaArray);
            $project->setMediaHash(md5(json_encode($mediaArray)));
        } else {
            $project->setMediaHash('');
        }
        $project->setDiversen($data['diversen']);
        $project->setCreatedAt(ArrayHelper::safeGetDate($data, 'marketing.publicatiedatum', new DateTimeImmutable()));
        $project->setUpdatedAt(ArrayHelper::safeGetDate($data, 'tijdstipLaatsteWijziging', new DateTimeImmutable()));
        $project->setLivingArea($livingAreaCombined);
        $project->setPlot($plotAreaCombined);

        $lowestNumberOfRooms = 0;
        $highestNumberOfRooms = 0;

        $bouwtypen = ArrayHelper::safeGet($data, 'bouwtypen', []);
        if (! is_array($bouwtypen)) {
            $bouwtypen = [];
        }

        foreach ($bouwtypen as $bouwType) {
            if (! is_array($bouwType)) {
                continue;
            }
            $type = new ConstructionType();
            $type->setExternalId(ArrayHelper::safeGet($bouwType, 'id'));
            $type->setTitle(ArrayHelper::safeGet($bouwType, 'algemeen.omschrijving', ''));
            $type->setMedia(ArrayHelper::safeGet($bouwType, 'media', []));
            $type->setAlgemeen(ArrayHelper::safeGet($bouwType, 'algemeen', []));
            $type->setTeksten(ArrayHelper::safeGet($bouwType, 'teksten', []));

            $totalRooms = 0;
            $etages = ArrayHelper::safeGet($bouwType, 'detail.etages', []);
            if (is_array($etages)) {
                foreach ($etages as $etage) {
                    if (is_array($etage)) {
                        $totalRooms += ArrayHelper::safeGetNumeric($etage, 'aantalKamers', 0);
                    }
                }
            }
            $type->setRooms($totalRooms);

            $woonhuistype = ArrayHelper::safeGet($bouwType, 'algemeen.woonhuistype');
            $appartementsoort = ArrayHelper::safeGet($bouwType, 'algemeen.appartementsoort');
            if ($woonhuistype || $appartementsoort) {
                $type->setType(KeyTranslationsHelper::houseType($woonhuistype ?? $appartementsoort));
            }

            $typeLivingAreaFrom = ArrayHelper::safeGet($bouwType, 'algemeen.woonoppervlakteVanaf');
            $typeLivingAreaTo = ArrayHelper::safeGet($bouwType, 'algemeen.woonoppervlakteTot');
            $typeLivingArea = ArrayHelper::combineAreaValues($typeLivingAreaFrom, $typeLivingAreaTo);
            if ($typeLivingArea) {
                $type->setLivingArea($typeLivingArea);
            }

            $bouwnummers = ArrayHelper::safeGet($bouwType, 'bouwnummers', []);
            if (is_array($bouwnummers)) {
                foreach ($bouwnummers as $number) {
                    if (! is_array($number)) {
                        continue;
                    }

                    $constructionNumber = new ConstructionNumber();
                    $constructionNumber->setExternalId(ArrayHelper::safeGet($number, 'id'));
                    $constructionNumber->setTitle(ArrayHelper::safeGet($number, 'adres.straat', ''));
                    $constructionNumber->setAddress(ArrayHelper::safeGet($number, 'adres', []));
                    $constructionNumber->setAlgemeen(ArrayHelper::safeGet($number, 'algemeen', []));
                    $constructionNumber->setFinancieel(ArrayHelper::safeGet($number, 'financieel', []));

                    $status = ArrayHelper::safeGet($number, 'financieel.overdracht.status', '');
                    $constructionNumber->setStatus($status);
                    if ($status) {
                        $constructionNumber->setReadableStatus(KeyTranslationsHelper::status($status));
                    }

                    $energyClass = ArrayHelper::safeGet($number, 'algemeen.energieklasse');
                    if ($energyClass) {
                        $constructionNumber->setEnergyClass(KeyTranslationsHelper::energyClass($energyClass));
                    }

                    $description = ArrayHelper::safeGet($number, 'teksten.aanbiedingstekst');
                    if ($description) {
                        $constructionNumber->setDescription($description);
                    }

                    $constructionNumber->setTeksten(ArrayHelper::safeGet($number, 'teksten', []));
                    $constructionNumber->setDiversen(ArrayHelper::safeGet($number, 'diversen', []));
                    $constructionNumber->setDetail(ArrayHelper::safeGet($number, 'detail', []));

                    $numberMedia = ArrayHelper::safeGet($number, 'media', []);
                    if (is_array($numberMedia)) {
                        ArrayHelper::sort($numberMedia);
                        $constructionNumber->setMedia($numberMedia);
                        $constructionNumber->setMediaHash(md5(json_encode($numberMedia)));
                    } else {
                        $constructionNumber->setMedia([]);
                        $constructionNumber->setMediaHash('');
                    }

                    $constructionNumber->setUpdatedAt(ArrayHelper::safeGetDate($number, 'diversen.diversen.wijzigingsdatum', new DateTimeImmutable()));

                    $totalCNRooms = 0;
                    $totalCNBedrooms = 0;

                    $numberEtages = ArrayHelper::safeGet($number, 'detail.etages', []);
                    if (is_array($numberEtages)) {
                        foreach ($numberEtages as $etage) {
                            if (is_array($etage)) {
                                $totalCNRooms += ArrayHelper::safeGetNumeric($etage, 'aantalKamers', 0);
                                $totalCNBedrooms += ArrayHelper::safeGetNumeric($etage, 'aantalSlaapkamers', 0);
                            }
                        }
                    }

                    $koopprijs = ArrayHelper::safeGetNumeric($number, 'financieel.overdracht.koopprijs', 0);
                    $huurprijs = ArrayHelper::safeGetNumeric($number, 'financieel.overdracht.huurprijs', 0);
                    $price = $koopprijs ?: $huurprijs;
                    $constructionNumber->setPrice(
                        new \Money\Money($price * 100, new \Money\Currency('EUR'))
                    );

                    $koopconditie = ArrayHelper::safeGet($number, 'financieel.overdracht.koopconditie');
                    $huurconditie = ArrayHelper::safeGet($number, 'financieel.overdracht.huurconditie');
                    $conditie = $koopconditie ?? $huurconditie;
                    if ($conditie) {
                        $constructionNumber->setPriceCondition(match ($conditie) {
                            'PER_JAAR' => 'p.j.',
                            'PER_MAAND' => 'p.m.',
                            'KOSTEN_KOPER' => 'k.k.',
                            'VRIJ_OP_NAAM' => 'v.o.n.',
                            default => ''
                        });
                    }

                    $constructionNumber->setRooms($totalCNRooms);
                    $constructionNumber->setBedrooms($totalCNBedrooms);
                    $constructionNumber->setConstructionType($type);
                    $constructionNumber->setLivingArea(ArrayHelper::safeGet($number, 'algemeen.woonoppervlakte', ''));
                    $constructionNumber->createSlug();

                    $type->addConstructionNumber($constructionNumber);
                }
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
            $project->setRooms($lowestNumberOfRooms.' tot '.$highestNumberOfRooms);
        } else {
            $project->setRooms($lowestNumberOfRooms);
        }

        return $project;
    }

    /**
     * @param  Project  $project
     */
    public function normalize($project, ?string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
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
