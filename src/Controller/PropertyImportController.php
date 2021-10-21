<?php

namespace App\Controller;

use App\Entity\Property;
use App\Service\PropertyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class PropertyImportController extends AbstractController
{
    /**
     * @Route("/property-import", name="property_import")
     */
    public function import(PropertyService $propertyService): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $data = $propertyService->getProperties();

        $createdProperties = 0;
        $updatedProperties = 0;

        $propertyRepo = $entityManager->getRepository(Property::class);
        foreach ($data as $property) {

            if (($existingProperty = $propertyRepo->findOneBy(['id' => $property->getId()])) && $existingProperty->getUpdateHash() !== md5(serialize($property))) {
                $property->setUpdateHash(md5(serialize($property)));
                $existingProperty->map($property);

                $entityManager->persist($existingProperty);

                $updatedProperties++;

                continue;
            }


            if(!isset($existingProperty)) {
                $entityManager->persist($property);

                $createdProperties++;
            }
        }

        $entityManager->flush();

        return $this->json([
            'result' => 'Saved ' . $createdProperties . ' properties and updated ' . $updatedProperties . ' properties',
            'path' => 'src/Controller/PropertyImportController.php',
        ]);
    }

    /**
     * @Route("/properties", name="property_index")
     */
    public function index(): Response
    {
        $propertyRepo = $this->getDoctrine()->getManager()->getRepository(Property::class);

        $data = $propertyRepo->findAll();

        return $this->json([
           'count' => count($data),
           'data' => $data,
       ]);

    }
}
