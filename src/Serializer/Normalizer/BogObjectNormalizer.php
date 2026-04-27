<?php

namespace App\Serializer\Normalizer;

use App\Entity\BogObject;
use App\Helpers\ArrayHelper;
use App\Helpers\KeyTranslationsHelper;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class BogObjectNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function __construct(#[Autowire(service: 'app.object_normalizer')] private NormalizerInterface&DenormalizerInterface $objectNormalizer)
    {
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): BogObject
    {
        $property = new BogObject();

        /** Address */
        $huisnummer = ArrayHelper::safeGet($data, 'adres.huisnummer.hoofdnummer');
        if ($huisnummer) {
            $huisnummertoevoeging = ArrayHelper::safeGet($data, 'adres.huisnummer.toevoeging');
            if ($huisnummertoevoeging) {
                $property->setHouseNumber($huisnummer.$huisnummertoevoeging);
            } else {
                $property->setHouseNumber($huisnummer);
            }
        }
        $city = ArrayHelper::safeGet($data, 'adres.plaats');
        if ($city) {
            $property->setCity($city);
        }
        $zipcode = ArrayHelper::safeGet($data, 'adres.postcode');
        if ($zipcode) {
            $property->setZipCode($zipcode);
        }
        $street = ArrayHelper::safeGet($data, 'adres.straat');
        if ($street) {
            $property->setStreet($street);
        }
        $country = ArrayHelper::safeGet($data, 'adres.land');
        if ($country) {
            $property->setCountry($country);
        }

        $numberIsZero = $huisnummer === null || (($huisnummer === '0' || $huisnummer === 0) && $property->getHouseNumber() !== null);

        /** Generic */
        if ($numberIsZero) {
            $property->setHouseNumber(null);
            $property->setTitle("{$property->getStreet()}, {$property->getCity()}");
        } else {
            $property->setTitle("{$property->getStreet()} {$property->getHouseNumber()}, {$property->getCity()}");
        }
        $hoofdfunctie = ArrayHelper::safeGet($data, 'kenmerken.hoofdfunctie');
        if ($hoofdfunctie) {
            $property->setMainFunction(KeyTranslationsHelper::mainFunction($hoofdfunctie));
        }
        $property->setCreatedAt(ArrayHelper::safeGetDate($data, 'marketing.publicatiedatum', new \DateTimeImmutable()));
        $property->setUpdatedAt(ArrayHelper::safeGetDate($data, 'tijdstipLaatsteWijziging', new \DateTimeImmutable()));
        $property->setExternalId(ArrayHelper::safeGet($data, 'id'));
        $property->setArchived(false);
        $property->setFinance(ArrayHelper::safeGet($data, 'financieel', []));
        $property->setDiversen(ArrayHelper::safeGet($data, 'diversen.diversen', []));
        $property->setKadaster(ArrayHelper::safeGet($data, 'diversen.kadaster', []));

        $eigenSiteTekst = ArrayHelper::safeGet($data, 'teksten.eigenSiteTekst');
        if ($eigenSiteTekst && ! empty($eigenSiteTekst)) {
            $property->setDescription($eigenSiteTekst);
        } else {
            $aanbiedingstekst = ArrayHelper::safeGet($data, 'teksten.aanbiedingstekst');
            if ($aanbiedingstekst) {
                $property->setDescription($aanbiedingstekst);
            }
        }

        $bouwjaarFromLokatie = ArrayHelper::safeGetNumeric($data, 'gebouwdetails.bouwjaar.bouwjaar1', 0);
        if ($bouwjaarFromLokatie > 0) {
            $property->setBuildYear((int) $bouwjaarFromLokatie);
        }
        $energieklasse = ArrayHelper::safeGet($data, 'gebouwdetails.energielabel.energieklasse');
        if ($energieklasse) {
            $property->setEnergyClass(KeyTranslationsHelper::energyClass($energieklasse));
        }

        $functies = ArrayHelper::safeGet($data, 'object.functies', []);
        if (! is_array($functies)) {
            $functies = [];
        }
        $functies = array_values(array_filter($functies, fn ($function) => is_array($function) && ! empty($function['actief'])));

        $plot = 0;
        $facilities = [];

        foreach ($functies as $function) {
            if (isset($function['bedrijfsruimte'])) {
                $bedrijfshal = ArrayHelper::safeGet($function, 'bedrijfsruimte.bedrijfshal');
                $kantoor = ArrayHelper::safeGet($function, 'bedrijfsruimte.bedrijfsruimteKantoorruimte');
                $this->addFacilities($facilities, $bedrijfshal, 'bedrijfshalVoorzieningen');
                $this->addFacilities($facilities, $kantoor, 'kantoorruimteVoorzieningen');
                $this->addPlot($plot, $bedrijfshal, 'oppervlakte');
                $this->addPlot($plot, $kantoor, 'kantoorruimteOppervlakte');
                if (isset($kantoor['kantoorruimteAantalVerdiepingen'])) {
                    $property->setNumberOfFloors($kantoor['kantoorruimteAantalVerdiepingen']);
                }
            } elseif (isset($function['leisure'])) {
                $this->addFacilities($facilities, $function['leisure'], 'leisurevoorzieningen');
                $this->addPlot($plot, $function['leisure'], 'oppervlakte');
            } elseif (isset($function['maatschappelijkvastgoed'])) {
                $instellingen = ArrayHelper::safeGet($function, 'maatschappelijkvastgoed.instellingen', []);
                if (is_array($instellingen)) {
                    foreach ($instellingen as $instelling) {
                        $this->addFacilities($facilities, $instelling, 'voorzieningen');
                        $this->addPlot($plot, $instelling, 'oppervlakte');
                    }
                }
            } elseif (isset($function['kantoorruimte'])) {
                $this->addFacilities($facilities, $function['kantoorruimte'], 'voorzieningen');
                $this->addPlot($plot, $function['kantoorruimte'], 'oppervlakte');
                if (isset($function['kantoorruimte']['aantalVerdiepingen'])) {
                    $property->setNumberOfFloors($function['kantoorruimte']['aantalVerdiepingen']);
                }
            } elseif (isset($function['overige'])) {
                $this->addPlot($plot, $function['overige'], 'oppervlakte');
                if (isset($function['overige']['aantalVerdiepingen'])) {
                    $property->setNumberOfFloors($function['overige']['aantalVerdiepingen']);
                }
            } elseif (isset($function['belegging'])) {
                $this->addPlot($plot, $function['belegging'], 'oppervlakte');
            } elseif (isset($function['winkelruimte'])) {
                $this->addPlot($plot, $function['winkelruimte'], 'oppervlakte');
                if (isset($function['winkelruimte']['aantalVerdiepingen'])) {
                    $property->setNumberOfFloors($function['winkelruimte']['aantalVerdiepingen']);
                }
            }
        }

        if ($plot == 0) {
            $kadasterList = ArrayHelper::safeGet($data, 'diversen.kadaster', []);
            if (is_array($kadasterList)) {
                foreach ($kadasterList as $kadaster) {
                    $this->addPlot($plot, ArrayHelper::safeGet($kadaster, 'kadastergegevens'), 'oppervlakte');
                }
            }
        }

        $property->setPlot($plot);
        $property->setFunctions($functies);
        $property->setFacilities(KeyTranslationsHelper::facilities(array_unique($facilities)));

        if (isset($data['gebouwdetails']['lokatie'])) {

            if (isset($data['gebouwdetails']['lokatie']['parkeren'])) {
                $property->setParking($data['gebouwdetails']['lokatie']['parkeren']);
            }

            if (isset($data['gebouwdetails']['lokatie']['bereikbaarheid'])) {
                $accessibility = [];
                $localAmentities = [];
                $accessibilityKeyMapping = [
                    'bereikbaarheidBushalte' => 'Bushalte',
                    'bereikbaarheidMetrohalte' => 'Metrohalte',
                    'bereikbaarheidNsStation' => 'NS Station',
                    'bereikbaarheidSnelwegafrit' => 'Snelwegafrit',
                    'bereikbaarheidBusknooppunt' => 'Busknooppunt',
                    'bereikbaarheidMetroknooppunt' => 'Metroknooppunt',
                    'bereikbaarheidTramhalte' => 'Tramhalte',
                    'bereikbaarheidTramknooppunt' => 'Tramknooppunt',
                ];

                $amentitiesKeyMapping = [
                    'voorzieningRestaurantAfstand' => 'Restaurant',
                    'voorzieningWinkelAfstand' => 'Winkel',
                    'voorzieningBankafstand' => 'Bank',
                ];

                foreach ($accessibilityKeyMapping as $key => $label) {
                    if (isset($data['gebouwdetails']['lokatie']['bereikbaarheid'][$key])) {
                        $distance = $data['gebouwdetails']['lokatie']['bereikbaarheid'][$key];
                        $distanceString = KeyTranslationsHelper::distance($distance);
                        $accessibility[] = "{$label} op {$distanceString}";
                    }
                }

                foreach ($amentitiesKeyMapping as $key => $label) {
                    if (isset($data['gebouwdetails']['lokatie']['bereikbaarheid'][$key])) {
                        $distance = $data['gebouwdetails']['lokatie']['bereikbaarheid'][$key];
                        $distanceString = KeyTranslationsHelper::distance($distance);
                        $localAmentities[] = "{$label} op {$distanceString}";
                    }
                }

                if (count($accessibility) > 1) {
                    $lastItem = array_pop($accessibility);
                    $property->setAccessibility(implode(', ', $accessibility).' en '.$lastItem);
                } else {
                    $property->setAccessibility(implode('', $accessibility));
                }

                if (count($localAmentities) > 1) {
                    $lastItem = array_pop($localAmentities);
                    $property->setLocalAmentities(implode(', ', $localAmentities).' en '.$lastItem);
                } else {
                    $property->setLocalAmentities(implode('', $localAmentities));
                }
            }

            $bouwjaarFromLokatie = ArrayHelper::safeGetNumeric($data, 'gebouwdetails.bouwjaar.bouwjaar1', 0);
            if ($bouwjaarFromLokatie > 0) {
                $property->setBuildYear((int) $bouwjaarFromLokatie);
            }
        }

        /** Media */
        $mediaArray = ArrayHelper::safeGet($data, 'media', []);
        if (! is_array($mediaArray)) {
            $mediaArray = [];
        }
        $mediaArray = ArrayHelper::remapMediaLinks($mediaArray);
        $mainImage = array_values(array_filter($mediaArray, function ($media) {
            return is_array($media) && ($media['soort'] ?? null) === 'HOOFDFOTO';
        }));

        if (isset($mainImage[0])) {
            $property->setImage($mainImage[0]);
        } elseif (! empty($mediaArray)) {
            $property->setImage($mediaArray[0]);
        }

        $property->setMedia($mediaArray);
        ArrayHelper::sort($mediaArray);
        $property->setMediaHash(md5(json_encode($mediaArray)));

        /** Price */
        $koopconditie = ArrayHelper::safeGet($data, 'financieel.overdracht.koopEnOfHuur.koopconditie');
        $huurconditie = ArrayHelper::safeGet($data, 'financieel.overdracht.koopEnOfHuur.huurconditie');
        $condition = $koopconditie ?? $huurconditie;
        if ($condition) {
            $property->setPriceCondition(match ($condition) {
                // huur: PER_JAAR, PER_MAAND, PER_VIERKANTE_METERS_PER_JAAR
                'PER_JAAR' => 'p.j.',
                'PER_MAAND' => 'p.m.',
                'PER_VIERKANTE_METERS_PER_JAAR' => 'p.j. per m²',
                // Koop: KOSTEN_KOPER, VRIJ_OP_NAAM
                'KOSTEN_KOPER' => 'k.k.',
                'VRIJ_OP_NAAM' => 'v.o.n.',
                default => '',
            });
        }

        $serviceCondition = ArrayHelper::safeGet($data, 'financieel.overdracht.koopEnOfHuur.servicekostenconditie');
        if ($serviceCondition) {
            $property->setServiceCostCondition(match ($serviceCondition) {
                'PER_JAAR' => 'p.j.',
                'PER_MAAND' => 'p.m.',
                'PER_VIERKANTE_METERS_PER_JAAR' => 'p.j. per m²',
                default => '',
            });
        }

        $koopprijs = ArrayHelper::safeGetNumeric($data, 'financieel.overdracht.koopEnOfHuur.koopprijs', 0);
        $huurprijs = ArrayHelper::safeGetNumeric($data, 'financieel.overdracht.koopEnOfHuur.huurprijs', 0);
        $property->setCategory($koopprijs ? 'Koop' : 'Huur');
        $property->setPrice($koopprijs ?: $huurprijs);

        $servicekosten = ArrayHelper::safeGetNumeric($data, 'financieel.overdracht.koopEnOfHuur.servicekosten', 0);
        $property->setServiceCostPrice($servicekosten ?: null);
        $property->setServiceCostVAT(ArrayHelper::safeGet($data, 'financieel.overdracht.koopEnOfHuur.servicekostenBtwBelast') ?: null);

        $status = ArrayHelper::safeGet($data, 'status', '');
        $property->setStatus($status);
        if ($status) {
            $property->setReadableStatus(KeyTranslationsHelper::status($status));
        }

        return $property;
    }

    /**
     * @param  BogObject  $project
     */
    public function normalize($project, ?string $format = null, array $context = []): array
    {
        $data = $this->objectNormalizer->normalize($project, $format, $context);

        if (isset($data['diversen'])) {
            $data['diversen'] = $project->getDiversen();
        }
        if (isset($data['kadaster'])) {
            $data['kadaster'] = $project->getKadaster();
        }
        if (isset($data['image'])) {
            $data['image'] = $project->getImage();
        }
        if (isset($data['media'])) {
            $data['media'] = $project->getMedia();
        }
        if (isset($data['finance'])) {
            $data['finance'] = $project->getFinance();
        }
        if (isset($data['functions'])) {
            $data['functions'] = $project->getFunctions();
        }
        if (isset($data['parking'])) {
            $data['parking'] = $project->getParking();
        }

        return $data;
    }

    public function supportsDenormalization($data, string $type, ?string $format = null, array $context = []): bool
    {
        return $type === BogObject::class;
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof BogObject;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            \App\Entity\BogObject::class => true,
            'App\Entity\BogObject[]' => true,
        ];
    }

    private function addFacilities(&$facilities, $source, $key): void
    {
        if (isset($source[$key])) {
            $facilities = array_merge($facilities, $source[$key]);
        }
    }

    private function addPlot(&$plot, $source, $key): void
    {
        if (isset($source[$key])) {
            $plot += $source[$key];
        }
    }
}
