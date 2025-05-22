<?php

namespace App\Controller;

use App\Entity\BogObject;
use App\Entity\ConstructionNumber;
use App\Entity\Project;
use App\Entity\Property;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use ApiPlatform\Symfony\Action\NotFoundAction;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class ExternalItemController extends AbstractController
{
    public function __invoke(EntityManagerInterface $entityManager, int $id): Project|NotFoundAction|Property|BogObject|ConstructionNumber
    {
        $repositories = [Property::class, Project::class, BogObject::class, ConstructionNumber::class];

        foreach ($repositories as $entityClass) {
            $repository = $entityManager->getRepository($entityClass);
            $item = $repository->findOneBy(['externalId' => $id]);

            if ($item) {
                return $item;
            }
        }

        return new NotFoundAction();
    }
}
