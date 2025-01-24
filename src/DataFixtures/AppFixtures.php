<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\utilisateur;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $utilisateur = new utilisateur();
        $utilisateur->setEmail('youness.azhich@gmail.com');
        $utilisateur->setPassword('1234');
        $manager->persist($utilisateur);

        $utilisateur = new utilisateur();
        $utilisateur->setEmail('omar.azhich@gmail.com');
        $utilisateur->setPassword('5678');
        $manager->persist($utilisateur);

        $manager->flush();
    }
}
