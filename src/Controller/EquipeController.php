<?php

namespace App\Controller;

use App\Entity\Equipe;
use App\Entity\Joueur;
use App\Repository\EquipeRepository;
use App\Repository\JoueurRepository;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

/**
 * Cette méthode permet de récupérer l'ensemble des équipes.
 *
 * @OA\Response(
 *     response=200,
 *     description="Retourne la liste des équipes",
 *     @OA\JsonContent(
 *        type="array",
 *        @OA\Items(ref=@Model(type=Equipe::class, groups={"equipe"}))
 *     )
 * )
 * @OA\Parameter(
 *     name="page",
 *     in="query",
 *     description="La page que l'on veut récupérer",
 *     @OA\Schema(type="int")
 * )
 *
 * @OA\Parameter(
 *     name="limit",
 *     in="query",
 *     description="Le nombre d'éléments que l'on veut récupérer",
 *     @OA\Schema(type="int")
 * )
 * @OA\Tag(name="Equipe")
 *
 * @param EquipeRepository $bookRepository
 * @param SerializerInterface $serializer
 * @param Request $request
 * @return JsonResponse
 */

class EquipeController extends AbstractController
{
    #[Route('/api/equipe', name: 'app_equipe',methods: ['GET'])]
    public function getEquipeList(EquipeRepository $equipeRepository, SerializerInterface $serializer,Request $request,TagAwareCacheInterface $cache): JsonResponse
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 3);

        $idCache = "getEquipeList-" . $page . "-" . $limit;

        $jsonEquipeList = $cache->get($idCache, function (ItemInterface $item) use ($equipeRepository, $page, $limit, $serializer) {
            echo ("L'element n'est pas encore dans le cache !");
            $item->tag("equipeCache");
            $item->expiresAfter(60);
            $equipeList = $equipeRepository->findAllWithPagination($page, $limit);
            $context = SerializationContext::create()->setGroups(['equipe']);
            return $serializer->serialize($equipeList, 'json',$context);
        });
        return new JsonResponse($jsonEquipeList, Response::HTTP_OK, [], true);
    }

    #[Route('/api/equipe/{id}', name: 'detailEquipe',methods: ['GET'])]
    public function getEquipeById(Equipe $equipe, SerializerInterface $serializer): JsonResponse
    {
        $context = SerializationContext::create()->setGroups(['equipe']);
        $jsonEquipeList = $serializer->serialize($equipe, 'json',$context);
        return new JsonResponse($jsonEquipeList, Response::HTTP_OK, [], true);
    }

    #[Route('/api/equipe/{id}', name: 'deleteEquipe',methods: ['DELETE'])]
    #[IsGranted("ROLE_ADMIN", message: "Seul un admin peut supprimer une équipe")]
    public function deleteJoueur(Equipe $equipe,EntityManagerInterface $em,TagAwareCacheInterface $cache) : JsonResponse {
        $cache->invalidateTags(["equipeCache"]);
        $em->remove($equipe);
        $em->flush();
        return new JsonResponse(null,Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/equipe', name: 'createEquipe',methods: ['POST'])]
    #[IsGranted("ROLE_ADMIN", message: "Seul un admin peut ajouter une équipe")]
    public function createEquipe( Request $request, SerializerInterface $serializer,EntityManagerInterface $em,UrlGeneratorInterface $urlGenerator,EquipeRepository $equipeRepository) : JsonResponse {
        $equipe = $serializer->deserialize($request->getContent(),Equipe::class,'json');

        $em->persist($equipe);
        $em->flush();
        $context = SerializationContext::create()->setGroups(['equipe']);
        $jsonequipe = $serializer->serialize($equipe,'json',$context);

        $location = $urlGenerator->generate('detailEquipe', ['id' => $equipe->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        return new JsonResponse($jsonequipe,Response::HTTP_CREATED,["Location" => $location], true);
    }

    #[Route('/api/equipe/{id}', name: 'updateEquipe',methods: ['PUT'])]
    #[IsGranted("ROLE_ADMIN", message: "Seul un admin peut modifier une équipe")]
    public function updateEquipe(Request $request, SerializerInterface $serializer, Equipe $currentEquipe, EntityManagerInterface $em, ValidatorInterface $validator, TagAwareCacheInterface $cache): JsonResponse
    {
        $newEquipe = $serializer->deserialize($request->getContent(), Equipe::class, 'json');
        $currentEquipe->setNom($newEquipe->getNom());
        $currentEquipe->setLogo($newEquipe->getLogo());
        $currentEquipe->setSurnom($newEquipe->getSurnom());


        // On vérifie les erreurs
        $errors = $validator->validate($currentEquipe);
        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        $em->persist($currentEquipe);
        $em->flush();

        // On vide le cache.
        $cache->invalidateTags(["EquipeCache"]);

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
