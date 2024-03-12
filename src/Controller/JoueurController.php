<?php

namespace App\Controller;

use App\Repository\JoueurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class JoueurController extends AbstractController
{
    #[Route('/api/joueur', name: 'app_joueur',methods: ['GET'])]
    public function getJoueurList(JoueurRepository $joueurRepository, SerializerInterface $serializer): JsonResponse
    {
        $joueurList = $joueurRepository->findAll();
        $jsonBookList = $serializer->serialize($joueurList, 'json',['groups' => 'joueur']);
        return new JsonResponse($jsonBookList, Response::HTTP_OK, [], true);
    }
}
