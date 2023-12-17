<?php

namespace App\Service\Handler;

use App\Entity\Property;
use Doctrine\ORM\EntityManagerInterface;

class PropertyHandlerService extends AbstractHandlerService
{
    public function __construct(protected EntityManagerInterface $entityManager)
    {

    }

    /**
     * @param Property $model
     */
    public function handle($model, $output): void
    {
        return;
    }
}
