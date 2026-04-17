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
    public function __construct(#[Autowire(service: 'app.object_normalizer')] private NormalizerInterface&DenormalizerInterface $objectNormalizer) {}

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

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): BogObject
    {
        $property = new BogObject;

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
        if (isset($data['kenmerken']['hoofdfunctie'])) {
            $property->setMainFunction(KeyTranslationsHelper::mainFunction($data['kenmerken']['hoofdfunctie']));
        }
        $property->setCreatedAt(ArrayHelper::safeGetDate($data, 'marketing.publicatiedatum', new \DateTimeImmutable));
        $property->setUpdatedAt(ArrayHelper::safeGetDate($data, 'tijdstipLaatsteWijziging', new \DateTimeImmutable));
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

        if (isset($data['gebouwdetails']['bouwjaar']['bouwjaar1'])) {
            $bouwjaarFromLokatie = ArrayHelper::safeGetNumeric($data, 'gebouwdetails.bouwjaar.bouwjaar1', 0);
            if ($bouwjaarFromLokatie > 0) {
                $property->setBuildYear((int) $bouwjaarFromLokatie);
            }
        }
        if (isset($data['gebouwdetails']['energielabel']['energieklasse'])) {
            $property->setEnergyClass(KeyTranslationsHelper::energyClass($data['gebouwdetails']['energielabel']['energieklasse']));
        }

        foreach ($data['object']['functies'] as $key => $function) {
            if (! $function['actief']) {
                unset($data['object']['functies'][$key]);
            }
        }
        // make sure the array is reindexed
        $data['object']['functies'] = array_values($data['object']['functies']);

        if (isset($data['object']['functies'])) {
            $plot = 0;
            $facilities = [];

            foreach ($data['object']['functies'] as $function) {
                if (isset($function['bedrijfsruimte'])) {
                    $this->addFacilities($facilities, $function['bedrijfsruimte']['bedrijfshal'], 'bedrijfshalVoorzieningen');
                    $this->addFacilities($facilities, $function['bedrijfsruimte']['bedrijfsruimteKantoorruimte'], 'kantoorruimteVoorzieningen');
                    $this->addPlot($plot, $function['bedrijfsruimte']['bedrijfshal'], 'oppervlakte');
                    $this->addPlot($plot, $function['bedrijfsruimte']['bedrijfsruimteKantoorruimte'], 'kantoorruimteOppervlakte');
                    $property->setNumberOfFloors($function['bedrijfsruimte']['bedrijfsruimteKantoorruimte']['kantoorruimteAantalVerdiepingen']);
                } elseif (isset($function['leisure'])) {
                    $this->addFacilities($facilities, $function['leisure'], 'leisurevoorzieningen');
                    $this->addPlot($plot, $function['leisure'], 'oppervlakte');
                } elseif (isset($function['maatschappelijkvastgoed'])) {
                    foreach ($function['maatschappelijkvastgoed']['instellingen'] as $instelling) {
                        $this->addFacilities($facilities, $instelling, 'voorzieningen');
                        $this->addPlot($plot, $instelling, 'oppervlakte');
                    }
                } elseif (isset($function['kantoorruimte'])) {
                    $this->addFacilities($facilities, $function['kantoorruimte'], 'voorzieningen');
                    $this->addPlot($plot, $function['kantoorruimte'], 'oppervlakte');
                    $property->setNumberOfFloors($function['kantoorruimte']['aantalVerdiepingen']);
                } elseif (isset($function['overige'])) {
                    $this->addPlot($plot, $function['overige'], 'oppervlakte');
                    $property->setNumberOfFloors($function['overige']['aantalVerdiepingen']);
                } elseif (isset($function['belegging'])) {
                    $this->addPlot($plot, $function['belegging'], 'oppervlakte');
                } elseif (isset($function['winkelruimte'])) {
                    $this->addPlot($plot, $function['winkelruimte'], 'oppervlakte');
                    $property->setNumberOfFloors($function['winkelruimte']['aantalVerdiepingen']);
                }
            }

            if ($plot == 0 && $data['diversen']['kadaster'] !== null && isset($data['diversen']['kadaster'][0]['kadastergegevens'])) {
                foreach ($data['diversen']['kadaster'] as $kadaster) {
                    $this->addPlot($plot, $kadaster['kadastergegevens'], 'oppervlakte');
                }
            }

            $property->setPlot($plot);
            $facilities = array_unique($facilities);
        }

        $property->setFunctions($data['object']['functies']);

        if (isset($facilities)) {
            $property->setFacilities(KeyTranslationsHelper::facilities($facilities));
        }

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
        $data['media'] = ArrayHelper::remapMediaLinks($data['media']);
        $mainImage = array_filter($data['media'], function ($media) {
            return $media['soort'] === 'HOOFDFOTO';
        });

        // get first item in $mainImage array
        $mainImage = array_values($mainImage);

        if (isset($mainImage[0])) {
            $property->setImage($mainImage[0]);
        } else {
            if ($data['media'] !== null && count($data['media']) > 0) {
                $property->setImage($data['media'][0]);
            }
        }

        $property->setMedia($data['media']);
        ArrayHelper::sort($data['media']);
        $property->setMediaHash(md5(json_encode($data['media'])));

        /** Price */
        $condition = $data['financieel']['overdracht']['koopEnOfHuur']['koopconditie'] ?? $data['financieel']['overdracht']['koopEnOfHuur']['huurconditie'];
        if (isset($condition)) {
            $property->setPriceCondition(match ($condition) {
                // huur: PER_JAAR, PER_MAAND, PER_VIERKANTE_METERS_PER_JAAR
                'PER_JAAR' => 'p.j.',
                'PER_MAAND' => 'p.m.',
                'PER_VIERKANTE_METERS_PER_JAAR' => 'p.j. per m²',
                // Koop: KOSTEN_KOPER, VRIJ_OP_NAAM
                'KOSTEN_KOPER' => 'k.k.',
                'VRIJ_OP_NAAM' => 'v.o.n.',
            });
        }

        $serviceCondition = $data['financieel']['overdracht']['koopEnOfHuur']['servicekostenconditie'] ?: null;
        if (isset($serviceCondition)) {
            $property->setServiceCostCondition(match ($serviceCondition) {
                // huur: PER_JAAR, PER_MAAND, PER_VIERKANTE_METERS_PER_JAAR
                'PER_JAAR' => 'p.j.',
                'PER_MAAND' => 'p.m.',
                'PER_VIERKANTE_METERS_PER_JAAR' => 'p.j. per m²',
            });
        }

        $property->setCategory($data['financieel']['overdracht']['koopEnOfHuur']['koopprijs'] ? 'Koop' : 'Huur');
        $property->setPrice($data['financieel']['overdracht']['koopEnOfHuur']['koopprijs'] ?: $data['financieel']['overdracht']['koopEnOfHuur']['huurprijs']);
        $property->setServiceCostPrice($data['financieel']['overdracht']['koopEnOfHuur']['servicekosten'] ?: null);
        $property->setServiceCostVAT($data['financieel']['overdracht']['koopEnOfHuur']['servicekostenBtwBelast'] ?: null);
        $property->setStatus($data['status']);
        $property->setReadableStatus(KeyTranslationsHelper::status($data['status']));

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

    public function supportsDenormalization($data, string $type, ?string $format = null): bool
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
}
