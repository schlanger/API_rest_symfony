<?php

namespace App\Controller;

use App\Entity\Equipe;
use App\Entity\Joueur;
use App\Repository\EquipeRepository;
use App\Repository\JoueurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class EquipeController extends AbstractController
{
    #[Route('/api/equipe', name: 'app_equipe',methods: ['GET'])]
    public function getEquipeList(EquipeRepository $equipeRepository, SerializerInterface $serializer): JsonResponse
    {
        $EquipeList = $equipeRepository->findAll();
        $jsonEquipeList = $serializer->serialize($EquipeList, 'json',['groups' => 'equipe']);
        return new JsonResponse($jsonEquipeList, Response::HTTP_OK, [], true);
    }

    #[Route('/api/equipe/{id}', name: 'detailEquipe',methods: ['GET'])]
    public function getEquipeById(Equipe $equipe, SerializerInterface $serializer): JsonResponse
    {
        $jsonEquipeList = $serializer->serialize($equipe, 'json',['groups' => 'equipe']);
        return new JsonResponse($jsonEquipeList, Response::HTTP_OK, [], true);
    }

    #[Route('/api/equipe/{id}', name: 'deleteEquipe',methods: ['DELETE'])]
    public function deleteJoueur(Equipe $equipe,EntityManagerInterface $em) : JsonResponse {
        $em->remove($equipe);
        $em->flush();
        return new JsonResponse(null,Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/equipe', name: 'createEquipe',methods: ['POST'])]
    public function createEquipe( Request $request, SerializerInterface $serializer,EntityManagerInterface $em,UrlGeneratorInterface $urlGenerator,EquipeRepository $equipeRepository) : JsonResponse {
        $equipe = $serializer->deserialize($request->getContent(),Equipe::class,'json');

        $em->persist($equipe);
        $em->flush();
        $jsonequipe = $serializer->serialize($equipe,'json',['groups'=> 'equipe']);

        $location = $urlGenerator->generate('detailEquipe', ['id' => $equipe->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        return new JsonResponse($jsonequipe,Response::HTTP_CREATED,["Location" => $location], true);
    }

    #[Route('/api/equipe/{id}', name: 'updateEquipe',methods: ['PUT'])]
    public function updateEquipe( Request $request, SerializerInterface $serializer,Equipe $equipe,
                                  EntityManagerInterface $em,UrlGeneratorInterface $urlGenerator,EquipeRepository $equipeRepository)
    : JsonResponse {
        $updateEquipe = $serializer->deserialize($request->getContent(),Equipe::class,'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $equipe]);

        $em->persist($updateEquipe);
        $em->flush();

        return new JsonResponse($updateEquipe,JsonResponse::HTTP_OK);
    }
}
