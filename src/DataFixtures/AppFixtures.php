<?php
// src\DataFixtures\AppFixtures.php

namespace App\DataFixtures;

use App\Controller\EquipeController;
use App\Entity\Joueur;
use App\Entity\Equipe;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Création d'un user "normal"
        $user = new User();
        $user->setEmail("user@bookapi.com");
        $user->setRoles(["ROLE_USER"]);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, "password"));
        $manager->persist($user);

        // Création d'un user admin
        $userAdmin = new User();
        $userAdmin->setEmail("admin@bookapi.com");
        $userAdmin->setRoles(["ROLE_ADMIN"]);
        $userAdmin->setPassword($this->userPasswordHasher->hashPassword($userAdmin, "12345"));
        $manager->persist($userAdmin);


        // Création d'une équipe
        $listEquipe = [];
        for ($i = 0; $i < 10; $i++) {
            // Création de l'auteur lui-même.
            $equipe = new Equipe();
            $equipe->setNom("Equipe " . $i);
            $equipe->setLogo("Logo " . $i);
            $equipe->setSurnom("Surnom " . $i);
            $manager->persist($equipe);

            // On sauvegarde l'auteur créé dans un tableau.
            $listEquipe[] = $equipe;
        }

        for ($i = 0; $i < 20; $i++) {
            $joueur = new Joueur();
            $joueur->setNom("Nom " . $i);
            $joueur->setPrenom("Prenom " . $i);
            $joueur->setAge(20 + $i);
            $joueur->setSexe("Sexe " . $i);
            $joueur->setPoste("Poste " . $i);
            $joueur->setNumero($i);
            $joueur->setEquipe($listEquipe[array_rand($listEquipe)]);
            $manager->persist($joueur);
        }



        $manager->flush();
    }
}