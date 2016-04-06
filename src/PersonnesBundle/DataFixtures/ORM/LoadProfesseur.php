<?php
//PersonnesBundle/DataFixtures/ORM/LoadCategory.php

namespace PersonnesBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use PersonnesBundle\Entity\Professeur;

class LoadProfesseur implements FixtureInterface
{
  // Dans l'argument de la méthode load, l'objet $manager est l'EntityManager
  public function load(ObjectManager $manager)
  {
    // Liste des noms de catégorie à ajouter
    $lesProfesseurs = array(
      array('Durand','Pascal','16 rue Eugene Turbat',new \DateTime('1987-11-11 17:26:30',new \DateTimeZone('Europe/Paris'))
        ,0239393939,'durand.pascal@hotmail.fr',
        'durandp','durandp','ROLE_PROF'),
      array('admin','admin','16 rue Eugene Turbat',new \DateTime('1987-11-11 17:26:30',new \DateTimeZone('Europe/Paris'))
        ,0239393939,'admin.admin@hotmail.fr',
        'admin','admin','ROLE_ADMIN'),
    );

    foreach ($lesProfesseurs as $un_professeur) {
      // On crée le professeur
      $professeur = new Professeur();
      $professeur->setNomPersonne($un_professeur[0]);
      $professeur->setPrenomPersonne($un_professeur[1]);
      $professeur->setAdressePersonne($un_professeur[2]);
      $professeur->setDateNaissancePersonne($un_professeur[3]);
      $professeur->setTelephonePersonne($un_professeur[4]);
      $professeur->setEmail($un_professeur[5]);
      $professeur->setEmailPersonne($un_professeur[5]);
      $professeur->setUsername($un_professeur[6]);
      $professeur->setPassword($un_professeur[7]);
      $professeur->addRole($un_professeur[8]);

      $manager->persist($professeur);
    }

    // On déclenche l'enregistrement de tous les professeurs
    $manager->flush();
  }
}