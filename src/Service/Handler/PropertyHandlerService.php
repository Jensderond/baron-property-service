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

        /** @var list<Property> $property */
        $property = $propertyRepo->findBy(['externalId' => $model->getExternalId()], [], 1);
        $property = $property[0] ?? null;

        dump($property->getUpdatedAt() < $model->getUpdatedAt());
        die();

        if ($property && $property->getUpdatedAt() < $model->getUpdatedAt()) {
            $this->idsInImport[] = $property->getExternalId();

            $tmpLat = $property->getLat();
            $tmpLng = $property->getLng();

            $property->map($model);

            if (!$property->getLat() && !$property->getLng()) {
                $property->setLat($tmpLat);
                $property->setLng($tmpLng);
            }

            $property->setImage($this->handlePropertyMainImage($model));
            $property->setMedia($this->handleMedia($property->getMedia()));

            $this->checkLatLong($property);

            $property->createSlug();

            $this->entityManager->persist($property);
            $output->writeln('<info>Updated Property: '.$model->getTitle().'</info>');
            return;
        } else {
            $output->writeln('<info>No update needed for: '.$model->getTitle().'</info>');

            return;
        }

        $model->createSlug();
        $model->setImage($this->handlePropertyMainImage($model));
        $model->setMedia($this->handleMedia($model->getMedia()));
        $this->checkLatLong($model);
        $this->entityManager->persist($model);

        $output->writeln('<info>Added Property: '.$model->getTitle().'</info>');

        return;
    }

    /**
     * This function checks if the lat and long are set for the given property
     */
    public function checkLatLong(Property &$property): void
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
    public function archiveProperties($output): void
    {
        /** @var PropertyRepository $propertyRepo */
        $propertyRepo = $this->entityManager->getRepository(Property::class);


        $count = $propertyRepo->archiveProperties($this->idsInImport);

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

        if (!$mainImage) {
            return [];
        }

        return $this->mediaService->buildObject($mainImage['link'], $options);
    }

    private function handleMedia(?array $mediaInput): array
    {
        if (!isset($mediaInput)) {
            return [];
        }

        $transformedItems = [];

        foreach ($mediaInput as $key => $media) {
            if ($media['soort'] === 'HOOFDFOTO' || $media['soort'] === 'FOTO') {
                $transformedItems[] = $this->mediaService->transfromItem($media);

                unset($mediaInput[$key]);
            }
        }

        $mediaItems = array_merge($transformedItems, array_values($mediaInput));

        return $mediaItems;
    }
}
