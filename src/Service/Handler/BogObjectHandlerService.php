<?php

namespace App\Service\Handler;

use App\Entity\BogObject;
use App\Repository\BogObjectRepository;
use App\Service\AddressService;
use App\Service\MediaService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BogObjectHandlerService extends AbstractHandlerService
{
    /** @var array<int> */
    private array $idsInImport = [];

    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected AddressService $addressService,
        protected MediaService $mediaService
    ) {}

    /**
     * @param BogObject $model
     * @param OutputInterface $output
     */
    public function handle($model, $output): void
    {
        /** @var BogObjectRepository $projectRepo */
        $projectRepo = $this->entityManager->getRepository(BogObject::class);
        $existingObject = $projectRepo->findOneBy(['externalId' => $model->getExternalId()], [], 1);
        $existingObjectMediaHash = $existingObject ? $existingObject->getMediaHash() : null;
        $this->idsInImport[] = $model->getExternalId();


        if ($existingObject) {
            $existingObject->map($model);
            $existingObject->createSlug();

            $existingObject->setImage($this->handleMainImage($model->getImage()));
            if (empty($existingObjectMediaHash) || $existingObjectMediaHash !== $existingObject->getMediaHash()) {
                $output->writeln('<info>Handling media existing BOG Object</info>');
                $existingObject->setMedia($this->mediaService->handleMedia($model->getMedia()));
                $existingObject->setUpdatedAt($model->getUpdatedAt());
            } else {
                $output->writeln('<info>Skipping media existing BOG Object</info>');
            }

            $this->checkLatLong($existingObject);

            $this->entityManager->persist($existingObject);
            $output->writeln('<info>Updated BOG Object: ' . $model->getTitle() . '</info>');
            return;
        }

        $model->map($model);
        $model->setImage($this->handleMainImage($model->getImage()));
        $model->setMedia($this->mediaService->handleMedia($model->getMedia()));
        $model->createSlug();
        $this->checkLatLong($model);
        $output->writeln('<info>Handling media for new BOG Object</info>');
        $model->setMedia($this->mediaService->handleMedia($model->getMedia()));

        $this->entityManager->persist($model);

        $output->writeln('<info>Added BOG Object: ' . $model->getTitle() . '</info>');
        return;
    }

    public function persist(): void
    {
        $this->entityManager->flush();
    }

    /**
     * @param OutputInterface $output
     */
    public function archiveItems($output): void
    {
        /** @var BogObjectRepository $projectRepo */
        $projectRepo = $this->entityManager->getRepository(BogObject::class);

        $count = $projectRepo->archiveMissing($this->idsInImport);

        $output->writeln("<info>Archived $count BOG Objects </info>");
    }

    /**
     * This function checks if the lat and long are set for the given property
     */
    private function checkLatLong(BogObject &$object): void
    {
        $houseNumber = strtolower(str_replace(' ', '', $object->getHouseNumber()));
        $numberIsZero = $houseNumber === "0" || null === $houseNumber || $houseNumber === "0ong";

        if (!$numberIsZero && (!$object->getLat() || !$object->getLng())) {
            if (($object->getHouseNumber() && !$numberIsZero) && $object->getStreet() && $object->getCity()) {
                $geoData = $this->addressService->getLatLngFromAddress($object->getHouseNumber(), $object->getStreet(), $object->getCity());
                if ($geoData) {
                    $object->setLat($geoData['lat']);
                    $object->setLng($geoData['lng']);
                }
            }
        }
    }

    private function handleMainImage(?array $mediaItems): array
    {
        $options = [
            'sizes' => [
                '1x' => '401x267',
                '2x' => '802x534',
            ],
        ];

        if (!$mediaItems || !isset($mediaItems['link'])) {
            return [];
        }

        $url = parse_url($mediaItems['link']);
        if (isset($url['query'])) {
            $mediaItems['link'] .= '&resize=4';
        } else {
            $mediaItems['link'] .= '?resize=4';
        }

        return $this->mediaService->buildObject($mediaItems['link'], $options);
    }
}
