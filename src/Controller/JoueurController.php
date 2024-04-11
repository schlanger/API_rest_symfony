<?php

namespace App\Controller;

use App\Entity\Joueur;
use App\Repository\EquipeRepository;
use App\Repository\JoueurRepository;
use App\Service\VersioningService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

/**
 * Cette méthode permet de récupérer l'ensemble des joueurs.
 *
 * @OA\Response(
 *     response=200,
 *     description="Retourne la liste des joueurs",
 *     @OA\JsonContent(
 *        type="array",
 *        @OA\Items(ref=@Model(type=Joueur::class, groups={"joueur"}))
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
 * @OA\Tag(name="Joueur")
 *
 * @param JoueurRepository $bookRepository
 * @param SerializerInterface $serializer
 * @param Request $request
 * @return JsonResponse
 */


class JoueurController extends AbstractController
{
    #[Route('/api/joueur', name: 'app_joueur',methods: ['GET'])]
    public function getJoueurList(JoueurRepository $joueurRepository, SerializerInterface $serializer,Request $request,TagAwareCacheInterface $cachePool): JsonResponse
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 3);

        $idCache = "getJoueurList-" . $page . "-" . $limit;

        $jsonJoueurList = $cachePool->get($idCache, function (ItemInterface $item) use ($joueurRepository, $page, $limit, $serializer) {
            echo ("L'element n'est pas encore dans le cache !");
            $item->tag("joueurCache");
            $item->expiresAfter(60);
            $joueurList = $joueurRepository->findAllWithPagination($page, $limit);
            $context = SerializationContext::create()->setGroups(['joueur']);
            return $serializer->serialize($joueurList, 'json',$context);
        });

        return new JsonResponse($jsonJoueurList, Response::HTTP_OK, [], true);
    }

    #[Route('/api/joueur/{id}', name: 'detailJoueur',methods: ['GET'])]
    public function getJoueurById(Joueur $joueur, SerializerInterface $serializer,VersioningService $versioningService): JsonResponse
    {
        $version = $versioningService->getVersion();
        $context = SerializationContext::create()->setGroups(['joueur']);
        $context->setVersion($version);
        $jsonBookList = $serializer->serialize($joueur, 'json',$context);
        return new JsonResponse($jsonBookList, Response::HTTP_OK, [], true);
    }
   #[Route('/api/joueur/{id}', name: 'deleteJoueur',methods: ['DELETE'])]
   #[IsGranted("ROLE_ADMIN", message: "Seul un admin peut supprimer un joueur")]
    public function deleteJoueur(Joueur $joueur,EntityManagerInterface $em,TagAwareCacheInterface $cache) : JsonResponse {

        $cache->invalidateTags(["joueurCache"]);
        $em->remove($joueur);
        $em->flush();
        return new JsonResponse(null,Response::HTTP_NO_CONTENT);
   }

    #[Route('/api/joueur', name: 'createJoueur',methods: ['POST'])]
    #[IsGranted("ROLE_ADMIN", message: "Seul un admin peut ajouter un joueur")]
    public function createJoueur( Request $request, SerializerInterface $serializer,EntityManagerInterface $em,UrlGeneratorInterface $urlGenerator,EquipeRepository $equipeRepository,ValidatorInterface $validator) : JsonResponse {
        $joueur = $serializer->deserialize($request->getContent(),Joueur::class,'json');

        $content = $request->toArray();

        $idEquipe = $content['equipe'];

        $joueur->setEquipe($equipeRepository->find($idEquipe));

        // On vérifie les erreurs

        $errors = $validator->validate($joueur);
        if ( $errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors,'json'), JsonResponse::HTTP_BAD_REQUEST,[], true);
        }

        $em->persist($joueur);
        $em->flush();
        $context = SerializationContext::create()->setGroups(['joueur']);
        $jsonJoueur = $serializer->serialize($joueur,'json',$context);

        $location = $urlGenerator->generate('detailJoueur', ['id' => $joueur->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        return new JsonResponse($jsonJoueur,Response::HTTP_CREATED,["Location" => $location], true);
    }

    #[Route('/api/joueur/{id}', name: 'updateJoueur',methods: ['PUT'])]
    #[IsGranted("ROLE_ADMIN", message: "Seul un admin peut modifier un joueur")]
    public function updateJoueur(Request $request, SerializerInterface $serializer, Joueur $currentJoueur, EntityManagerInterface $em, EquipeRepository $equipeRepository, ValidatorInterface $validator, TagAwareCacheInterface $cache): JsonResponse
    {
        $newJoueur = $serializer->deserialize($request->getContent(), Joueur::class, 'json');
        $currentJoueur->setNom($newJoueur->getNom());
        $currentJoueur->setPrenom($newJoueur->getPrenom());
        $currentJoueur->setAge($newJoueur->getAge());
        $currentJoueur->setSexe($newJoueur->getSexe());
        $currentJoueur->setPoste($newJoueur->getPoste());

        // On vérifie les erreurs
        $errors = $validator->validate($currentJoueur);
        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        $content = $request->toArray();
        $idJoueur = $content['equipe'] ?? -1;

        $currentJoueur->setEquipe($equipeRepository->find($idJoueur));

        $em->persist($currentJoueur);
        $em->flush();

        // On vide le cache.
        $cache->invalidateTags(["JoueurCache"]);

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }


}
