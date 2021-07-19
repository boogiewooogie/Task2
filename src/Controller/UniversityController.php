<?php

namespace App\Controller;

use App\Entity\University;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UniversityController extends AbstractController
{
    /**
     * @Route("/university", name="university")
     * @return Response
     */
    public function index(): Response
    {
        return $this->json([
            'message' => 'Welcome to my API!',
            'path' => 'src/Controller/UniversityController.php',
            'commands' => []
        ]);
    }

    /**
     * @Route("/data", name="data")
     * @return Response
     */
    private function getData(): Response
    {
        $data = $this->getDoctrine()->getRepository(University::class)->findAll();

        return new JsonResponse(json_encode($data, JSON_UNESCAPED_UNICODE), Response::HTTP_OK);
    }
}
