<?php

namespace App\Controller;

use App\Entity\Joueur;
use App\Repository\EquipeRepository;
use App\Repository\JoueurRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
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

    #[Route('/api/joueur/{id}', name: 'detailJoueur',methods: ['GET'])]
    public function getJoueurById(Joueur $joueur, SerializerInterface $serializer): JsonResponse
    {
        $jsonBookList = $serializer->serialize($joueur, 'json',['groups' => 'joueur']);
        return new JsonResponse($jsonBookList, Response::HTTP_OK, [], true);
    }
   #[Route('/api/joueur/delete/{id}', name: 'deleteJoueur',methods: ['DELETE'])]
    public function deleteJoueur(Joueur $joueur,EntityManagerInterface $em) : JsonResponse {
        $em->remove($joueur);
        $em->flush();
        return new JsonResponse(null,Response::HTTP_NO_CONTENT);
   }

    #[Route('/api/joueur/create', name: 'createJoueur',methods: ['POST'])]
    public function createJoueur( Request $request, SerializerInterface $serializer,EntityManagerInterface $em,UrlGeneratorInterface $urlGenerator,EquipeRepository $equipeRepository) : JsonResponse {
        $joueur = $serializer->deserialize($request->getContent(),Joueur::class,'json');

        $content = $request->toArray();

        $idEquipe = $content['equipe_id'] ?? 1;

        $joueur->setEquipeId($equipeRepository->find($idEquipe));

        $em->persist($joueur);
        $em->flush();
        $jsonJoueur = $serializer->serialize($joueur,'json',['groups'=> 'joueur']);

        $location = $urlGenerator->generate('detailJoueur', ['id' => $joueur->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        return new JsonResponse($jsonJoueur,Response::HTTP_CREATED,["Location" => $location], true);
    }

    #[Route('/api/joueur/update/{id}', name: 'updateJoueur',methods: ['PUT'])]
    public function updateJoueur( Request $request, SerializerInterface $serializer,Joueur $currentJoueur,EntityManagerInterface $em,UrlGeneratorInterface $urlGenerator,EquipeRepository $equipeRepository) : JsonResponse {
        $updatejoueur = $serializer->deserialize($request->getContent(),Joueur::class,'json',[AbstractNormalizer::OBJECT_TO_POPULATE => $currentJoueur]);

        $content = $request->toArray();

        $idEquipe = $content['equipe_id'] ?? 1;

        $updatejoueur->setEquipeId($equipeRepository->find($idEquipe));

        $em->persist($updatejoueur);
        $em->flush();

        return new JsonResponse(null,JsonResponse::HTTP_NO_CONTENT);
    }


}
