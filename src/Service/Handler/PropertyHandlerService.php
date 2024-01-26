<?php

namespace App\Service\Handler;

use App\Entity\Property;
use App\Repository\PropertyRepository;
use App\Service\AddressService;
use App\Service\MediaService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PropertyHandlerService extends AbstractHandlerService
{
    /** @var array<int> */
    private array $idsInImport = [];

    public function __construct(protected EntityManagerInterface $entityManager, protected AddressService $addressService, protected MediaService $mediaService)
    {
    }

    /**
     * @param Property $model
     * @param OutputInterface $output
     */
    public function handle($model, $output): void
    {
        /** @var PropertyRepository $propertyRepo */
        $propertyRepo = $this->entityManager->getRepository(Property::class);

        $property = $propertyRepo->findOneBy(['externalId' => $model->getExternalId()], [], 1);
        $existingPropertyMediaHash = $property ? $property->getMediaHash() : null;
        $this->idsInImport[] = $model->getExternalId();

        if ($property) {
            $property->map($model);

            if ($existingPropertyMediaHash === null || $existingPropertyMediaHash !== $model->getMediaHash()) {
                $output->writeln('<info>Handling media existing property</info>');
                $property->setImage($this->handlePropertyMainImage($model));
                $property->setMedia($this->mediaService->handleMedia($property->getMedia()));

                $output->writeln('<info>Updated media for Property: '.$model->getTitle().'</info>');
            } else {
                $output->writeln('<info>No media update needed for: '.$model->getTitle().'</info>');
            }

            $this->checkLatLong($property);
            $property->createSlug();
            $this->entityManager->persist($property);
            return;
        }

        $model->createSlug();
        $output->writeln('<info>Handling media for new property</info>');
        $model->setImage($this->handlePropertyMainImage($model));
        $model->setMedia($this->mediaService->handleMedia($model->getMedia()));
        $this->checkLatLong($model);
        $this->entityManager->persist($model);

        $output->writeln('<info>Added Property: '.$model->getTitle().'</info>');

        return;
    }

    /**
     * This function checks if the lat and long are set for the given property
     */
    private function checkLatLong(Property &$property): void
    {
        $numberIsZero = $property->getHouseNumber() === 0 || null === $property->getHouseNumber();

        if (!$numberIsZero && (!$property->getLat() || !$property->getLng())) {
            if (($property->getHouseNumber() && !$numberIsZero) && $property->getStreet() && $property->getCity()) {
                $geoData = $this->addressService->getLatLngFromAddress($property->getHouseNumber(), $property->getStreet(), $property->getCity());
                if($geoData) {
                    $property->setLat($geoData['lat']);
                    $property->setLng($geoData['lng']);
                }
            }
        }
    }

    /**
     * @param OutputInterface $output
     */
    public function archiveItems($output): void
    {
        /** @var PropertyRepository $propertyRepo */
        $propertyRepo = $this->entityManager->getRepository(Property::class);

        $count = $propertyRepo->archiveMissing($this->idsInImport);

        $output->writeln("<info>Archived ${count} properties </info>");
    }

    public function persist(): void
    {
        $this->entityManager->flush();
    }

    private function handlePropertyMainImage(Property $item): array
    {
        $options = [
            'sizes' => [
                '1x' => '400x266',
                '2x' => '800x532',
            ],
        ];

        $mainImage = current(array_filter($item->getMedia(), function ($media) {
            return $media['soort'] === 'HOOFDFOTO';
        }));

        if (!$mainImage || !isset($mainImage['link'])) {
            return [];
        }

        return $this->mediaService->buildObject($mainImage['link'], $options);
    }
}
