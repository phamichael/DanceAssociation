<?php

namespace StagesBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use StagesBundle\Entity\Stage;

class LoadStage implements FixtureInterface{
	
  // Dans l'argument de la méthode load, l'objet $manager est l'EntityManager
  public function load(ObjectManager $manager){
    
    // TimeZone :
    $tz = new \DateTimeZone('Europe/Paris');
	
	// Liste des stages :
    $stages = array(
		// titreStage, dateStage, heureDebutStage(a revoir), heureFinStage(a revoir), montantPreinscription, delaiPreinscription, tarifStage, descriptifStage
		array('bachatons mes amis',new \DateTime('2014-11-11 17:26:30', $tz), new \DateTime('2014-11-11 17:26:30', $tz), new \DateTime('2014-11-11 17:26:30', $tz), 10, new \DateTime('2014-11-11 17:26:30', $tz), 40, "Découverte du bachata avec des p"),
		array('Holé Holà me gusta la salsa',new \DateTime('2016-01-05 17:26:30', $tz),  new \DateTime('2014-11-11 17:26:30', $tz), new \DateTime('2014-11-11 17:26:30', $tz), 15,new \DateTime('2014-11-11 17:26:30', $tz), 47, "stage de perfectionnement à la s")
    );

    foreach ($stages as $un_stage) {
      // On crée le stage
      $stage = new Stage();
      $stage->setTitreStage($un_stage[0]);
      $stage->setDateStage($un_stage[1]);
      $stage->setHeureDebutStage($un_stage[2]);
      $stage->setHeureFinStage($un_stage[3]);
      $stage->setMontantPreinscription($un_stage[4]);
      $stage->setDelaiPreinscription($un_stage[5]);
      $stage->setTarifStage($un_stage[6]);
      $stage->setDescriptifStage($un_stage[7]);

      // On la persiste
      $manager->persist($stage);
    }

    // On déclenche l'enregistrement de toutes les catégories
    $manager->flush();
  }
}
