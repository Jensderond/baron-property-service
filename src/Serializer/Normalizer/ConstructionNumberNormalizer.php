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

        if(!in_array('read', $context['groups'])) {
            $data['algemeen'] = $number->getAlgemeen();
            $data['diversen'] = $number->getDiversen();
            $data['address'] = $number->getAddress();
            $data['detail'] = $number->getDetail();
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
