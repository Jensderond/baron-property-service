<?php

namespace App\Controller;

use App\Service\PyberService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class PyberImportController extends AbstractController
{
    /**
     * @Route("/pyber/import", name="pyber_import")
     */
    public function index(PyberService $pyberService): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $data = $pyberService->getProperties();

        foreach ($data as $property) {
            $entityManager->persist($property);
        }

        $entityManager->flush();

        return $this->json([
            'result' => 'Saved ' . count($data) . ' properties',
            'path' => 'src/Controller/PyberImportController.php',
        ]);
    }
}
