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
            'routes' => [
                'buildTree' => 'Returns you branch of the tree',
                'data' => 'Returns you all existing fields'
                ]
        ]);
    }

    /**
     * @Route("/data", name="data")
     * @return Response
     */
    public function getData(): Response
    {
        $data = $this->getDoctrine()->getRepository(University::class)->findAll();

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Route("/university/buildTree", name="buildTree")
     * @return Response
     */
    public function buildTree($start_id = 1): Response
    {
        $data = $this->getData()->getContent();
        $data = json_decode($data,true);

        $data = array_combine(range(1, count($data)), array_values($data));

        foreach ($data as $key => $value) {
            $data[$value['parent_id']]['sub_divisions'][] = &$data[$key];
        }

        return new JsonResponse($data[$start_id], Response::HTTP_OK);
    }
}
