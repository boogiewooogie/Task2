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
            'routes' => [
                'buildTree' => 'Returns you branch of the tree',
                'data' => 'Returns you all existing rows',
                'information' => 'Returns you all existing fields of row',
                'delete' => 'Remove row and all existing dependencies',
                'update' => ['description' => 'Update the field of the row', 'name' => 'Updates name', 'parentID' => 'Updates parentID']
                ]
        ]);
    }

    /**
     * @Route("/data", name="data")
     * @return JsonResponse
     */
    public function getData(): JsonResponse
    {
        $data = $this->getDoctrine()->getRepository(University::class)->findAll();

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @param int $start_id
     * @Route("/university/buildTree/{start_id}", name="buildTree")
     * @return JsonResponse
     */
    public function buildTree(int $start_id = 1): JsonResponse
    {
        $data = $this->getData()->getContent();
        $data = json_decode($data,true);

        $data = array_combine(range(1, count($data)), array_values($data));

        foreach ($data as $key => $value) {
            $data[$value['parent_id']]['sub_divisions'][] = &$data[$key];
        }

        return new JsonResponse($data[$start_id], Response::HTTP_OK);
    }

    /**
     * @param int $id
     * @Route("university/information/{id}", name="getInformation")
     * @return JsonResponse
     */
    public function getInformation(int $id): JsonResponse
    {
        $result = $this->getDoctrine()->getRepository(University::class)->find($id);

        return new JsonResponse($result, Response::HTTP_OK);
    }

    /**
     * @param int $id
     * @Route("university/delete/{id}", name="deleteNode")
     * @return Response
     */
    public function deleteNode(int $id): Response
    {
        $data = $this->getData()->getContent();
        $data = json_decode($data,true);

        $entityManager = $this->getDoctrine()->getManager();

        foreach ($data as $item ) {
            if ($item['id'] === $id ) {
                $deleted = $entityManager->getRepository(University::class)->find($id);
                $entityManager->remove($deleted);
                $entityManager->flush();
            }

            if ($item['parent_id'] === $id) {
                $deleted = $entityManager->getRepository(University::class)->find($item['id']);

                $this->deleteNode($item['id']);

                $entityManager->remove($deleted);
                $entityManager->flush();
            }
        }

        return new Response('Success!');
    }

    /**
     * @param int $id
     * @param string $new_name
     * @return Response
     * @Route("university/update/name/{id}/{new_name}", name="updateName")
     */
    public function updateName(int $id, string $new_name): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $row = $entityManager->getRepository(University::class)->find($id);

        $row->setName($new_name);
        $entityManager->flush();

        return new Response('Success!');
    }

    /**
     * @param int $id
     * @param int $new_parent_id
     * @return Response
     * @Route("university/update/parentID/{id}/{new_parent_id}", name="updateParentID")
     */
    public function updateParentID(int $id, int $new_parent_id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $row = $entityManager->getRepository(University::class)->find($id);

        $row->setParentId($new_parent_id);
        $entityManager->flush();

        return new Response('Success!');
    }
}
