<?php

namespace App\Serializer\Normalizer;

use App\Helpers\ArrayHelper;
use App\Entity\BogObject;
use App\Helpers\KeyTranslationsHelper;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class BogObjectNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function __construct(#[Autowire(service: 'app.object_normalizer')] private NormalizerInterface&DenormalizerInterface $objectNormalizer)
    {
    }


    private function addFacilities(&$facilities, $source, $key)
    {
        if (isset($source[$key])) {
            $facilities = array_merge($facilities, $source[$key]);
        }
    }

    private function addPlot(&$plot, $source, $key)
    {
        if (isset($source[$key])) {
            $plot += $source[$key];
        }
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = [])
    {
        $property = new BogObject();

        /** Address */
        if (isset($data['huisnummer'])) {
            if (isset($data['huisnummertoevoeging'])) {
                $property->setHouseNumber($data['huisnummer'] . $data['huisnummertoevoeging']);
            } else {
                $property->setHouseNumber($data['huisnummer']);
            }
        }
        if (isset($data['plaats'])) {
            $property->setCity($data['plaats']);
        }
        if (isset($data['postcode'])) {
            $property->setZipCode($data['postcode']);
        }
        if (isset($data['straat'])) {
            $property->setStreet($data['straat']);
        }
        if (isset($data['land'])) {
            $property->setCountry($data['land']);
        }

        $numberIsZero = ($data['huisnummer'] === "0" || $data['huisnummer'] === 0) && null !== $property->getHouseNumber();

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
        $property->setCreatedAt(new \DateTimeImmutable($data['marketing']['publicatiedatum']));
        $property->setUpdatedAt(new \DateTimeImmutable($data['tijdstipLaatsteWijziging']));
        $property->setExternalId($data['id']);
        $property->setArchived(false);
        $property->setFinance($data['financieel']);
        $property->setDiversen($data['diversen']['diversen']);
        $property->setKadaster($data['diversen']['kadaster']);

        if (isset($data['teksten']['eigenSiteTekst']) && !empty($data['teksten']['eigenSiteTekst'])) {
            $property->setDescription($data['teksten']['eigenSiteTekst']);
        } elseif (isset($data['teksten']['aanbiedingstekst'])) {
            $property->setDescription($data['teksten']['aanbiedingstekst']);
        }

        if (isset($data['gebouwdetails']['bouwjaar']['bouwjaar1'])) {
            $property->setBuildYear((int)$data['gebouwdetails']['bouwjaar']['bouwjaar1']);
        }
        if (isset($data['gebouwdetails']['energielabel']['energieklasse'])) {
            $property->setEnergyClass(KeyTranslationsHelper::energyClass($data['gebouwdetails']['energielabel']['energieklasse']));
        }


        if (isset($data['object']['functies'])) {
            $plot = 0;
            $facilities = [];

            foreach ($data['object']['functies'] as $function) {
                if (isset($function['bedrijfsruimte'])) {
                    $this->addFacilities($facilities, $function['bedrijfsruimte']['bedrijfshal'], 'bedrijfshalVoorzieningen');
                    $this->addFacilities($facilities, $function['bedrijfsruimte']['bedrijfsruimteKantoorruimte'], 'kantoorruimteVoorzieningen');
                    $this->addPlot($plot, $function['bedrijfsruimte']['bedrijfshal'], 'oppervlakte');
                    $this->addPlot($plot, $function['bedrijfsruimte']['bedrijfsruimteKantoorruimte'], 'kantoorruimteOppervlakte');
                    $this->addPlot($plot, $function['bedrijfsruimte']['terrein'], 'terreinOppervlakte');
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

            $property->setPlot($plot);
            $facilities = array_unique($facilities);
        }

        foreach ($data['object']['functies'] as $key => $function) {
            if (!$function['actief']) {
                unset($data['object']['functies'][$key]);
            }
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
                    $property->setAccessibility(implode(', ', $accessibility) . ' en ' . $lastItem);
                } else {
                    $property->setAccessibility(implode('', $accessibility));
                }

                if (count($localAmentities) > 1) {
                    $lastItem = array_pop($localAmentities);
                    $property->setLocalAmentities(implode(', ', $localAmentities) . ' en ' . $lastItem);
                } else {
                    $property->setLocalAmentities(implode('', $localAmentities));
                }
            }

            $property->setBuildYear((int)$data['gebouwdetails']['bouwjaar']['bouwjaar1']);
        }

        /** Media */
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
     * @param BogObject $project
     */
    public function normalize($project, ?string $format = null, array $context = [])
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

    public function supportsNormalization($data, string $format = null, array $context = []): bool
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
