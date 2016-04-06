<?php

namespace PersonnesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

// Entités utilisés :
use PersonnesBundle\Entity\Personne;
use PersonnesBundle\Entity\Adherent;
use SaisonsBundle\Entity\ForfaitDanseNiveau;
use SaisonsBundle\Entity\Cours;

class GestionCompteController extends Controller{

	public function moncompteAction(Request $request){
		if( $this->container->get('security.context')->isGranted("IS_AUTHENTICATED_REMEMBERED") ){
			//On récupère les informations de la personne connectée
			$utilisateur = $this->container->get('security.context')->getToken()->getUser();

			$form = $this->get('form.factory')->createBuilder('form', $utilisateur)
				->add('Modifier mes informations','submit')
				->getForm();

			$form->handleRequest($request);
	        if ($form->isValid()){
	        	if($utilisateur->hasRole('ROLE_PROF')){
	        		return $this->redirect($this->generateUrl('professeur_editsuppr',array('idPersonne' => $utilisateur->getId())));
	        	}
	        	elseif($utilisateur->hasRole('ROLE_ADHERENT')){
	        		return $this->redirect($this->generateUrl('adherent_editsuppr',array('idPersonne' => $utilisateur->getId())));
	        	}
	        	
	        }

			//Il faut aussi afficher 
			//		- le type de forfait (nombre de danse choisie(s) et pour chacune d'elle(s) le niveau choisi)
			//      - le moyen de paiement
			//

			$em = $this->getDoctrine()->getManager();
			$personne = $em->getRepository('PersonnesBundle:Personne')->findOneById($utilisateur->getId());

			if($utilisateur->hasRole('ROLE_ADHERENT')){
				$adh =     $em->getRepository('PersonnesBundle:Adherent')->findOneBy(array('personne' => $personne->getId()));
				$forfait = $em->getRepository('SaisonsBundle:Forfait')->findOneBy(array('adherent' => $adh->getId()));
				$listDanseNiveau = $em->getRepository('SaisonsBundle:ForfaitDanseNiveau')->FindBy(array('idForfait' => $forfait->getId()));

				$soirees = $em->getRepository("SoireesBundle:Soiree")->getSoireesAdherent($adh->getId())->getQuery()->getResult();
				$stages = $em->getRepository("StagesBundle:Stage")->getStagesAdherent($utilisateur->getId())->getQuery()->getResult();
				
				return $this->render('PersonnesBundle:moncompte.html.twig',array('utilisateur'=>$utilisateur,
				'adherent'=>$adh,
				'forfait'=>$forfait,
				'listDanseNiveau'=>$listDanseNiveau,
				'listStages'=>$stages,
				'listSoirees'=>$soirees,
				'form' => $form->createView()));

			}
			elseif ($utilisateur->hasRole('ROLE_PROFASSOC')) {
				$profAsso = $em->getRepository('PersonnesBundle:ProfesseurAssociation')->findOneBy(
					array('personne' => $personne->getId()));
				
				$em = $this->getDoctrine()->getManager();
				
				// Rappel : Les cours et soirées ne peuvent être enseignés que par les professeurs de l'association.
				
				// Cours enseigné par le professeur, récupéré depuis une méthode créée dans CoursRepository.php
				$cours = $em->getRepository("CoursBundle:Cours")->getCours($profAsso->getId())->getQuery()->getResult();
				$soirees = $em->getRepository("SoireesBundle:Soiree")->getSoirees($profAsso->getId())->getQuery()->getResult();
				
				$stages = $em->getRepository("StagesBundle:Stage")->getStages($profAsso->getId())->getQuery()->getResult();
				
				// PROFESSEURS EXTERNES, récupérer ses stages :
				// $stages = $em->getRepository("StagesBundle:Stage")->getStages_profExterne($profAsso->getId())->getQuery()->getResult();
				
				

				return $this->render('PersonnesBundle:moncompte.html.twig',array('utilisateur'=>$utilisateur,
				'profAssociation'=>$profAsso,
				'listCours'=>$cours,
				'listStages'=>$stages,
				'listSoirees'=>$soirees,
				'form' => $form->createView()));
					
			}
			elseif ($utilisateur->hasRole('ROLE_PROF')) {
				$profExt = $em->getRepository('PersonnesBundle:Professeur')->findOneBy(
					array('personne' => $personne->getId()));
				$em = $this->getDoctrine()->getManager();

				$stages = $em->getRepository("StagesBundle:Stage")->getStages_profExterne($profExt->getId())->getQuery()->getResult();
				return $this->render('PersonnesBundle:moncompte.html.twig',array('utilisateur'=>$utilisateur,
					'listStages'=>$stages,
					'form' => $form->createView()));
			}
			return $this->render('PersonnesBundle:moncompte.html.twig',array('utilisateur'=>$utilisateur,
				'form' => $form->createView()));
		
		}else{
			return $this->redirect($this->generateUrl('accueil'));
		}
	}

}
