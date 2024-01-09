<?php

namespace App\Serializer\Normalizer;

use App\Entity\ConstructionNumber;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ConstructionNumberNormalizer implements NormalizerInterface
{
    public function __construct(#[Autowire(service: 'app.object_normalizer')] private NormalizerInterface $objectNormalizer) {
    }

    public function supportsDenormalization($data, $type, $format = null, array $context = [])
    {
        return $type === ConstructionNumber::class; // Adjust the namespace accordingly
    }

    public function supportsNormalization($data, $format = null, array $context = [])
    {
        return $data instanceof ConstructionNumber; // Adjust the namespace accordingly
    }

    /**
     * @param ConstructionNumber $number
     */
    public function normalize($number, ?string $format = null, array $context = [])
    {
        $data = $this->objectNormalizer->normalize($number, $format, $context);

        if(isset($data['algemeen'])) {
            $data['algemeen'] = $number->getAlgemeen();
        }
        if(isset($data['diversen'])) {
            $data['diversen'] = $number->getDiversen();
        }
        if(isset($data['address'])){
            $data['address'] = $number->getAddress();
        }
        if(isset($data['financieel'])){
            $data['financieel'] = $number->getFinancieel();
        }
        if(isset($data['detail'])){
            $data['detail'] = $number->getDetail();
        }
        if(isset($data['teksten'])){
            $data['teksten'] = $number->getTeksten();
        }

        return $data;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            '*' => true,
            ConstructionNumber::class => true,
        ];
    }
}
