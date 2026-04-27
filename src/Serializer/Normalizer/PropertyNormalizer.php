<?php

namespace App\Serializer\Normalizer;

use App\Entity\Property;
use App\Helpers\ArrayHelper;
use App\Helpers\KeyTranslationsHelper;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class PropertyNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function __construct(#[Autowire(service: 'app.object_normalizer')] private NormalizerInterface&DenormalizerInterface $objectNormalizer)
    {
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): Property
    {
        $property = new \App\Entity\Property();

        /** Address */
        $huisnummer = ArrayHelper::safeGet($data, 'adres.huisnummer.hoofdnummer');
        if ($huisnummer !== null && $huisnummer !== '') {
            $property->setHouseNumber((int) $huisnummer);
        }
        $huisnummertoevoeging = ArrayHelper::safeGet($data, 'adres.huisnummer.toevoeging');
        if ($huisnummertoevoeging) {
            $property->setHouseNumberAddition($huisnummertoevoeging);
        }
        $city = ArrayHelper::safeGet($data, 'adres.plaats');
        if ($city) {
            $property->setCity($city);
        }
        $zipcode = ArrayHelper::safeGet($data, 'adres.postcode');
        if ($zipcode) {
            $property->setZip($zipcode);
        }
        $street = ArrayHelper::safeGet($data, 'adres.straat');
        if ($street) {
            $property->setStreet($street);
        }

        $numberIsZero = $property->getHouseNumber() === null || $property->getHouseNumber() === 0;

        if ($numberIsZero) {
            $property->setHouseNumber(null);
            $property->setHouseNumberAddition(null);
            $property->setAddress("{$property->getStreet()}, {$property->getCity()}");
        } else {
            $property->setAddress("{$property->getStreet()} {$property->getHouseNumber()}{$property->getHouseNumberAddition()}, {$property->getCity()}");
        }

        /** Generic */
        $property->setCreatedAt(ArrayHelper::safeGetDate($data, 'marketing.publicatiedatum', new \DateTimeImmutable()));
        $property->setUpdatedAt(ArrayHelper::safeGetDate($data, 'tijdstipLaatsteWijziging', new \DateTimeImmutable()));
        $property->setExternalId(ArrayHelper::safeGet($data, 'id'));
        $property->setArchived(false);
        if ($numberIsZero) {
            $property->setTitle("{$property->getStreet()}, {$property->getCity()}");
        } else {
            $property->setTitle("{$property->getStreet()} {$property->getHouseNumber()}{$property->getHouseNumberAddition()}, {$property->getCity()}");
        }
        $property->setAlgemeen(ArrayHelper::safeGet($data, 'algemeen', []));
        $property->setFinancieel(ArrayHelper::safeGet($data, 'financieel', []));

        $eigenSiteTekst = ArrayHelper::safeGet($data, 'teksten.eigenSiteTekst');
        if ($eigenSiteTekst) {
            $property->setDescription($eigenSiteTekst);
        } else {
            $aanbiedingstekst = ArrayHelper::safeGet($data, 'teksten.aanbiedingstekst');
            if ($aanbiedingstekst) {
                $property->setDescription($aanbiedingstekst);
            }
        }

        $property->setTeksten(ArrayHelper::safeGet($data, 'teksten', []));

        $bouwjaar = ArrayHelper::safeGetNumeric($data, 'algemeen.bouwjaar', 0);
        if ($bouwjaar > 0) {
            $property->setBuildYear($bouwjaar);
        }
        $energieklasse = ArrayHelper::safeGet($data, 'algemeen.energieklasse');
        if ($energieklasse) {
            $property->setEnergyClass(KeyTranslationsHelper::energyClass($energieklasse));
        }

        /** Media */
        $mediaArray = ArrayHelper::safeGet($data, 'media', []);
        $mainImage = [];
        if (is_array($mediaArray) && ! empty($mediaArray)) {
            $mainImage = array_filter($mediaArray, function ($media) {
                return is_array($media) && ArrayHelper::safeGet($media, 'soort') === 'HOOFDFOTO';
            });
            $mainImage = array_values($mainImage);
        }

        if (isset($mainImage[0])) {
            $property->setImage($mainImage[0]);
        } else {
            $property->setImage($mediaArray[0] ?? null);
        }

        $property->setMedia($mediaArray);
        if (is_array($mediaArray)) {
            ArrayHelper::sort($mediaArray);
            $property->setMediaHash(md5(json_encode($mediaArray)));
        } else {
            $property->setMediaHash('');
        }
        $property->setEtages(ArrayHelper::safeGet($data, 'detail.etages', []));

        $etages = ArrayHelper::safeGet($data, 'detail.etages', []);

        $slaapkamers = 0;
        if (is_array($etages)) {
            $slaapkamers = array_reduce($etages, function ($carry, $item) {
                if (is_array($item)) {
                    return $carry + ArrayHelper::safeGetNumeric($item, 'aantalSlaapkamers', 0);
                }

                return $carry;
            }, 0);
        }
        $property->setBedrooms($slaapkamers);

        $totalRooms = 0;
        if (is_array($etages)) {
            foreach ($etages as $etage) {
                if (is_array($etage)) {
                    $totalRooms += ArrayHelper::safeGetNumeric($etage, 'aantalKamers', 0);
                }
            }
        }
        $property->setRooms($totalRooms);
        $property->setLivingArea(ArrayHelper::safeGet($data, 'algemeen.woonoppervlakte', ''));

        $property->setOverigOnroerendGoed(ArrayHelper::safeGet($data, 'detail.overigOnroerendGoed', []));
        $property->setBuitenruimte(ArrayHelper::safeGet($data, 'detail.buitenruimte', []));
        $totaleWoonkameroppervlakte = ArrayHelper::safeGet($data, 'algemeen.totaleWoonkameroppervlakte');
        $totaleKadestraleOppervlakte = ArrayHelper::safeGet($data, 'algemeen.totaleKadestraleOppervlakte');
        $property->setPlot($totaleWoonkameroppervlakte ?: $totaleKadestraleOppervlakte);

        /** Price */
        $koopconditie = ArrayHelper::safeGet($data, 'financieel.overdracht.koopconditie');
        $huurconditie = ArrayHelper::safeGet($data, 'financieel.overdracht.huurconditie');
        $condition = $koopconditie ?? $huurconditie;
        if ($condition) {
            $property->setPriceCondition(match ($condition) {
                // huur: PER_JAAR, PER_MAAND
                'PER_JAAR' => 'p.j.',
                'PER_MAAND' => 'p.m.',
                // Koop: KOSTEN_KOPER, VRIJ_OP_NAAM
                'KOSTEN_KOPER' => 'k.k.',
                'VRIJ_OP_NAAM' => 'v.o.n.',
                default => ''
            });
        }
        $koopprijs = ArrayHelper::safeGetNumeric($data, 'financieel.overdracht.koopprijs', 0);
        $huurprijs = ArrayHelper::safeGetNumeric($data, 'financieel.overdracht.huurprijs', 0);
        $property->setPrice($koopprijs ?: $huurprijs);

        $category = $koopprijs > 0 ? 'Koop' : 'Huur';
        $property->setCategory($category);
        $status = ArrayHelper::safeGet($data, 'financieel.overdracht.status', '');
        $property->setStatus($status);
        if ($status) {
            $property->setReadableStatus(KeyTranslationsHelper::status($status));
        }

        return $property;
    }

    /**
     * @param  Property  $project
     */
    public function normalize($project, ?string $format = null, array $context = []): array
    {
        $data = $this->objectNormalizer->normalize($project, $format, $context);

        if (isset($data['algemeen'])) {
            $data['algemeen'] = $project->getAlgemeen();
        }
        if (isset($data['financieel'])) {
            $data['financieel'] = $project->getFinancieel();
        }
        if (isset($data['teksten'])) {
            $data['teksten'] = $project->getTeksten();
        }
        if (isset($data['image'])) {
            $data['image'] = $project->getImage();
        }
        if (isset($data['media'])) {
            $data['media'] = $project->getMedia();
        }
        if (isset($data['etages'])) {
            $data['etages'] = $project->getEtages();
        }
        if (isset($data['buitenruimte'])) {
            $data['buitenruimte'] = $project->getBuitenruimte();
        }

        return $data;
    }

    public function supportsDenormalization($data, string $type, ?string $format = null, array $context = []): bool
    {
        return $type === Property::class;
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
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
